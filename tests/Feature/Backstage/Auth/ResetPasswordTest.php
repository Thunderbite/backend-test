<?php

namespace Tests\Feature\Backstage\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use DatabaseMigrations;

    protected function getValidToken($user)
    {
        return Password::broker()->createToken($user);
    }

    protected function getInvalidToken()
    {
        return 'invalid-token';
    }

    /** @test */
    public function aUserCanViewAPasswordResetForm(): void
    {
        $user = create(User::class);
        $token = $this->getValidToken($user);

        $response = $this->get('/backstage/password/reset/'.$token);

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.reset');
        $response->assertViewHas('token', $token);
    }

    /** @test */
    public function aUserCanViewAPasswordResetFormWhenAuthenticated(): void
    {
        $user = create(User::class);
        $token = $this->getValidToken($user);

        $response = $this->actingAs($user)->get('/backstage/password/reset/'.$token);

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.reset');
        $response->assertViewHas('token', $token);
    }

    /** @test */
    public function aUserCanResetPasswordWithValidToken(): void
    {
        Event::fake();
        $user = create(User::class);

        $response = $this->post('/backstage/password/reset', [
            'token' => $this->getValidToken($user),
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect('/backstage');
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('new-awesome-password', $user->fresh()->password));
        $this->assertAuthenticatedAs($user);
        Event::assertDispatched(PasswordReset::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });
    }

    /** @test */
    public function aUserCannotResetPasswordWithInvalidToken(): void
    {
        $user = create(User::class, [
            'password' => Hash::make('old-password'),
        ]);

        $invalidToken = $this->getInvalidToken();

        $response = $this->from('/backstage/password/reset/'.$invalidToken)->post('/backstage/password/reset', [
            'token' => $invalidToken,
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect('/backstage/password/reset/'.$invalidToken);
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    /** @test */
    public function aUserCannotResetPasswordWithoutProvidingANewPassword(): void
    {
        $user = create(User::class, [
            'password' => Hash::make('old-password'),
        ]);

        $token = $this->getValidToken($user);

        $response = $this->from('/backstage/password/reset/'.$token)->post('/backstage/password/reset/', [
            'token' => $token,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect('/backstage/password/reset/'.$token);
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    /** @test */
    public function aUserCannotResetPasswordWithoutProvidingAnEmail(): void
    {
        $user = create(User::class, [
            'password' => Hash::make('old-password'),
        ]);

        $token = $this->getValidToken($user);

        $response = $this->from('/backstage/password/reset/'.$token)->post('/backstage/password/reset/', [
            'token' => $token,
            'email' => '',
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect('/backstage/password/reset/'.$token);
        $response->assertSessionHasErrors('email');
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }
}
