<?php

namespace Tests\Feature\Backstage\Dashboard;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function aVisitorCannotViewTheDashboard(): void
    {
        $response = $this->get('/backstage/dashboard');
        $response->assertRedirect('/backstage/login');
        $this->assertGuest();
    }

    /** @test */
    public function anAuthenticatedUserCanViewTheDashboard(): void
    {
        $this->signIn();

        $response = $this->get('/backstage/dashboard');
        $response->assertOk();
    }
}
