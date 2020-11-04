/**
 * Stack for holding previous commands for retrieval with the up arrow.
 * Stores data in localStorage. Won't push consecutive duplicates.
 *
 * @author   Jake Gully, chimpytk@gmail.com
 * @license  MIT License
 */

/**
 * Constructor
 * @param {string}  id       Unique id for this stack
 * @param {integer} max_size Number of commands to store
 */
export default class CmdStack {
  instance_id: string;
  cur: number;
  arr: string[];
  max_size: number;

  constructor(id: string, max_size: number) {
    this.instance_id = id;
    this.cur = 0;
    this.arr = []; // This is a fairly meaningless name but
    // makes it sound like this function was
    // written by a pirate.  I'm keeping it.

    this.max_size = max_size;
    this.loadArray();
  }

  /**
   * Store the array in localstorage
   */
  setArray() {
    localStorage.setItem('cmd_stack_' + this.instance_id, JSON.stringify(this.arr));
  }

  /**
   * Load array from localstorage
   */
  loadArray() {
    if (!localStorage.getItem('cmd_stack_' + this.instance_id)) {
      this.arr = [];
      this.setArray();
      return this.arr;
    }

    try {
      this.arr = JSON.parse(localStorage.getItem('cmd_stack_' + this.instance_id));
    } catch (err) {
      this.arr = [];
    }

    return this.arr;
  }

  /**
   * Push a command to the array
   * @param  {string} cmd Command to append to stack
   */
  push(cmd: string) {
    // don't push if same as last command
    if (cmd === this.arr[this.arr.length - 1]) {
      return false;
    }

    this.arr.push(cmd);

    // crop off excess
    while (this.arr.length > this.max_size) {
      this.arr.shift();
    }

    this.cur = this.arr.length;

    this.setArray();
  }

  /**
   * Get previous command from stack (up key)
   * @return {string} Retrieved command string
   */
  prev(): string {
    this.cur -= 1;

    if (this.cur < 0) {
      this.cur = 0;
    }

    return this.arr[this.cur];
  }

  /**
   * Get next command from stack (down key)
   * @return {string} Retrieved command string
   */
  next(): string {
    this.cur = this.cur + 1;

    // Return a blank string as last item
    if (this.cur === this.arr.length) {
      return '';
    }

    // Limit
    if (this.cur > this.arr.length - 1) {
      this.cur = this.arr.length - 1;
    }

    return this.arr[this.cur];
  }

  /**
   * Move cursor to last element
   */
  reset() {
    this.cur = this.arr.length;
  }

  /**
   * Is stack empty
   * @return {Boolean} True if stack is empty
   */
  isEmpty(): boolean {
    return this.arr.length === 0;
  }

  /**
   * Empty array and remove from localstorage
   */
  empty() {
    this.arr = undefined;
    localStorage.clear();
    this.reset();
  }

  /**
   * Get current cursor location
   * @return {integer} Current cursor index
   */
  getCur(): number {
    return this.cur;
  }

  /**
   * Get entire stack array
   * @return {array} The stack array
   */
  getArr(): string[] {
    return this.arr;
  }

  /**
   * Get size of the stack
   * @return {Integer} Size of stack
   */
  getSize(): number {
    return this.arr.length;
  }
}
