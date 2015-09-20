
var cmd = Cmd({
	selector: '#chimpcom',
	external_processor: Chimpcom.respond.bind(Chimpcom),
	timeout_length: 20000
});

if (typeof QueryString['cmd'] === 'string') {
	cmd.handleInput(QueryString['cmd']);
} else {
	cmd.handleInput('hi');
}
