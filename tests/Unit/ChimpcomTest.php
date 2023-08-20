<?php

namespace Tests\Unit;

use Mrchimp\Chimpcom\Actions\Action;
use App\User;
use Illuminate\Support\Facades\Config;
use Mrchimp\Chimpcom\Actions\Candyman;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Commands\Hi;
use Mrchimp\Chimpcom\Models\Shortcut;
use Tests\TestCase;

class ChimpcomTest extends TestCase
{
    /** @test */
    public function command_can_be_instantiated_if_it_exists()
    {
        $command = Command::make('hi');

        $this->assertInstanceOf(Hi::class, $command);
    }

    /** @test */
    public function null_will_be_returned_if_command_is_not_recognised()
    {
        $command = Command::make('commandthatdoesnotexist');

        $this->assertNull($command);
    }

    /** @test */
    public function action_can_be_instantiated_if_it_exists()
    {
        $action = Command::make('candyman', 'action');

        $this->assertInstanceOf(Candyman::class, $action);
    }

    /** @test */
    public function null_will_be_return_if_action_is_not_recognised()
    {
        $action = Command::make('actionthatdoesnotexist', 'action');

        $this->assertNull($action);
    }

    /** @test */
    public function command_names_are_case_insensitive()
    {
        $this->assertInstanceOf(Hi::class, Command::make('Hi'));
        $this->assertTrue(Command::exists('Hi'));

        $this->assertInstanceOf(Candyman::class, Command::make('Candyman', 'action'));
        $this->assertTrue(Action::exists('Candyman'));
    }

    /** @test */
    public function clearaction_clears_the_action()
    {
        $this->getGuestResponse('clearaction', 'someactionid');
        $this->assertNoAction();
    }

    /** @test */
    public function global_shortcuts_can_be_used()
    {
        $this->user = User::factory()->create();
        $this->other_user = User::factory()->create();

        Shortcut::factory()->create([
            'name' => 'testshortcut',
            'url' => 'http://example.com',
            'user_id' => null,
        ]);

        $guest_json = $this
            ->getGuestResponse('testshortcut')
            ->assertOk()
            ->json();
        $this->assertEquals('http://example.com', $guest_json['redirect']);

        $user_json = $this
            ->getUserResponse('testshortcut')
            ->assertOk()
            ->json();
        $this->assertEquals('http://example.com', $user_json['redirect']);

        $other_user_json = $this
            ->getUserResponse('testshortcut', $this->other_user)
            ->assertOk()
            ->json();
        $this->assertEquals('http://example.com', $other_user_json['redirect']);

        $admin_json = $this
            ->getAdminResponse('testshortcut')
            ->assertOk()
            ->json();
        $this->assertEquals('http://example.com', $admin_json['redirect']);
    }

    /** @test */
    public function private_shortcuts_can_be_used_by_their_owners()
    {
        $this->user = User::factory()->create();
        $this->other_user = User::factory()->create();

        Shortcut::factory()->create([
            'name' => 'testshortcut',
            'url' => 'http://example.com',
            'user_id' => $this->user->id,
        ]);

        $guest_json = $this
            ->getGuestResponse('testshortcut')
            ->assertNotFound()
            ->json();
        $this->assertArrayNotHasKey('redirect', $guest_json);

        $user_json = $this
            ->getUserResponse('testshortcut')
            ->assertOk()
            ->json();
        $this->assertEquals('http://example.com', $user_json['redirect']);

        $other_user_json = $this
            ->getUserResponse('testshortcut', $this->other_user)
            ->assertNotFound()
            ->json();
        $this->assertArrayNotHasKey('redirect', $other_user_json);

        $admin_json = $this
            ->getAdminResponse('testshortcut')
            ->assertNotFound()
            ->json();
        $this->assertArrayNotHasKey('redirect', $admin_json);
    }

    /** @test */
    public function invalid_commands_dont_totally_fail()
    {
        Config::set('chimpcom.commands', [
            'testcommand' => 'Fake/Class/That/Doesnt/Exist',
        ]);

        $this->getGuestResponse('testcommand')
            ->assertSee('Invalid command: testcommand')
            ->assertStatus(404);
    }
}
