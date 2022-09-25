<?php

namespace Tests\Feature\Commands;

use App\User;
use Mrchimp\Chimpcom\Models\Alias;
use Tests\TestCase;

class AliasesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->other_user = User::factory()->create();

        Alias::create([
            'name' => 'global',
            'alias' => 'global',
        ]);

        $private_alias = Alias::create([
            'name' => 'private',
            'alias' => 'private',
        ]);
        $private_alias->user_id = $this->user->id;
        $private_alias->save();
    }

    public function testAliasCommand()
    {
        $this->getGuestResponse('aliases')
            ->assertNotFound()
            ->assertSee(__('chimpcom.must_log_in'));

        $this->getUserResponse('aliases', $this->user)
            ->assertOk()
            ->assertDontSee('global')
            ->assertSee('private');

        $this->getUserResponse('aliases', $this->other_user)
            ->assertOk()
            ->assertDontSee('global')
            ->assertDontSee('private');
    }

    public function testAliasPublicFlag()
    {
        $this->getGuestResponse('aliases --global')
            ->assertSee(__('chimpcom.must_log_in'));

        $this->getUserResponse('aliases --global', $this->user)
            ->assertOk()
            ->assertSee('global')
            ->assertDontSee('private');

        $this->getUserResponse('aliases --global', $this->other_user)
            ->assertOk()
            ->assertSee('global')
            ->assertDontSee('private');
    }
}
