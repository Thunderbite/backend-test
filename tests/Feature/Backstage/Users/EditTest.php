<?php

namespace Tests\Feature\Backstage\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /** @test */
    public function aVisitorCannotSeeTheEditView(): void
    {
        $user = create(User::class);

        $response = $this->get(route('backstage.users.edit', $user->id));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** @test */
    public function aUserThatIsNotAnAdminCannotSeeTheCreateView(): void
    {
        $this->signIn();
        $user = create(User::class);

        $response = $this->get(route('backstage.users.edit', $user->id));
        $response->assertForbidden();
    }

    /** @test */
    public function aAdminUserCanSeeTheEditView(): void
    {
        $this->signInAsAdmin();
        $user = create(User::class);

        $response = $this->get(route('backstage.users.edit', $user->id));
        $response->assertOk();
    }

    /** @test */
    public function aUserThatIsNotAnAdminCannotModifyAUser(): void
    {
        $this->signIn();

        $user = create(User::class);

        $response = $this->put(route('backstage.users.update', $user), $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function anAdminUserCanModifyAUser(): void
    {
        $this->withoutExceptionHandling();
        $this->signInAsAdmin();

        $user = create(User::class);

        $response = $this->put(route('backstage.users.update', $user), $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'level' => 'readonly',
        ]);

        $this->assertDatabaseHas('users', $attributes);

        $response->assertRedirect(route('backstage.users.edit', $user->id));
        $response->assertSessionHas('success', 'The user details have been saved!');
    }
}
