<?php 
/**
 * Read RSS feeds
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Validator;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Feed;

/**
 * Read RSS feeds
 */
class Feeds extends LoggedInCommand
{

    /**
     * Run the command
     */
    public function process() {
        // <td>feeds</td>
        // <td>feeds add name rss_url.xml</td>
        // <td>feeds list</td>
        // <td>feeds remove ID</td>
 
        $user = Auth::user();
        $action = $this->input->get(1);

        if ($action === 'add') {
            if ($this->input->get(1) == false) {
                $this->response->error('You need to provide a url.');
                return false;
            }

            $data = [
                'name' => $this->input->get(2),
                'url' => $this->input->get(3)
            ];

            $rules = [
                'name' => 'required|string|min:1',
                'url' => 'required|active_url'
            ];

            if (!$this->validateOrDie($data, $rules)) {
                return;
            }

            $feed = new Feed($data);
            $user->feeds()->save($feed);
            $this->response->alert('Ok');
            return;
        }


        if ($action === 'list') {
            $feeds = $user->feeds;

            if (count($feeds) === 0) {
                $this->response->error('No feeds. use `FEED ADD ...`');
                return;
            }

            foreach ($feeds as $feed) {
                $this->response->say(
                    Format::title(e($feed->name)) .': ' . e($feed->url).'<br>'
                );
            }

            return;
        }


        if ($action == 'remove') {
            $name = $this->input->get(2);

            if ($name === false) {
                $this->response->error('You must provide a feed name.');
                return;
            }

            $feed = Feed::where('name', $name)
                          ->where('user_id', $user->id)
                          ->first();

            if (!$feed) {
                $this->response->error('Could not find feed or it isn\'t yours to remove.');
                return;
            }

            $result = $feed->delete();

            if ($result) {
                $this->response->alert('Feed removed.');
            } else {
                $this->response->error('Problem removing feed.');
            }

            return;
        }





        // ============= Get feeds ============================
        $name = $this->input->get(1);

        $feeds = Feed::where('user_id', $user->id);

        if ($name !== false) {
            $feeds->where('name', $name);
        }

        $feeds = $feeds->get();

        if (!$feeds) {
            $this->response->say('Couldn\'t get feed list.');
            return;
        }

        foreach ($feeds as $feed) {
            $the_feed = $feed->getFeed(); // Well shit this is getting confusing

            $this->response->say(Format::feed($the_feed));
        }

    }

}