if (!window.fetch) {
  alert('Your browser is not good enough.');
}

/**
 * Chimpcom external command handler for Cmd.js
 */
export default class Chimpcom {
  constructor() {
    this.options = {
      responder: 'ajax/respond/json',
    };
  }

  /**
   * Handle a command input
   * @param  string cmd_in The command string
   * @param  object cmd    Reference to Cmd instance
   * @return various
   */
  respond(cmd_in, cmd) {
    this.cmd = cmd;

    switch (cmd_in) {
      case 'bash':
        if (!document.getElementById('bash')) {
          let bash_img = document.createElement('img');
          bash_img.setAttribute('src', 'img/bash.png');
          bash_img.setAttribute('id', 'bash');
          bash_img.setAttribute(
            'style',
            'position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);'
          );

          document.getElementsByTagName('body')[0].prepend(bash_img);
        }
        return "Ow! I hope you're going to fix that!";
      case 'alert':
      case 'alarm':
      case 'timer':
        window.open(
          'timer.php?time=' + param,
          'chimpcom_timer',
          'height=90,width=350,left=100,top=100,menubar=no,location=no,scrollbars=no,status=no,toolbar=no,titlebar=no'
        );

        return 'Clock has opened in new window.';
      case 'fix':
        let el = document.getElementById('bash');
        if (el) {
          el.remove();
          return 'Good as new.';
        } else {
          return 'Nothing to fix.';
        }
      default:
        this.ajaxCmd(cmd_in);
        return true;
    }
  }

  /**
   * Send a command to the server
   */
  ajaxCmd(cmd_in, content) {
    const body = {
      _token: document.querySelector('input[name="_token"]').value,
      cmd_in: cmd_in,
    };

    if (content) {
      body.content = content;
    }

    const request = new Request(this.options.responder, {
      method: 'POST',
      body: JSON.stringify(body),
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    });

    return fetch(request)
      .then((response) => response.json())
      .then((data) => {
        this.cmd.handleResponse(data);
      })
      .catch((error) => {
        var cmd_out;

        switch (status) {
          case 'parsererror':
            cmd_out = 'WTF error.';
            break;
          default:
            cmd_out = 'Server error. Try again.';
        }

        this.cmd.handleResponse({
          cmd_out: cmd_out,
        });
      });
  }

  /**
   * Reset server action
   */
  clearAction() {
    return this.ajaxCmd('clearaction');
  }

  saveContent(content) {
    return this.ajaxCmd('', content);
  }
}
