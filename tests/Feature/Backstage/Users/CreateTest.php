<?php

namespace Tests\Feature\Backstage\Users;

use App\Mail\Backstage\Users\WelcomeMail;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /** @test */
    public function aVisitorCannotSeeTheCreateView(): void
    {
        $response = $this->get(route('backstage.users.create'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** @test */
    public function aUserThatIsNotAnAdminCannotSeeTheCreateView(): void
    {
        $this->signIn();

        $response = $this->get(route('backstage.users.create'));
        $response->assertForbidden();
    }

    /** @test */
    public function aAdminUserCanSeeTheCreateView(): void
    {
        $this->signInAsAdmin();

        $response = $this->get(route('backstage.users.create'));
        $response->assertOk();
    }

    /** @test */
    public function aAdminUserCanCreateAUser(): void
    {
        Mail::fake();

        $this->signInAsAdmin();

        $response = $this->post(route('backstage.users.store'), $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'level' => 'readonly',
        ]);

        $this->assertDatabaseHas('users', $attributes);

        Mail::assertQueued(WelcomeMail::class, function ($mail) use ($attributes) {
            return $mail->hasTo($attributes['email']);
        });

        $response->assertRedirect(route('backstage.users.index'));
        $response->assertSessionHas('success', 'The user has been created!');
    }
}
