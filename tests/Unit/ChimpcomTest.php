<?php

namespace Tests\Unit;

use Mrchimp\Chimpcom\Actions\Candyman;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Commands\Hi;
use Tests\TestCase;

class ChimpcomTest extends TestCase
{
    /** @test */
    public function command_can_be_instantiated_if_it_exists()
    {
        $command = Chimpcom::instantiateCommand('hi');

        $this->assertInstanceOf(Hi::class, $command);
    }

    /** @test */
    public function null_will_be_returned_if_command_is_not_recognised()
    {
        $command = Chimpcom::instantiateCommand('commandthatdoesnotexist');

        $this->assertNull($command);
    }

    /** @test */
    public function action_can_be_instantiated_if_it_exists()
    {
        $action = Chimpcom::instantiateAction('candyman');

        $this->assertInstanceOf(Candyman::class, $action);
    }

    /** @test */
    public function null_will_be_return_if_action_is_not_recognised()
    {
        $action = Chimpcom::instantiateAction('actionthatdoesnotexist');

        $this->assertNull($action);
    }

    /** @test */
    public function command_names_are_case_insensitive()
    {
        $this->assertInstanceOf(Hi::class, Chimpcom::instantiateCommand('Hi'));
        $this->assertTrue(Chimpcom::commandExists('Hi'));

        $this->assertInstanceOf(Candyman::class, Chimpcom::instantiateAction('Candyman'));
        $this->assertTrue(Chimpcom::actionExists('Candyman'));
    }
}
