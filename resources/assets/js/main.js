import Cmd from '../vendor/cmd/js/Cmd.js';
import Chimpcom from './Chimpcom';
import QueryString from './QueryString';

const chimpcom = new Chimpcom();

const cmd = new Cmd({
	selector: '#chimpcom',
	external_processor: chimpcom.respond.bind(chimpcom),
	timeout_length: 20000,
	remote_cmd_list_url: 'ajax/commands',
	tabcomplete_url: 'ajax/tabcomplete',
	typewriter_time: 0
});

if (typeof QueryString['cmd'] === 'string') {
	cmd.handleInput(QueryString['cmd']);
} else {
	cmd.handleInput('hi');
}
