<?php 
/**
 * Show some lorem ipsum
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;

/**
 * Show some lorem ipsum
 */
class Lipsum extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $this->response->say('<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean sodales malesuada tempus. Proin eu ante et diam lobortis ullamcorper. Vestibulum at odio feugiat odio lacinia sodales quis eu diam. Nunc venenatis fringilla ullamcorper. Aliquam eget libero nec leo ultricies tristique in non velit. Nullam commodo tempor tempus. Morbi ac gravida neque. Nulla nunc lectus, ultricies in aliquet sit amet, ultricies nec lacus. Nullam rutrum rutrum tellus, ac cursus quam sagittis at. Suspendisse vitae rutrum lorem. Suspendisse augue tortor, vestibulum nec vehicula in, fringilla a quam. Fusce dictum, nunc ut bibendum blandit, odio lacus elementum mi, sed semper metus urna ac velit. Pellentesque sit amet nisl a massa suscipit hendrerit at vel odio.</p>
            <p>Maecenas id condimentum velit. Nulla elementum, elit quis sodales suscipit, ante arcu facilisis dui, mollis mattis risus urna sit amet ipsum. Integer et est a felis interdum porta id vitae nibh. Morbi tempor dolor ut urna tristique dignissim. Duis in auctor lacus. Cras lacinia, eros ac pretium convallis, felis turpis tincidunt risus, vitae pellentesque nibh lacus in nisl. In nulla mauris, consectetur eget varius ac, condimentum ac sem. Donec blandit porta malesuada.</p>
            <p>Nulla adipiscing nisl a ligula dignissim et sodales ante pretium. Curabitur placerat, urna quis lobortis suscipit, justo leo fermentum nibh, nec condimentum lorem sem eu libero. Aenean aliquet accumsan dolor eget ultricies. Curabitur id tempus nulla. Nam at magna lacus, et condimentum quam. Etiam eget lacus mauris, eget laoreet nibh. Nunc ultrices turpis eu justo hendrerit facilisis. Cras felis orci, molestie tempus accumsan sed, tempor non urna. Mauris in augue erat. Maecenas tincidunt, lorem et vulputate elementum, est nisl accumsan massa, non consectetur ante elit eu dolor. Pellentesque sit amet velit eu magna laoreet adipiscing. Aliquam congue scelerisque enim ac molestie. Nam lacinia, enim quis iaculis egestas, nunc nisl consequat leo, eget auctor nulla tortor eget lacus. Vivamus dapibus, arcu ac malesuada mattis, ipsum lacus vulputate orci, ultricies pharetra ante orci nec ligula. Quisque hendrerit varius magna, in pretium mauris porta id.</p>
            <p>Mauris a velit nibh, vel consectetur felis. Nullam libero diam, accumsan sed ultrices id, molestie ac lectus. Vivamus nec metus sed orci pretium sagittis. Donec rhoncus posuere sem sit amet volutpat. Nam consequat libero eget est ultrices rhoncus. Nunc tellus dui, facilisis mollis pulvinar vitae, sollicitudin non mi. Proin risus augue, hendrerit a tempor ut, rhoncus eu est. In in metus metus, ac aliquam ipsum. Fusce justo elit, viverra eget feugiat sed, congue vel urna.</p>
            <p>Donec facilisis felis lorem. In pellentesque vulputate iaculis. Morbi iaculis, magna a dapibus commodo, mauris odio malesuada purus, ut malesuada dui quam sed tortor. Donec luctus magna elit. Pellentesque sit amet nisi nec risus mattis suscipit sed at mi. Nullam id nisi lacus, fringilla scelerisque mauris. Nullam tempus ipsum vel ante dictum tincidunt. Praesent bibendum erat in est eleifend convallis. Mauris massa mi, varius eu sodales vel, molestie eget metus. Nunc auctor, dolor at facilisis tempus, tellus ante lacinia est, id accumsan justo est id quam. In id tellus dui, id auctor ligula. Integer id eros magna, vel malesuada mauris. Aenean viverra mollis quam, quis gravida justo condimentum eget. Etiam in metus et diam aliquam commodo.</p>
            <a href="http://www.lipsum.com/" target="_blank">Get more</a>');
    }

}