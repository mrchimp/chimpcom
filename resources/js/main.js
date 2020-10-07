import Cmd from '../vendor/cmd/js/Cmd.js';
import Chimpcom from './Chimpcom';
import QueryString from './QueryString';

const chimpcom = new Chimpcom();

const cmd = new Cmd({
  selector: '#chimpcom',
  external_processor: chimpcom.respond.bind(chimpcom),
  remote_cmd_list_url: 'ajax/commands',
  tabcomplete_url: 'ajax/tabcomplete',
  typewriter_time: 0,
  cancel_edit_handler: chimpcom.clearAction.bind(chimpcom),
  save_edit_handler: chimpcom.saveContent.bind(chimpcom),
});

if (typeof QueryString['cmd'] === 'string') {
  cmd.handleInput(QueryString['cmd']);
} else {
  cmd.handleInput('hi');
}
