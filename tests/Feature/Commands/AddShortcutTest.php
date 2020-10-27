<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class AddShortcutTest extends TestCase
{
    /** @test */
    public function addshortcut_requires_user_to_be_logged_in()
    {
        $this->getGuestResponse('addshortcut foo http://example.com')
            ->assertStatus(200)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function addshortcut_is_not_available_to_non_admins()
    {
        $this->getUserResponse('addshortcut foo http://example.com')
            ->assertStatus(200)
            ->assertSee(__('chimpcom.not_admin'));
    }

    /** @test */
    public function addshortcut_works_for_admins()
    {
        $this->getAdminResponse('addshortcut foo http://example.com')
            ->assertStatus(200)
            ->assertSee('Ok.');
    }

    /** @test */
    public function addshortcut_fails_if_url_is_not_a_url()
    {
        $this->getAdminResponse('addshortcut foo this_is_not_a_url')
            ->assertStatus(200)
            ->assertSee('There was a problem');
    }
}
