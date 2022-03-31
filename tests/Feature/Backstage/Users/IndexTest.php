<?php

namespace Tests\Feature\Backstage\Users;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function aVisitorCannotSeeTheIndexView(): void
    {
        $response = $this->get(route('backstage.users.index'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** @test */
    public function anAuthenticatedUserCanSeeTheIndexView(): void
    {
        $this->signIn();

        $response = $this->get(route('backstage.users.index'));
        $response->assertOk();
        $response->assertViewIs('backstage.users.index');
    }

    /** @test */
    public function aUserThatIsNotAnAdminCannotSeeModifyButtons(): void
    {
        $this->signIn();

        $response = $this->get(route('backstage.users.index'));
        $response->assertDontSeeText('Create user');
        $response->assertDontSeeText('tools');
    }

    /** @test */
    public function aAdminUserCanSeeModifyButtons(): void
    {
        $this->signInAsAdmin();

        $response = $this->get(route('backstage.users.index'));
        $response->assertSeeText('Create user');
        $response->assertSeeText('tools');
    }
}
