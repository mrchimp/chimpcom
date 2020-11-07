import Cmd from './Cmd';
import QueryString from './QueryString';

const cmd = new Cmd();

if (typeof QueryString['cmd'] === 'string') {
  cmd.handleInput(QueryString['cmd']);
} else {
  cmd.disableInput();
  cmd.handleInput('hi');
}
