
var cmd = Cmd({
	selector: '#chimpcom',
	external_processor: Chimpcom.respond.bind(Chimpcom),
	timeout_length: 20000
});
