<?php

namespace Tests\Feature\Backstage\Campaigns;

use App\Models\Campaign;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /** @test */
    public function aVisitorCannotDestroyACampaign(): void
    {
        $campaign = create(Campaign::class);
        $response = $this->delete(route('backstage.campaigns.destroy', $campaign));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** @test */
    public function aUserThatIsNotAnAdminCannotDestoryACampaign(): void
    {
        $this->signIn();
        $campaign = create(Campaign::class);
        $response = $this->delete(route('backstage.campaigns.destroy', $campaign));
        $response->assertForbidden();
    }

    /** @test */
    public function aAdminUserCanDestroyACampaign(): void
    {
        $this->signInAsAdmin();
        $campaign = create(Campaign::class);
        $response = $this->delete(route('backstage.campaigns.destroy', $campaign));
        $response->assertRedirect(route('backstage.campaigns.index'));
        $response->assertSessionHas('success', 'The campaign has been removed!');
    }
}
