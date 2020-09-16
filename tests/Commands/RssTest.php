<?php

namespace Tests\Commands;

use App\User;
use Mrchimp\Chimpcom\Models\Feed;

class RssTest extends CommandTestTemplate
{
    /** @test */
    public function guest_user_cant_do_much_with_this_command()
    {
        $this
            ->getGuestResponse('rss')
            ->assertStatus(401)
            ->assertSee('You must be logged in to use this command.');
    }

    /** @test */
    public function adding_an_rss_feed_and_not_providing_a_name_returns_422()
    {
        $this
            ->getUserResponse('rss add')
            ->assertStatus(422)
            ->assertSee('You need to provide a name.');
    }

    /** @test */
    public function adding_an_rss_feed_and_not_providing_a_url_returns_422()
    {
        $this
            ->getUserResponse('rss add my_feed')
            ->assertStatus(422)
            ->assertSee('You need to provide a URL.');
    }

    /** @test */
    public function an_rss_feed_can_be_added()
    {
        $this
            ->getUserResponse('rss add my_feed http://example.com')
            ->assertStatus(200)
            ->assertSee('Ok.');
    }

    /** @test */
    public function adding_an_rss_feed_requires_an_available_url()
    {
        $this
            ->getUserResponse('rss add my_feed http://baddomain.invalid')
            ->assertStatus(422)
            ->assertSee('There was a problem with that.');
    }

    /** @test */
    public function getting_a_list_of_rss_feeds_when_you_have_none_gives_a_message()
    {
        $user = factory(User::class)->create();

        $this
            ->getUserResponse('rss list', $user)
            ->assertStatus(200)
            ->assertSee('No feeds. use `RSS ADD ...`');
    }

    /** @test */
    public function a_list_of_rss_feeds_can_be_displayed()
    {
        $user = factory(User::class)->create();

        factory(Feed::class)->create([
            'user_id' => $user->id,
            'name' => 'test_feed_name',
            'url' => 'http://example.com/my_rss_feed.xml',
        ]);

        $this
            ->getUserResponse('rss list', $user)
            ->assertStatus(200)
            ->assertSee('test_feed_name')
            ->assertSee('http:\/\/example.com\/my_rss_feed.xml');
    }

    /** @test */
    public function removing_a_feed_requires_a_name()
    {
        $user = factory(User::class)->create();

        $this
            ->getUserResponse('rss remove', $user)
            ->assertStatus(422)
            ->assertSee('You must provide a feed name.');
    }

    /** @test */
    public function a_user_cant_remove_another_users_feed()
    {
        $user = factory(User::class)->create();
        $other_user = factory(User::class)->create();

        factory(Feed::class)->create([
            'user_id' => $other_user->id,
            'name' => 'test_feed_name',
            'url' => 'http://example.com/my_rss_feed.xml',
        ]);

        $this->getUserResponse('rss remove test_feed_name', $user)
            ->assertStatus(422)
            ->assertSee('Could not find feed or it isn\'t yours to remove.');
    }

    /** @test */
    public function a_feed_can_be_removed()
    {
        $user = factory(User::class)->create();

        factory(Feed::class)->create([
            'user_id' => $user->id,
            'name' => 'test_feed_name',
            'url' => 'http://example.com/my_rss_feed.xml',
        ]);

        $this
            ->getUserResponse('rss remove test_feed_name', $user)
            ->assertStatus(200);

        $this->assertEquals(0, Feed::count());
    }

    /** @test */
    public function how_can_we_mock_rss_feeds_question_mark()
    {
        $this->markTestIncomplete();
    }
}
