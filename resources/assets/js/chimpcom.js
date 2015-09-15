/**
* Chimpcom external command handler for Cmd.js
*/
var Chimpcom = {

  options: {
    responder: '/ajax/respond/json',
    timeout_length: 2000
  },

  /**
   * Get a response
   * @param  string cmd_in The command string
   * @param  object cmd    Reference to Cmd instance
   * @return various
   */
  respond: function(cmd_in, cmd) {
    switch (cmd_in) {
      case 'bash':
        if (!$('#bash').length) {
          $('body').prepend('<img src="assets/img/bash.png" id="bash" style="position:fixed;">');
        }
        displayOutput('bash', 'Ow! I hope you\'re going to fix that!');
        break;
      case 'alert':
      case 'alarm':
      case 'timer':
        window.open('timer.php?time=' + param,
          'chimpcom_timer',
          'height=90,width=350,left=100,top=100,menubar=no,location=no,scrollbars=no,status=no,toolbar=no,titlebar=no');

        displayOutput(cmd_in, 'Clock has opened in new window.');
        break;
      case 'fix':
        $('#bash').remove();
        displayOutput('fix', 'Good as new.');
        break;
      case 'pwd':
        displayOutput('pwd', document.location.href);
        break;
      case 'popup':
      case 'detach':
        openChimpcomPopup();
        displayOutput(cmd_in, 'If nothing happened, try disabling your popup blocker.<br><br>To create popup bookmarklet, bookmark <a href="javascript:window.open(\'http://cmd.deviouschimp.co.uk/\',\'_blank\',\'height=388,width=669,left=100,top=100,menubar=no,location=no,scrollbars=yes,status=no,toolbar=no,titlebar=no\');">this link.</a>');
        break;
      case 'ispopup':
        if (this.popup) {
          displayOutput(cmd_in, 'Popup mode is enabled.');
        } else {
          displayOutput(cmd_in, 'Popup mode is not enabled.');
        }
        break;
      default:
        this.ajaxCmd(cmd_in);
        return true;
    }
  },

  /**
   * Sends the command to the server and calls displayOutput() to
   * deal with the response.
   */
  ajaxCmd: function(cmd_in) {
    $.ajax({
      url: this.options.responder,
      type: 'POST',
      dataType: 'json',
      data: {
        '_token': $('input[name="_token"]').val(),
        'cmd_in': cmd_in
      },
      success: this.handleAjaxSuccess.bind(this),
      error: this.handleAjaxFailure.bind(this),
      timeout: this.options.timeout_length
    });
  },

  /**
   * Handle AJAX success
   * @param  object data AJAX response data
   */
  handleAjaxSuccess: function(data) {
    if (data.redirect !== undefined) {
      document.location.href = data.redirect;
    }

    if (data.openWindow !== undefined) {
      window.open(data.openWindow, '_blank', data.openWindowSpecs);
    }

    if (data.log !== undefined && data.log !== '') {
      console.log(data.log);
    }

    // mask the text that was input when outputting it to screen
    if (data.hide_output === true) {
      data.cmd_in = new Array(cmd_in.length + 1).join("*");
    }
    // Show password/text box
    if (data.show_pass === true) {
      cmd.showInputType('password');
    } else {
      cmd.showInputType();
    }
    
    cmd.handleResponse({
      cmd_in: data.cmd_in,
      cmd_out: data.cmd_out
    });

    // user.id = data.user.id;
    // user.name = data.user.name;
    prompt_str = data.user.name + ' $ ';
    $('.prompt').html(prompt_str);

    // autofill cmd_in from PHP 
    if (data.cmd_fill !== '') {
      $('#cmd_in').val(data.cmd_fill);
    }
  },

  /**
   * Handle AJAX failure
   * @param  object data   AJAX response data
   * @param  string status AJAX response status
   */
  handleAjaxFailure: function(data, status) {
    var cmd_out;

    switch (status) {
      case 'parsererror':
        cmd_out = 'WTF error.';
        break;
      case 'timeout':
        cmd_out = "Time out. Try again. If you continue to get this " +
          "error increase the timeout limit by typing 'timeout " +
          "10,000' (i.e. 10,000ms. Default is 3000)";
        break;
      default:
      cmd_out = 'Server error. Try again.';
    }

    cmd.handleResponse({
      cmd_in: 'oops',
      cmd_out: cmd_out
    });
  }
}