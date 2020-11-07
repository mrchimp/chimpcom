<?php

namespace Tests\Unit;

use App\Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Config;
use Mrchimp\Chimpcom\Actions\Candyman;
use Mrchimp\Chimpcom\Chimpcom;
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
        $this->withSession(['action' => 'specialaction']);

        $this->getGuestResponse('clearaction')
            ->assertSessionHas('action', 'normal');
    }

    /** @test */
    public function shortcuts_can_be_used()
    {
        Shortcut::factory()->create([
            'name' => 'testshortcut',
            'url' => 'http://example.com',
        ]);

        $json = $this->getGuestResponse('testshortcut')->json();

        $this->assertEquals('http://example.com', $json['redirect']);
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
