import CmdStack from './CmdStack';

interface ExternalProcessor {
  (input_str: string, context: Cmd): CmdResponse
};

interface CancelEditHandler {
  (): Promise<string>
};

interface SaveEditHandler {
  (content: string, continue_editing: boolean): Promise<string>;
};

interface Options {
  busy_text?: string;
  cancel_edit_handler?: CancelEditHandler | null,
  endpoint?: string,
  external_processor?: ExternalProcessor | null;
  history_id?: string,
  remote_cmd_list_url?: string,
  save_edit_handler?: SaveEditHandler | null,
  selector?: string,
  tabcomplete_url?: string,
  talk?: boolean,
  unknown_cmd?: string,
  volume?: number,
};

interface CmdResponse {
  cmd_fill?: string,
  cmd_out: string,
  edit_content?: string,
  log?: string,
  openWindow?: string,
  openWindowSpecs?: string,
  redirect?: string,
  show_pass?: boolean,
  user?: CmdResonseUser,
};

interface CmdResonseUser {
  name: string,
};

/**
 * HTML5 Command Line Terminal
 *
 * @author   Jake Gully (chimpytk@gmail.com)
 * @license  MIT License
 */
export default class Cmd {
  all_commands: string[];
  autocomplete_attempted: boolean;
  autocomplete_controller: AbortController;
  bash_el?: HTMLElement;
  history: CmdStack;
  container: HTMLElement;
  edit_content: string;
  edit_mode: boolean;
  editor_wrapper_el: HTMLElement;
  editor_el: HTMLTextAreaElement;
  input_caret_el: HTMLElement;
  input_container_el: HTMLElement;
  input_el: HTMLInputElement | HTMLTextAreaElement;
  input_wrapper_el: HTMLElement;
  loading_el: HTMLElement;
  local_cmds: string[];
  prompt_str: string;
  options: Options;
  output_el: HTMLElement;
  prompt_el: HTMLElement;
  remote_cmds: string[];
  tts_supported: boolean;
  tab_completions: string[];
  tab_index: number;
  tab_mode: boolean;
  theme: string;
  themes: string[];
  username: string;
  wrapper_el: HTMLElement;

  constructor(user_config: Options = {}) {
    this.prompt_str = '%USERNAME% $';
    this.tts_supported =
      'speechSynthesis' in window && typeof SpeechSynthesisUtterance !== 'undefined';
    this.options = Object.assign(
      {
        busy_text: 'Processing... ',
        external_processor: this.respond.bind(this),
        history_id: 'cmd_history',
        remote_cmd_list_url: 'ajax/commands',
        selector: '#cmd',
        tabcomplete_url: 'ajax/tabcomplete',
        talk: false,
        unknown_cmd: 'Unrecognised command',
        volume: 1,
        cancel_edit_handler: this.clearAction.bind(this),
        save_edit_handler: this.saveContent.bind(this),
        endpoint: 'ajax/respond/json',
      },
      user_config
    );
    this.remote_cmds = [];
    this.all_commands = [];
    this.local_cmds = [
      'alarm',
      'alert',
      'bash',
      'clear',
      'clr',
      'cls',
      'clearhistory',
      'fix',
      'shh',
      'talk',
      'theme',
      'timer',
      'volume',
    ];
    this.themes = ['default', 'light', 'solarized', 'solarized-light'];
    this.theme = 'default';
    this.autocomplete_attempted = false;
    this.tab_mode = false;
    this.tab_index = 0;
    this.tab_completions = [];
    this.username = 'guest';
    this.edit_mode = false;
    this.edit_content = null;

    if (this.options.remote_cmd_list_url) {
      this.loadRemoteCmdList();
    } else {
      this.all_commands = this.local_cmds;
    }

    if (!document.querySelector(this.options.selector)) {
      throw 'Cmd err: Invalid selector.';
    }

    this.history = new CmdStack(this.options.history_id, 30);
    this.history.reset();
    this.setupDOM();
    this.setTheme(localStorage.getItem('theme'));
    this.input_el.focus();
  }

  loadRemoteCmdList() {
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
        this.remote_cmds = data;
        this.all_commands = Object.assign(this.remote_cmds, this.local_cmds);
      });
  }

  /**
   * Create DOM elements, add click & key handlers
   */
  setupDOM() {
    this.wrapper_el = document.querySelector(this.options.selector);
    this.wrapper_el.classList.add('cmd-interface');

    this.container = document.createElement('div');
    this.container.classList.add('cmd-container');

    this.wrapper_el.append(this.container);

    this.clear(); // adds output, input and prompt

    this.wrapper_el.addEventListener('click', (e) => {
      if (window.getSelection().type !== 'Range') {
        this.focusOnInput();
      }
    });

    this.container.addEventListener('keydown', this.handleKeyPress.bind(this));
    this.container.addEventListener('keyup', this.updateCaret.bind(this));
    this.container.addEventListener('select', this.updateCaret.bind(this));
    this.container.addEventListener('click', this.updateCaret.bind(this));

    this.initTextEditor();
  }

  initTextEditor() {
    if (!this.editor_wrapper_el) {
      this.editor_wrapper_el = document.createElement('div');
      this.editor_wrapper_el.classList.add('cmd-editor');

      this.editor_el = document.createElement('textarea');
      this.editor_el.classList.add('cmd-editor-content');

      this.editor_wrapper_el.appendChild(this.editor_el);
      this.wrapper_el.appendChild(this.editor_wrapper_el);

      const editor_actions_el = document.createElement('div');
      editor_actions_el.classList.add('cmd-editor-actions');
      editor_actions_el.innerText = 'escape=cancel shift+enter=save ctrl+shift+enter=save';
      this.editor_wrapper_el.appendChild(editor_actions_el);
      this.editor_wrapper_el.addEventListener('click', (e) => {
        e.stopImmediatePropagation();
      });
      this.editor_el.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          e.preventDefault();
          e.stopPropagation();
          this.cancelEdit();
          return;
        }

        if (e.key === 'Enter' && e.shiftKey) {
          e.preventDefault();
          e.stopPropagation();
          this.saveEdit(!e.ctrlKey);
          return;
        }
      });
    }
  }

  startEdit(content: string) {
    this.edit_mode = true;
    this.editor_wrapper_el.classList.add('is-active');
    this.editor_el.innerHTML = content;
  }

  cancelEdit() {
    this.options
      .cancel_edit_handler()
      .then(() => {
        this.edit_mode = false;
        this.editor_el.innerHTML = '';
        this.editor_wrapper_el.classList.remove('is-active');
        this.focusOnInput();
      })
      .catch((e) => {
        alert(e);
      });
  }

  saveEdit(continue_editing = true) {
    if (!continue_editing) {
      this.output('Saving...');
    }

    this.editor_el.disabled = false;

    this.options
      .save_edit_handler(this.editor_el.value, continue_editing)
      .then(() => {
        this.editor_el.disabled = false;

        if (!continue_editing) {
          this.edit_mode = false;
          this.editor_el.innerHTML = '';
          this.editor_wrapper_el.classList.remove('is-active');
          this.focusOnInput();
        }
      })
      .catch((e) => {
        alert('Save failed.');
      });
  }

  /**
   * Changes the input type
   */
  showInputType(input_type?: string) {
    this.input_el.remove();

    switch (input_type) {
      case 'password':
        this.input_el = document.createElement('input');
        this.input_el.setAttribute('type', 'password');
        this.input_el.setAttribute('maxlength', '512');
        break;
      case 'textarea':
        this.input_el = document.createElement('textarea');
        break;
      default:
        this.input_el = document.createElement('input');
        this.input_el.setAttribute('type', 'text');
        this.input_el.setAttribute('maxlength', '512');
    }

    this.input_el.setAttribute('autocapitalize', 'off');
    this.input_el.classList.add('cmd-in');
    this.input_el.setAttribute('title', 'Cmd input');

    this.input_wrapper_el.append(this.input_el);

    this.focusOnInput();
  }

  /**
   * Take command output and display it appropriately.
   *
   * @param  string  cmd_out  The server output to write to screen
   */
  output(cmd_out: string) {
    if (this.output_el.innerHTML.length > 0) {
      this.output_el.insertAdjacentHTML('beforeend', '<br>');
    }

    this.output_el.insertAdjacentHTML('beforeend', cmd_out + '<br>');

    if (this.options.talk) {
      this.speakOutput(cmd_out);
    }

    this.history.reset();

    this.enableInput();
    this.focusOnInput();
    this.scrollToBottom();
    this.activateAutofills();
  }

  /**
   * Take an input string and output it to the screen
   */
  displayInput(cmd_in: string) {
    const prompt = document.createElement('span');
    prompt.classList.add('prompt');
    prompt.appendChild(document.createTextNode(this.makePromptStr()));

    const input = document.createElement('span');
    input.classList.add('grey_text');
    input.appendChild(document.createTextNode(cmd_in));

    this.output_el.appendChild(prompt);
    this.output_el.appendChild(input);

    this.prompt_el.innerText = this.makePromptStr();
  }

  /**
   * Make the prompt string
   */
  makePromptStr() {
    return this.prompt_str.replace('%USERNAME%', this.username);
  }

  /**
   * Set the theme
   * @param {string} theme
   */
  setTheme(theme: string) {
    if (!theme || !this.themes.includes(theme)) {
      theme = 'default';
    }

    localStorage.setItem('theme', theme);
    this.themes.forEach((theme) => {
      this.wrapper_el.classList.remove('theme-' + theme);
    });
    this.wrapper_el.classList.add('theme-' + theme);
  }

  /**
   * Do something
   */
  handleInput(input_str: string) {
    var cmd_array = input_str.split(' ');
    var shown_input = input_str;

    if (this.input_el.getAttribute('type') === 'password') {
      shown_input = new Array(shown_input.length + 1).join('•');
    }

    this.displayInput(shown_input);

    switch (cmd_array[0]) {
      case '':
        this.output('');
        break;
      case 'clear':
      case 'clr':
      case 'cls':
        this.clear();
        break;
      case 'clearhistory':
        this.history.empty();
        this.history.reset();
        this.output('Command history cleared. ');
        break;
      case 'shh':
        if (this.options.talk) {
          window.speechSynthesis.cancel();
          this.options.talk = false;
          this.output(
            'Speech stopped. Talk mode is still enabled. Type TALK to disable talk mode.'
          );
          this.options.talk = true;
        } else {
          this.output('Ok.');
        }
        break;
      case 'talk':
        if (!this.tts_supported) {
          this.output("You browser doesn't support speech synthesis.");
          return false;
        }

        this.options.talk = !this.options.talk;
        this.output(
          this.options.talk
            ? 'Talk mode enabled. Type "shh" to silence the voice. Type "talk" again to turn talk mode off.'
            : 'Talk mode disabled.'
        );
        break;
      case 'theme':
        if (typeof cmd_array[1] === 'undefined') {
          this.output(
            'Current theme: ' + this.theme + '.<br>Available themes: ' + this.themes.join(', ')
          );
          return;
        }
        if (!this.themes.includes(cmd_array[1])) {
          this.output('Invalid theme.');
          return;
        }
        this.setTheme(cmd_array[1]);
        this.output('Ok.');
        break;
      case 'volume':
        let vol = parseFloat(cmd_array[1]);
        vol = Math.min(vol, 1);
        vol = Math.max(vol, 0);
        this.options.volume = vol;
        this.output('Volume set to ' + this.options.volume);
        break;
      case 'bash':
        if (!document.getElementById('bash')) {
          this.bash_el = document.createElement('img');
          this.bash_el.setAttribute('src', 'img/bash.png');
          this.bash_el.setAttribute('id', 'bash');
          this.bash_el.setAttribute(
            'style',
            'position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);'
          );

          document.getElementsByTagName('body')[0].prepend(this.bash_el);
        }
        return "Ow! I hope you're going to fix that!";
      case 'alert':
      case 'alarm':
      case 'timer':
        window.open(
          'timer.php?time=60', // @todo Make changeable!
          'chimpcom_timer',
          'height=90,width=350,left=100,top=100,menubar=no,location=no,scrollbars=no,status=no,toolbar=no,titlebar=no'
        );

        return 'Clock has opened in new window.';
      case 'fix':
        if (this.bash_el) {
          this.bash_el.remove();
          this.bash_el = null;
          return 'Good as new.';
        } else {
          return 'Nothing to fix.';
        }
      default:
        if (typeof this.options.external_processor !== 'function') {
          this.output(this.options.unknown_cmd);
          return false;
        }

        var result = this.options.external_processor(input_str, this);

        switch (typeof result) {
          // If undefined, external handler should
          // call handleResponse when done
          case 'boolean':
            if (!result) {
              this.output(this.options.unknown_cmd);
            }
            break;
          // If we get a response object, deal with it directly
          case 'object':
            this.handleExternalResponse(result);
            break;
          // If we have a string, output it. This shouldn't
          // really happen but it might be useful
          case 'string':
            this.output(result);
            break;
          default:
            this.output(this.options.unknown_cmd);
        }
    }
  }

  /**
   * Handle JSON responses. Used as callback by external command handler
   * @param  {object} res Cmd command object
   */
  handleExternalResponse(response: CmdResponse) {
    if (!!response.redirect) {
      document.location.href = response.redirect;
    }

    if (!!response.openWindow) {
      window.open(response.openWindow, '_blank', response.openWindowSpecs);
    }

    if (!!response.log) {
      console.log(response.log);
    }

    if (!!response.show_pass) {
      this.showInputType('password');
    } else {
      this.showInputType();
    }

    if (typeof response.edit_content === 'string') {
      this.startEdit(response.edit_content);
    }

    this.output(response.cmd_out);

    if (response.user && response.user.name) {
      this.username = response.user.name;
      this.prompt_el.innerText = this.makePromptStr();
    }

    if (!!response.cmd_fill) {
      this.input_el.value = response.cmd_fill;
    }
  }

  /**
   * Handle keypresses
   */
  handleKeyPress(e: KeyboardEvent) {
    var input_str = this.input_el.value;

    switch (e.key) {
      case 'Tab':
        // For accessability reasons, don't tabcomplete when input is empty
        if (this.input_el.value === '') {
          return;
        }

        e.preventDefault();
        this.tabComplete(input_str);
        break;
      case 'Enter':
        e.preventDefault();

        if (this.input_el.getAttribute('disabled')) {
          return false;
        }

        if (e.ctrlKey) {
          this.history.push(input_str);
          this.goToURL(input_str);
        } else {
          this.disableInput();

          // push command to stack if using text input, i.e. no passwords
          if (this.input_el.type === 'text') {
            this.history.push(input_str);
          }

          this.handleInput(input_str);
        }
        break;
      case 'ArrowUp':
        e.preventDefault();

        if (input_str !== '' && this.history.cur === this.history.getSize() - 1) {
          this.history.push(input_str);
        }

        this.input_el.value = this.history.prev();

        break;
      case 'ArrowDown':
        e.preventDefault();

        this.input_el.value = this.history.next();
        break;
      case 'Escape':
        e.preventDefault();

        if (parseFloat(this.container.style.opacity) > 0.5) {
          this.container.style.opacity = '0';
        } else {
          this.container.style.opacity = '1';
        }
        break;
    }

    if (e.key !== 'Tab') {
      this.autocomplete_attempted = false;
      this.tab_mode = false;

      if (this.autocomplete_controller) {
        this.autocomplete_controller.abort();
      }
    }

    this.updateCaret();
  }

  updateCaret() {
    const startPos = this.input_el.selectionStart;
    const endPos = this.input_el.selectionEnd;

    let width = Math.max(Math.abs(endPos - startPos), 1);
    let str = this.input_el.value.substr(startPos, width);

    this.input_caret_el.innerText = str;
    this.input_caret_el.style.setProperty('--caret-offset', startPos + 'ch');
    this.input_caret_el.style.setProperty('--caret-width', width + 'ch');
  }

  /**
   * Complete command names when tab is pressed
   */
  tabComplete(str: string) {
    // If we have a space then offload to external processor
    if (str.indexOf(' ') !== -1) {
      if (this.tab_mode && this.tab_completions.length !== 0) {
        this.tab_index++;

        if (this.tab_index > this.tab_completions.length - 1) {
          this.tab_index = 0;
        }

        this.input_el.value = this.tab_completions[this.tab_index];

        return;
      }

      if (this.options.tabcomplete_url) {
        this.resetTabInput();
        this.tab_mode = true;

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
            if (!data) {
              this.resetTabInput();
              return;
            }

            if (data.length === 0) {
              this.resetTabInput();
            } else if (data.length === 1) {
              this.resetTabInput();
              this.input_el.value = data[0];
            } else {
              if (!Array.isArray(data)) {
                throw 'Tab complete is not array.';
              }

              this.tab_completions = data;
              this.input_el.value = data[0];
            }
          })
          .catch((error) => {
            console.error('autocomplete error', error);
          });
      }
      this.autocomplete_attempted = false;
      return;
    }

    const autocompletions = this.all_commands.filter(function(value) {
      return value.startsWith(str);
    });

    if (autocompletions.length === 0) {
      return false;
    } else if (autocompletions.length === 1) {
      this.input_el.value = autocompletions[0];
    } else {
      if (this.autocomplete_attempted) {
        this.output(autocompletions.join(', '));
        this.autocomplete_attempted = false;
        this.input_el.value = str;
        return;
      } else {
        this.autocomplete_attempted = true;
      }
    }
  }

  resetTabInput() {
    this.tab_mode = false;
    this.tab_index = 0;
    this.tab_completions = [];
  }

  /**
   * Takes a user to a given url. Adds "http://" if necessary.
   */
  goToURL(url: string) {
    if (url.substr(0, 4) !== 'http' && url.substr(0, 2) !== '//') {
      url = 'http://' + url;
    }

    location.href = url;
  }

  /**
   * Give focus to the command input and
   * scroll to the bottom of the page
   */
  focusOnInput() {
    if (this.edit_mode) {
      this.editor_el.focus();
      return;
    }

    this.input_el.focus();
    this.updateCaret();
  }

  /**
   * Scroll the output to the bottom
   */
  scrollToBottom() {
    this.wrapper_el.scrollTop = this.wrapper_el.scrollHeight;
  }

  /**
   * Clear the screen
   */
  clear() {
    // @todo this is duplicating setupDOM
    this.container.innerHTML = '';

    this.output_el = document.createElement('div');
    this.output_el.classList.add('cmd-output');
    this.container.append(this.output_el);

    this.input_container_el = document.createElement('div');
    this.input_container_el.classList.add('cmd-input-container');
    this.container.append(this.input_container_el);

    this.prompt_el = document.createElement('span');
    this.prompt_el.classList.add('main-prompt');
    this.prompt_el.classList.add('prompt');
    this.prompt_el.innerHTML = this.makePromptStr();
    this.input_container_el.append(this.prompt_el);

    this.input_wrapper_el = document.createElement('div');
    this.input_wrapper_el.classList.add('cmd-input-wrapper');
    this.input_container_el.append(this.input_wrapper_el);

    this.input_el = document.createElement('input');
    this.input_el.classList.add('cmd-in');
    this.input_el.setAttribute('type', 'text');
    this.input_el.setAttribute('maxlength', '512');
    this.input_wrapper_el.append(this.input_el);

    this.input_caret_el = document.createElement('div');
    this.input_caret_el.classList.add('cmd-in-caret');
    this.input_wrapper_el.append(this.input_caret_el);

    this.showInputType();

    this.input_el.value = '';
  }

  /**
   * Attach click handlers to 'autofills' - divs which, when clicked,
   * will insert text into the input
   */
  activateAutofills() {
    const autofillers = this.wrapper_el.querySelectorAll('[data-type=autofill]');

    autofillers.forEach((item) => {
      if (item instanceof HTMLElement) {
        item.addEventListener('click', (e) => {
          e.preventDefault();
          this.input_el.value = item.dataset.autofill;
        });
      }
    });
  }

  /**
   * Temporarily disable input while runnign commands
   */
  disableInput() {
    this.input_el.setAttribute('disabled', 'true');
    this.input_el.value = '';
    this.input_el.style.display = 'none';

    this.input_caret_el.style.display = 'none';

    this.loading_el = document.createElement('div');
    this.loading_el.innerText = this.options.busy_text;
    this.loading_el.classList.add('loading-spinner');

    this.input_wrapper_el.append(this.loading_el);
  }

  /**
   * Reenable input after running disableInput()
   */
  enableInput() {
    if (this.loading_el) {
      this.loading_el.remove();
    }

    this.input_el.removeAttribute('disabled');
    this.input_el.style.display = 'block';

    this.input_caret_el.style.display = 'block';
  }

  /**
   * Speak output aloud using speech synthesis API
   *
   * @param {String} output Text to read
   */
  speakOutput(output: string) {
    var msg = new SpeechSynthesisUtterance();

    var el = new DOMParser().parseFromString(output, 'text/html');
    output = el.body.textContent || '';

    msg.volume = this.options.volume; // 0 - 1
    msg.rate = 1; // 0.1 - 10
    msg.pitch = 2; // 0 - 2
    msg.lang = 'en-UK';
    msg.text = output;

    window.speechSynthesis.speak(msg);
  }

  /**
   * Send a command to the server
   *
   * @todo make async
   */
  ajaxCmd(cmd_in: string, options?: object) {
    const request = new Request(this.options.endpoint, {
      method: 'POST',
      body: JSON.stringify(
        Object.assign(
          {
            _token: (<HTMLInputElement>document.querySelector('input[name="_token"]')).value,
            cmd_in: cmd_in,
          },
          options
        )
      ),
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    });

    return fetch(request)
      .then((response) => {
        if (!([200, 404].includes(response.status))) {
          throw 'Invalid response';
        }
        return response;
      })
      .then((response) => response.json())
      .then((data) => {
        this.handleExternalResponse(data);
      })
      .catch((e) => {
        console.error(e)
        this.handleExternalResponse({
          cmd_out: 'Server error. Try again.',
        });
      });
  }

  /**
   * Handle a command input
   * @param  string cmd_in The command string
   * @return various
   */
  respond(cmd_in: string) {
    this.ajaxCmd(cmd_in);
    return true;
  }

  /**
   * Reset server action
   */
  clearAction() {
    return this.ajaxCmd('clearaction');
  }

  /**
   * Save some editable content
   */
  saveContent(content: string, continue_editing: boolean) {
    return this.ajaxCmd(continue_editing ? '--continue' : '', {
      content: content,
    });
  }
}
