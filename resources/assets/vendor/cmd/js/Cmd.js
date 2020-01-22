import CmdStack from './CmdStack';

/**
 * HTML5 Command Line Terminal
 *
 * @author   Jake Gully (chimpytk@gmail.com)
 * @license  MIT License
 */
export default class Cmd {
  constructor(user_config) {
    this.keys_array = [9, 13, 38, 40, 27];
    this.style = 'dark';
    this.popup = false;
    this.prompt_str = '$ ';
    this.speech_synth_support = ('speechSynthesis' in window && typeof SpeechSynthesisUtterance !== 'undefined');
    this.options = {
      busy_text: 'Communicating...',
      external_processor: function () { },
      filedrop_enabled: false,
      file_upload_url: 'ajax/uploadfile.php',
      history_id: 'cmd_history',
      remote_cmd_list_url: '',
      selector: '#cmd',
      tabcomplete_url: '',
      talk: false,
      unknown_cmd: 'Unrecognised command',
      typewriter_time: 32
    };
    this.voices = false;
    this.remote_commands = [];
    this.all_commands = [];
    this.local_commands = [
      'clear',
      'clr',
      'cls',
      'clearhistory',
      'invert',
      'shh',
      'talk'
    ];
    this.autocompletion_attempted = false;

    this.options = Object.assign(this.options, user_config);

    if (this.options.remote_cmd_list_url) {
      $.ajax({
        url: this.options.remote_cmd_list_url,
        context: this,
        dataType: 'json',
        method: 'GET',
        success: function (data) {
          this.remote_commands = data;
          this.all_commands = $.merge(this.remote_commands, this.local_commands)
        }
      });
    } else {
      this.all_commands = this.local_commands;
    }

    if (!$(this.options.selector).length) {
      throw 'Cmd err: Invalid selector.';
    }

    this.cmd_stack = new CmdStack(this.options.history_id, 30);

    if (this.cmd_stack.isEmpty()) {
      this.cmd_stack.push('secretmagicword!');
    }

    this.cmd_stack.reset();
    this.setupDOM();
    this.input.focus();
  }

  // ====== Layout / IO / Alter Interface =========

  /**
   * Create DOM elements, add click & key handlers
   */
  setupDOM() {
    this.wrapper = $(this.options.selector).addClass('cmd-interface');

    this.container = $('<div/>')
      .addClass('cmd-container')
      .appendTo(this.wrapper);

    if (this.options.filedrop_enabled) {
      setupFiledrop(); // adds dropzone div
    }

    this.clearScreen(); // adds output, input and prompt

    $(this.options.selector).on('click', $.proxy(this.focusOnInput, this));
    $(window).resize($.proxy(this.resizeInput, this));

    this.wrapper.keydown($.proxy(this.handleKeyDown, this));
    this.wrapper.keyup($.proxy(this.handleKeyUp, this));
    this.wrapper.keydown($.proxy(this.handleKeyPress, this));
  }

  /**
   * Changes the input type
   */
  showInputType(input_type) {
    switch (input_type) {
      case 'password':
        this.input = $('<input/>')
          .attr('type', 'password')
          .attr('maxlength', 512)
          .addClass('cmd-in');
        break;
      case 'textarea':
        this.input = $('<textarea/>')
          .addClass('cmd-in')
        break;
      default:
        this.input = $('<input/>')
          .attr('type', 'text')
          .attr('maxlength', 512)
          .addClass('cmd-in');
    }

    this.container.children('.cmd-in').remove();

    this.input.appendTo(this.container)
      .attr('title', 'Chimpcom input');

    this.focusOnInput();
  }

  /**
   * Takes the client's input and the server's output
   * and displays them appropriately.
   *
   * @param   string  cmd_in      The command as entered by the user
   * @param   string  cmd_out     The server output to write to screen
   */
  displayOutput(cmd_out) {
    if (typeof cmd_out !== 'string') {
      cmd_out = 'Error: invalid cmd_out returned.';
    }

    if (this.output.html()) {
      this.output.append('<br>');
    }

    this.output.append(cmd_out + '<br>');

    if (this.options.talk) {
      this.speakOutput(cmd_out);
    }

    this.cmd_stack.reset();

    this.input.val('').removeAttr('disabled');

    this.enableInput();
    this.focusOnInput();
    this.activateAutofills();
  }

  /**
   * Take an input string and output it to the screen
   */
  displayInput(cmd_in) {
    cmd_in = cmd_in.replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");

    this.output.append('<span class="prompt">' + this.prompt_str + '</span> ' +
      '<span class="grey_text">' + cmd_in + '</span>');
  }

  /**
   * Set the prompt string
   * @param {string} new_prompt The new prompt string
   */
  setPrompt(new_prompt) {
    if (typeof new_prompt !== 'string') {
      throw 'Cmd error: invalid prompt string.';
    }

    this.prompt_str = new_prompt;
    this.prompt_elem.html(this.prompt_str);
  }

  /**
   * Post-file-drop dropzone reset
   */
  resetDropzone() {
    dropzone.css('display', 'none');
  }

  /**
   * Add file drop handlers
   */
  setupFiledrop() {
    this.dropzone = $('<div/>')
      .addClass('dropzone')
      .appendTo(wrapper)
      .filedrop({
        url: this.options.file_upload_url,
        paramname: 'dropfile', // POST parameter name used on serverside to reference file
        maxfiles: 10,
        maxfilesize: 2, // MBs
        error: function (err, file) {
          switch (err) {
            case 'BrowserNotSupported':
              alert('Your browser does not support html5 drag and drop.');
              break;
            case 'TooManyFiles':
              this.displayInput('[File Upload]');
              this.displayOutput('Too many files!');
              this.resetDropzone();
              break;
            case 'FileTooLarge':
              // FileTooLarge also has access to the file which was too large
              // use file.name to reference the filename of the culprit file
              this.displayInput('[File Upload]');
              this.displayOutput('File too big!');
              this.resetDropzone();
              break;
            default:
              this.displayInput('[File Upload]');
              this.displayOutput('Fail D:');
              this.resetDropzone();
              break;
          }
        },
        dragOver: function () { // user dragging files over #dropzone
          this.dropzone.css('display', 'block');
        },
        dragLeave: function () { // user dragging files out of #dropzone
          this.resetDropzone();
        },
        docOver: function () { // user dragging files anywhere inside the browser document window
          this.dropzone.css('display', 'block');
        },
        docLeave: function () { // user dragging files out of the browser document window
          this.resetDropzone();
        },
        drop: function () { // user drops file
          this.dropzone.append('<br>File dropped.');
        },
        uploadStarted: function (i, file, len) {
          this.dropzone.append('<br>Upload started...');
          // a file began uploading
          // i = index => 0, 1, 2, 3, 4 etc
          // file is the actual file of the index
          // len = total files user dropped
        },
        uploadFinished: function (i, file, response, time) {
          // response is the data you got back from server in JSON format.
          if (response.error !== '') {
            upload_error = response.error;
          }
          this.dropzone.append('<br>Upload finished! ' + response.result);
        },
        progressUpdated: function (i, file, progress) {
          // this function is used for large files and updates intermittently
          // progress is the integer value of file being uploaded percentage to completion
          this.dropzone.append('<br>File uploading...');
        },
        speedUpdated: function (i, file, speed) { // speed in kb/s
          this.dropzone.append('<br>Upload speed: ' + speed);
        },
        afterAll: function () {
          // runs after all files have been uploaded or otherwise dealt with
          if (upload_error !== '') {
            this.displayInput('[File Upload]');
            this.displayOutput('Error: ' + upload_error);
          } else {
            this.displayInput('[File Upload]');
            this.displayOutput('[File Upload]', 'Success!');
          }

          upload_error = '';

          this.dropzone.css('display', 'none');
          this.resetDropzone();
        }
      });
  }

  /**
   * [invert description]
   * @return {[type]} [description]
   */
  invert() {
    this.wrapper.toggleClass('inverted');
  }



  // ====== Handlers ==============================

  /**
   * Do something
   */
  handleInput(input_str) {
    var cmd_array = input_str.split(' ');
    var shown_input = input_str;

    if (this.input.attr('type') === 'password') {
      shown_input = new Array(shown_input.length + 1).join("•");
    }

    this.displayInput(shown_input);

    switch (cmd_array[0]) {
      case '':
        this.displayOutput('');
        break;
      case 'clear':
      case 'cls':
      case 'clr':
        this.clearScreen();
        break;
      case 'clearhistory':
        this.cmd_stack.empty();
        this.cmd_stack.reset();
        this.displayOutput('Command history cleared. ');
        break;
      case 'invert':
        this.invert();
        this.displayOutput('Shazam.');
        break;
      case 'shh':
        if (this.options.talk) {
          window.speechSynthesis.cancel();
          this.options.talk = false;
          this.displayOutput('Speech stopped. Talk mode is still enabled. Type TALK to disable talk mode.');
          this.options.talk = true;
        } else {
          this.displayOutput('Ok.');
        }
        break;
      case 'talk':
        if (!this.speech_synth_support) {
          this.displayOutput('You browser doesn\'t support speech synthesis.');
          return false;
        }

        this.options.talk = !this.options.talk;
        this.displayOutput((this.options.talk ? 'Talk mode enabled.' : 'Talk mode disabled.'));
        break;
      default:
        if (typeof this.options.external_processor !== 'function') {
          this.displayOutput(this.options.unknown_cmd);
          return false;
        }

        var result = this.options.external_processor(input_str, this);

        switch (typeof result) {
          // If undefined, external handler should
          // call handleResponse when done
          case 'boolean':
            if (!result) {
              this.displayOutput(this.options.unknown_cmd);
            }
            break;
          // If we get a response object, deal with it directly
          case 'object':
            this.handleResponse(result);
            break;
          // If we have a string, output it. This shouldn't
          // really happen but it might be useful
          case 'string':
            this.displayOutput(result);
            break;
          default:
            this.displayOutput(this.options.unknown_cmd);
        }
    }
  }

  /**
   * Handle JSON responses. Used as callback by external command handler
   * @param  {object} res Chimpcom command object
   */
  handleResponse(res) {
    if (res.redirect !== undefined) {
      document.location.href = res.redirect;
    }

    if (res.openWindow !== undefined) {
      window.open(res.openWindow, '_blank', res.openWindowSpecs);
    }

    if (res.log !== undefined && res.log !== '') {
      console.log(res.log);
    }

    if (res.show_pass === true) {
      this.showInputType('password');
    } else {
      this.showInputType();
    }

    this.displayOutput(res.cmd_out);

    if (res.cmd_fill !== '') {
      this.wrapper.children('.cmd-container').children('.cmd-in').first().val(res.cmd_fill);
    }
  }

  /**
   * Handle keypresses
   */
  handleKeyPress(e) {
    var keyCode = e.keyCode || e.which,
      input_str = this.input.val(),
      autocompletions;

    if (keyCode === 9) { //tab
      this.tabComplete(input_str);
    } else {
      this.autocompletion_attempted = false;
      if (this.autocomplete_ajax) {
        this.autocomplete_ajax.abort();
      }

      if (keyCode === 13) { // enter
        if (this.input.attr('disabled')) {
          return false;
        }

        if (e.ctrlKey) {
          this.cmd_stack.push(input_str);
          this.goToURL(input_str);
        } else {
          this.disableInput();

          // push command to stack if using text input, i.e. no passwords
          if (this.input.get(0).type === 'text') {
            this.cmd_stack.push(input_str);
          }

          this.handleInput(input_str);
        }
      } else if (keyCode === 38) { // up arrow
        if (input_str !== "" && this.cmd_stack.cur === (this.cmd_stack.getSize() - 1)) {
          this.cmd_stack.push(input_str);
        }

        this.input.val(this.cmd_stack.prev());
      } else if (keyCode === 40) { // down arrow
        this.input.val(this.cmd_stack.next());
      } else if (keyCode === 27) { // esc
        if (this.container.css('opacity') > 0.5) {
          this.container.animate({ 'opacity': 0 }, 300);
        } else {
          this.container.animate({ 'opacity': 1 }, 300);
        }
      }
    }
  }

  /**
   * Prevent default action of special keys
   */
  handleKeyUp(e) {
    var key = e.which;

    if ($.inArray(key, this.keys_array) > -1) {
      e.preventDefault();
      return false;
    }

    return true;
  }

  /**
   * Prevent default action of special keys
   */
  handleKeyDown(e) {
    var key = e.which;

    if ($.inArray(key, this.keys_array) > -1) {
      e.preventDefault();

      return false;
    }
    return true;
  }

  /**
   * Complete command names when tab is pressed
   */
  tabComplete(str) {
    // If we have a space then offload to external processor
    if (str.indexOf(' ') !== -1) {
      if (this.options.tabcomplete_url) {
        if (this.autocomplete_ajax) {
          this.autocomplete_ajax.abort();
        }

        this.autocomplete_ajax = $.ajax({
          url: this.options.tabcomplete_url,
          context: this,
          dataType: 'json',
          data: {
            cmd_in: str
          },
          success: function (data) {
            if (data) {
              this.input.val(data);
            }
          }
        });
      }
      this.autocompletion_attempted = false;
      return;
    }

    var autocompletions = this.all_commands.filter(function (value) {
      return value.startsWith(str);
    });


    if (autocompletions.length === 0) {
      return false;
    } else if (autocompletions.length === 1) {
      this.input.val(autocompletions[0]);
    } else {
      if (this.autocompletion_attempted) {
        this.displayOutput(autocompletions.join(', '));
        this.autocompletion_attempted = false;
        this.input.val(str);
        return;
      } else {
        this.autocompletion_attempted = true;
      }
    }
  }


  // ====== Helpers ===============================

  /**
   * Takes a user to a given url. Adds "http://" if necessary.
   */
  goToURL(url) {
    if (url.substr(0, 4) !== 'http' && url.substr(0, 2) !== '//') {
      url = 'http://' + url;
    }

    if (popup) {
      window.open(url, '_blank');
      window.focus();
    } else {
      // break out of iframe - used by chrome plugin
      if (top.location !== location) {
        top.location.href = document.location.href;
      }

      location.href = url;
    }
  }

  /**
   * Give focus to the command input and
   * scroll to the bottom of the page
   */
  focusOnInput() {
    var cmd_width;

    $(this.options.selector).scrollTop($(this.options.selector)[0].scrollHeight);

    this.input.focus();
  }

  /**
   * Make prompt and input fit on one line
   */
  resizeInput() {
    var cmd_width = this.wrapper.width() - this.wrapper.find('.main-prompt').first().width() - 45;

    this.input.focus().css('width', cmd_width);
  }

  /**
   * Clear the screen
   */
  clearScreen() {
    this.container.empty();

    this.output = $('<div/>')
      .addClass('cmd-output')
      .appendTo(this.container);

    this.prompt_elem = $('<span/>')
      .addClass('main-prompt')
      .addClass('prompt')
      .html(this.prompt_str)
      .appendTo(this.container);

    this.input = $('<input/>')
      .addClass('cmd-in')
      .attr('type', 'text')
      .attr('maxlength', 512)
      .appendTo(this.container);

    this.showInputType();

    this.input.val('');
  }

  /**
   * Attach click handlers to 'autofills' - divs which, when clicked,
   * will insert text into the input
   */
  activateAutofills() {
    var input = this.input;

    this.wrapper
      .find('[data-type=autofill]')
      .on('click', function () {
        input.val($(this).data('autofill'));
      });
  }

  /**
   * Temporarily disable input while runnign commands
   */
  disableInput() {
    this.input
      .attr('disabled', true)
      .val(this.options.busy_text);
  }

  /**
   * Reenable input after running disableInput()
   */
  enableInput() {
    this.input
      .removeAttr('disabled')
      .val('');
  }

  /**
   * Speak output aloud using speech synthesis API
   *
   * @param {String} output Text to read
   */
  speakOutput(output) {
    var msg = new SpeechSynthesisUtterance();

    msg.volume = 1; // 0 - 1
    msg.rate = 1; // 0.1 - 10
    msg.pitch = 2; // 0 - 2
    msg.lang = 'en-UK';
    msg.text = output;

    window.speechSynthesis.speak(msg);
  }
}