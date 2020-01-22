/**
* Chimpcom external command handler for Cmd.js
*/
export default class Chimpcom {
  constructor() {
    this.options = {
      responder: 'ajax/respond/json',
      timeout_length: 2000
    };
  }

  /**
   * Get a response
   * @param  string cmd_in The command string
   * @param  object cmd    Reference to Cmd instance
   * @return various
   */
  respond(cmd_in, cmd) {
    this.cmd = cmd;

    switch (cmd_in) {
      case 'bash':
        if (!$('#bash').length) {
          $('body').prepend('<img src="assets/img/bash.png" id="bash" style="position:fixed;">');
        }
        return 'Ow! I hope you\'re going to fix that!';
      case 'alert':
      case 'alarm':
      case 'timer':
        window.open('timer.php?time=' + param,
          'chimpcom_timer',
          'height=90,width=350,left=100,top=100,menubar=no,location=no,scrollbars=no,status=no,toolbar=no,titlebar=no');

        return 'Clock has opened in new window.';
      case 'fix':
        $('#bash').remove();
        return 'Good as new.';
      case 'pwd':
        return document.location.href;
      case 'popup':
      case 'detach':
        openChimpcomPopup();
        return 'If nothing happened, try disabling your popup blocker.<br><br>To create popup bookmarklet, bookmark <a href="javascript:window.open(\'http://cmd.deviouschimp.co.uk/\',\'_blank\',\'height=388,width=669,left=100,top=100,menubar=no,location=no,scrollbars=yes,status=no,toolbar=no,titlebar=no\');">this link.</a>';
      case 'ispopup':
        if (this.popup) {
          return 'Popup mode is enabled.';
        } else {
          return 'Popup mode is not enabled.';
        }
      default:
        this.ajaxCmd(cmd_in);
        return true;
    }
  }

  /**
   * Sends the command to the server and calls displayOutput() to
   * deal with the response.
   */
  ajaxCmd(cmd_in) {
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
  }

  /**
   * Handle AJAX success
   * @param  object data AJAX response data
   */
  handleAjaxSuccess(data) {
    this.cmd.handleResponse(data);

    $('.prompt').html(data.user.name + ' $ ');

    // autofill cmd_in from PHP
    if (data.cmd_fill !== '') {
      $('#cmd_in').val(data.cmd_fill);
    }
  }

  /**
   * Handle AJAX failure
   * @param  object data   AJAX response data
   * @param  string status AJAX response status
   */
  handleAjaxFailure(data, status) {
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

    this.cmd.handleResponse({
      cmd_out: cmd_out
    });
  }
}
