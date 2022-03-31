<?php

namespace Tests\Feature\Backstage\Users;

    use App\Models\User;
    use Illuminate\Foundation\Testing\DatabaseMigrations;
    use Illuminate\Foundation\Testing\WithFaker;
    use Tests\TestCase;

    class DestoryTest extends TestCase
    {
        use DatabaseMigrations, WithFaker;

        /** @test */
        public function aVisitorCannotDestroyAUser(): void
        {
            $user = create(User::class);
            $response = $this->delete(route('backstage.users.destroy', $user));
            $response->assertRedirect(route('login'));
            $this->assertGuest();
        }

        /** @test */
        public function aUserThatIsNotAnAdminCannotDestoryAUser(): void
        {
            $this->signIn();
            $user = create(User::class);
            $response = $this->delete(route('backstage.users.destroy', $user));
            $response->assertForbidden();
        }

        /** @test */
        public function aAdminUserCanDestroyAUser(): void
        {
            $this->signInAsAdmin();
            $user = create(User::class);
            $response = $this->delete(route('backstage.users.destroy', $user));
            $response->assertRedirect(route('backstage.users.index'));
            $response->assertSessionHas('success', 'The user has been removed!');
        }
    }
