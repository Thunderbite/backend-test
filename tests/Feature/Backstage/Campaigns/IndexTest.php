<?php

namespace Tests\Feature\Backstage\Campaigns;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function aVisitorCannotSeeTheIndexView(): void
    {
        $response = $this->get(route('backstage.campaigns.index'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** @test */
    public function anAuthenticatedUserCanSeeTheIndexView(): void
    {
        $this->signIn();

        $response = $this->get(route('backstage.campaigns.index'));
        $response->assertOk();
        $response->assertViewIs('backstage.campaigns.index');
    }

    /** @test */
    public function aUserThatIsNotAnAdminCannotSeeModifyButtons(): void
    {
        $this->signIn();

        $response = $this->get(route('backstage.campaigns.index'));
        $response->assertDontSeeText('Create campaign');
        $response->assertDontSeeText('tools');
    }

    /** @test */
    public function aAdminUserCanSeeModifyButtons(): void
    {
        $this->signInAsAdmin();

        $response = $this->get(route('backstage.campaigns.index'));
        $response->assertSeeText('Create campaign');
        $response->assertSeeText('tools');
    }
}
