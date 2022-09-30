<?php

namespace Tests\Feature\Commands;

use App\User;
use Mrchimp\Chimpcom\Models\Shortcut;
use Tests\TestCase;

class ShortcutTest extends TestCase
{
    /** @test */
    public function requires_user_to_be_logged_in()
    {
        $this->getGuestResponse('shortcut foo https://example.com')
            ->assertOk()
            ->assertSee(__('chimpcom.must_log_in'));

        $this->assertShortcutNotCreated();
    }

    /** @test */
    public function adds_a_shortcut()
    {
        $this->getUserResponse('shortcut foo https://example.com')
            ->assertOk()
            ->assertSee('Ok.');

        $this->assertShortcutCreated();
        $this->assertEquals($this->user->id, $this->shortcut->user_id);
    }

    /** @test */
    public function works_for_admins_too()
    {
        $this->getAdminResponse('shortcut foo https://example.com')
            ->assertOk()
            ->assertSee('Ok.');

        $this->assertShortcutCreated();
        $this->assertEquals($this->admin->id, $this->shortcut->user_id);
    }

    /** @test */
    public function global_flag_fails_for_non_admins()
    {
        $this->getUserResponse('shortcut --global foo https://example.com')
            ->assertForbidden();

        $this->assertShortcutNotCreated();
    }

    /** @test */
    public function global_flag_works_for_admins()
    {
        $this->getAdminResponse('shortcut --global foo https://example.com')
            ->assertOk();

        $this->assertShortcutCreated();
        $this->assertNull($this->shortcut->user_id);
    }

    /** @test */
    public function fails_if_url_is_not_a_url()
    {
        $this->getAdminResponse('shortcut foo this_is_not_a_url')
            ->assertOk()
            ->assertSee('There was a problem');

        $this->assertShortcutNotCreated();
    }

    /** @test */
    public function if_name_already_exists_shortcut_is_updated()
    {
        $this->user = User::factory()->create();

        Shortcut::factory()->create([
            'name' => 'foo',
            'url' => 'https://example.com',
            'user_id' => $this->user->id,
        ]);

        $this->getUserResponse('shortcut foo https://example.com/update')
            ->assertOk()
            ->assertSee('Ok.');

        $this->assertEquals(1, Shortcut::count());
        $this->shortcut = Shortcut::first();
        $this->assertEquals('foo', $this->shortcut->name);
        $this->assertEquals('https://example.com/update', $this->shortcut->url);
        $this->assertEquals($this->user->id, $this->shortcut->user_id);
    }

    /** @test */
    public function a_duplicate_name_can_be_created_if_its_for_a_different_user()
    {
        Shortcut::factory()->create([
            'name' => 'foo',
            'url' => 'https://example.com',
            'user_id' => 9999,
        ]);

        $this->getUserResponse('shortcut foo https://example.com/update')
            ->assertOk();

        $this->assertEquals(2, Shortcut::count());
    }

    protected function assertShortcutNotCreated()
    {
        $this->assertEquals(0, Shortcut::count());
    }

    protected function assertShortcutCreated()
    {
        $this->shortcut = Shortcut::first();
        $this->assertEquals(1, Shortcut::count());
        $this->assertEquals('foo', $this->shortcut->name);
        $this->assertEquals('https://example.com', $this->shortcut->url);
    }
}
