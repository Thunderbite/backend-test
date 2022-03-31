<?php

namespace Tests\Feature\Backstage\Campaigns;

use App\Models\Campaign;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    private function publishCampaign($overrides = [])
    {
        $this->signInAsAdmin();

        $campaign = make(Campaign::class, $overrides);

        return $this->post(route('backstage.campaigns.store'), $campaign->toArray());
    }

    /** @test */
    public function ACampaignRequiresAName(): void
    {
        $this->publishCampaign(['name' => ''])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function ACampaignRequiresATimezone(): void
    {
        $this->publishCampaign(['timezone' => ''])
            ->assertSessionHasErrors('timezone');
    }

    /** @test */
    public function ACampaignRequiresStartsAt(): void
    {
        $this->publishCampaign(['starts_at' => ''])
            ->assertSessionHasErrors('starts_at');
    }

    /** @test */
    public function ACampaignRequiresTargeting(): void
    {
        $this->publishCampaign(['targeting' => null])
            ->assertSessionHasErrors('targeting');
    }

    /** @test */
    public function ACampaignRequiresSegmentation(): void
    {
        $this->publishCampaign(['segmentation' => null])
            ->assertSessionHasErrors('segmentation');
    }
}
