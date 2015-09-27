
var cmd = new Cmd({
	selector: '#chimpcom',
	external_processor: Chimpcom.respond.bind(Chimpcom),
	timeout_length: 20000,
	remote_cmd_list_url: '/ajax/commands',
	tabcomplete_url: '/ajax/tabcomplete',
	typewriter_time: 10
});

if (typeof QueryString['cmd'] === 'string') {
	cmd.handleInput(QueryString['cmd']);
} else {
	cmd.handleInput('hi');
}
