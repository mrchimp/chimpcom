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
    this.speech_synth_support =
      'speechSynthesis' in window && typeof SpeechSynthesisUtterance !== 'undefined';
    this.options = {
      busy_text: 'Communicating...',
      external_processor: function() {},
      history_id: 'cmd_history',
      remote_cmd_list_url: '',
      selector: '#cmd',
      tabcomplete_url: '',
      talk: false,
      unknown_cmd: 'Unrecognised command',
      typewriter_time: 32,
    };
    this.voices = false;
    this.remote_commands = [];
    this.all_commands = [];
    this.local_commands = ['clear', 'clr', 'cls', 'clearhistory', 'shh', 'talk', 'theme'];
    this.themes = ['default', 'light', 'solarized', 'solarized-light'];
    this.theme = 'default';
    this.remote_autocomplete_cache = {};
    this.autocompletion_attempted = false;

    this.options = Object.assign(this.options, user_config);

    if (this.options.remote_cmd_list_url) {
      const request = new Request(this.options.remote_cmd_list_url, {
        method: 'GET',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      fetch(request)
        .then((response) => response.json())
        .then((data) => {
          this.remote_commands = data;
          this.all_commands = Object.assign(this.remote_commands, this.local_commands);
        });
    } else {
      this.all_commands = this.local_commands;
    }

    if (!document.querySelector(this.options.selector)) {
      throw 'Cmd err: Invalid selector.';
    }

    this.cmd_stack = new CmdStack(this.options.history_id, 30);
    window.cmdstack = this.cmd_stack;
    if (this.cmd_stack.isEmpty()) {
      this.cmd_stack.push('secretmagicword!');
    }

    this.cmd_stack.reset();
    this.setupDOM();

    let theme = localStorage.getItem('theme');

    if (theme && this.themes.includes(theme)) {
      this.setTheme(theme);
    }

    this.input.focus();
  }

  // ====== Layout / IO / Alter Interface =========

  /**
   * Create DOM elements, add click & key handlers
   */
  setupDOM() {
    this.wrapper = document.querySelector(this.options.selector);
    this.wrapper.classList.add('cmd-interface');

    this.container = document.createElement('div');
    this.container.classList.add('cmd-container');

    this.wrapper.append(this.container);

    this.clearScreen(); // adds output, input and prompt

    window.addEventListener('resize', this.resizeInput.bind(this));
    this.wrapper.addEventListener('click', this.focusOnInput.bind(this));
    this.wrapper.addEventListener('keydown', this.handleKeyDown.bind(this));
    this.wrapper.addEventListener('keyup', this.handleKeyUp.bind(this));
    this.wrapper.addEventListener('keydown', this.handleKeyPress.bind(this));
  }

  /**
   * Changes the input type
   */
  showInputType(input_type) {
    switch (input_type) {
      case 'password':
        this.input = document.createElement('input');

        this.input.setAttribute('type', 'password');
        this.input.setAttribute('maxlength', 512);
        this.input.classList.add('cmd-in');
        break;
      case 'textarea':
        this.input = document.createElement('textarea');
        this.input.classList.add('cmd-in');
        break;
      default:
        this.input = document.createElement('input');
        this.input.setAttribute('type', 'text');
        this.input.setAttribute('maxlength', 512);
        this.input.classList.add('cmd-in');
    }

    let children = this.container.querySelectorAll('.cmd-in');

    children.forEach((child) => {
      child.remove();
    });

    this.input.setAttribute('title', 'Chimpcom input');
    this.container.append(this.input);

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

    if (this.output.innerHTML.length > 0) {
      this.output.insertAdjacentHTML('beforeend', '<br>');
    }

    this.output.insertAdjacentHTML('beforeend', cmd_out + '<br>');

    if (this.options.talk) {
      this.speakOutput(cmd_out);
    }

    this.cmd_stack.reset();

    this.input.value = '';
    this.input.removeAttribute('disabled');

    this.enableInput();
    this.focusOnInput();
    this.activateAutofills();
  }

  /**
   * Take an input string and output it to the screen
   */
  displayInput(cmd_in) {
    cmd_in = cmd_in
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');

    const prompt = document.createElement('span');
    prompt.classList.add('prompt');
    prompt.appendChild(document.createTextNode(this.prompt_str));

    const input = document.createElement('span');
    input.classList.add('grey_text');
    input.appendChild(document.createTextNode(cmd_in));

    this.output.appendChild(prompt);
    this.output.appendChild(input);
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
   * Set the theme
   * @param {string} theme
   */
  setTheme(theme) {
    localStorage.setItem('theme', theme);
    this.themes.forEach((theme) => {
      this.wrapper.classList.remove('theme-' + theme);
    });
    this.wrapper.classList.add('theme-' + theme);
  }

  // ====== Handlers ==============================

  /**
   * Do something
   */
  handleInput(input_str) {
    var cmd_array = input_str.split(' ');
    var shown_input = input_str;

    if (this.input.getAttribute('type') === 'password') {
      shown_input = new Array(shown_input.length + 1).join('•');
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
      case 'shh':
        if (this.options.talk) {
          window.speechSynthesis.cancel();
          this.options.talk = false;
          this.displayOutput(
            'Speech stopped. Talk mode is still enabled. Type TALK to disable talk mode.'
          );
          this.options.talk = true;
        } else {
          this.displayOutput('Ok.');
        }
        break;
      case 'talk':
        if (!this.speech_synth_support) {
          this.displayOutput("You browser doesn't support speech synthesis.");
          return false;
        }

        this.options.talk = !this.options.talk;
        this.displayOutput(this.options.talk ? 'Talk mode enabled.' : 'Talk mode disabled.');
        break;
      case 'theme':
        if (typeof cmd_array[1] === 'undefined') {
          this.displayOutput(
            'Current theme: ' + this.theme + '.<br>Available themes: ' + this.themes.join(', ')
          );
          return;
        }
        if (!this.themes.includes(cmd_array[1])) {
          this.displayOutput('Invalid theme.');
          return;
        }
        this.setTheme(cmd_array[1]);
        this.displayOutput('Ok.');
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
  handleResponse(response) {
    if (response.redirect !== undefined) {
      document.location.href = response.redirect;
    }

    if (response.openWindow !== undefined) {
      window.open(response.openWindow, '_blank', response.openWindowSpecs);
    }

    if (response.log !== undefined && response.log !== '') {
      console.log(response.log);
    }

    if (response.show_pass === true) {
      this.showInputType('password');
    } else {
      this.showInputType();
    }

    this.displayOutput(response.cmd_out);

    if (response.cmd_fill !== '') {
      let cmd_in = this.wrapper.querySelector('.cmd-container').querySelector('.cmd-in');

      cmd_in.value = response.cmd_fill;
    }
  }

  /**
   * Handle keypresses
   */
  handleKeyPress(e) {
    var keyCode = e.keyCode || e.which;
    var input_str = this.input.value;

    switch (keyCode) {
      case 9: // tab
        this.tabComplete(input_str);
        break;
      case 13: // enter
        if (this.input.getAttribute('disabled')) {
          return false;
        }

        if (e.ctrlKey) {
          this.cmd_stack.push(input_str);
          this.goToURL(input_str);
        } else {
          this.disableInput();

          // push command to stack if using text input, i.e. no passwords
          if (this.input.type === 'text') {
            this.cmd_stack.push(input_str);
          }

          this.handleInput(input_str);
        }
        break;
      case 38: // up arrow
        if (input_str !== '' && this.cmd_stack.cur === this.cmd_stack.getSize() - 1) {
          this.cmd_stack.push(input_str);
        }

        this.input.value = this.cmd_stack.prev();

        break;
      case 40: // down arrow
        this.input.value = this.cmd_stack.next();
        break;
      case 27: // esc
        if (this.container.css('opacity') > 0.5) {
          this.container.animate({ opacity: 0 }, 300);
        } else {
          this.container.animate({ opacity: 1 }, 300);
        }
        break;
    }

    if (keyCode !== 9) {
      this.autocompletion_attempted = false;

      if (this.autocomplete_controller) {
        this.autocomplete_controller.abort();
      }
    }
  }

  /**
   * Prevent default action of special keys
   */
  handleKeyUp(e) {
    if (this.keys_array.includes(e.which)) {
      e.preventDefault();
      return false;
    }

    return true;
  }

  /**
   * Prevent default action of special keys
   */
  handleKeyDown(e) {
    if (this.keys_array.includes(e.which)) {
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
      if (typeof this.remote_autocomplete_cache[str] !== 'undefined') {
        this.input.value = this.remote_autocomplete_cache[str];
        return;
      }

      if (this.options.tabcomplete_url) {
        if (this.autocomplete_controller) {
          this.autocomplete_controller.abort();
        }

        this.autocomplete_controller = new AbortController();

        const params = new URLSearchParams(
          Object.entries({
            cmd_in: str,
          })
        );

        const request = new Request(this.options.tabcomplete_url + '?' + params, {
          method: 'GET',
          signal: this.autocomplete_controller.signal,
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        fetch(request)
          .then((response) => response.json())
          .then((data) => {
            if (data) {
              this.input.value = data;
            }
          })
          .catch((error) => {
            console.error('autocomplete error', error);
          });
      }
      this.autocompletion_attempted = false;
      return;
    }

    const autocompletions = this.all_commands.filter(function(value) {
      return value.startsWith(str);
    });

    if (autocompletions.length === 0) {
      return false;
    } else if (autocompletions.length === 1) {
      this.input.value = autocompletions[0];
    } else {
      if (this.autocompletion_attempted) {
        this.displayOutput(autocompletions.join(', '));
        this.autocompletion_attempted = false;
        this.input.value = str;
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
    this.wrapper.scrollTop = this.wrapper.scrollHeight;
    this.input.focus();
  }

  /**
   * Make prompt and input fit on one line
   */
  resizeInput() {
    var cmd_width =
      this.wrapper.clientWidth - this.wrapper.querySelector('.main-prompt').clientWidth - 45;

    this.input.focus();
    this.input.style.width = cmd_width;
  }

  /**
   * Clear the screen
   */
  clearScreen() {
    this.container.innerHTML = '';

    this.output = document.createElement('div');
    this.output.classList.add('cmd-output');
    this.container.append(this.output);

    this.prompt_elem = document.createElement('span');
    this.prompt_elem.classList.add('main-prompt');
    this.prompt_elem.classList.add('prompt');
    this.prompt_elem.innerHTML = this.prompt_str;
    this.container.append(this.prompt_elem);

    this.input = document.createElement('input');
    this.input.classList.add('cmd-in');
    this.input.setAttribute('type', 'text');
    this.input.setAttribute('maxlength', 512);
    this.container.append(this.input);

    this.showInputType();

    this.input.value = '';
  }

  /**
   * Attach click handlers to 'autofills' - divs which, when clicked,
   * will insert text into the input
   */
  activateAutofills() {
    var input = this.input;

    const autofillers = this.wrapper.querySelectorAll('[data-type=autofill]');

    autofillers.forEach((item) => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        input.value = item.dataset.autofill;
      });
    });
  }

  /**
   * Temporarily disable input while runnign commands
   */
  disableInput() {
    this.input.setAttribute('disabled', true);
    this.input.value = this.options.busy_text;
  }

  /**
   * Reenable input after running disableInput()
   */
  enableInput() {
    this.input.removeAttribute('disabled');
    this.input.value = '';
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
