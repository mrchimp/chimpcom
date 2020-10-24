<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class SudoTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function sudo_reports_guests()
    {
        $this->getGuestResponse('sudo test')
            ->assertSee('This incident will be reported to the relevant authorities')
            ->assertStatus(200);
    }

    /** @test */
    public function sudo_reports_non_admin_users()
    {
        $this->getUserResponse('sudo test')
            ->assertSee('This incident will be reported to the relevant authorities')
            ->assertStatus(200);
    }

    /** @test */
    public function sudo_is_not_available_to_admin_but_they_could_use_it_if_it_was()
    {
        $this->getAdminResponse('sudo test')
            ->assertSee('You have permission but sudo is not available at this time.')
            ->assertStatus(200);
    }
}
