<?php

namespace Tests\Feature\Backstage\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function aVisitorCanViewAnEmailPasswordForm(): void
    {
        $response = $this->get('/backstage/password/reset');

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.email');
    }

    /** @test */
    public function aUserCanViewAnEmailPasswordFormWhenAuthenticated(): void
    {
        $this->signIn();

        $response = $this->get('/backstage/password/reset');

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.email');
    }

    /** @test */
    public function aUserReceivesAnEmailWithAPasswordResetLink(): void
    {
        Notification::fake();

        $user = create(User::class, [
            'email' => 'test@thunderbite.com',
        ]);

        $response = $this->from('/backstage/password/reset')->post('/backstage/password/email', [
            'email' => 'test@thunderbite.com',
        ]);

        $this->assertNotNull($token = DB::table('password_resets')->first());
        Notification::assertSentTo($user, ResetPassword::class, function ($notification, $channels) use ($token) {
            return Hash::check($notification->token, $token->token) === true;
        });
    }

    /** @test */
    public function aUserDoesNotReceiveEmailWhenNotRegistered(): void
    {
        Notification::fake();

        $response = $this->from('backstage/password/reset')->post('backstage/password/email', [
            'email' => 'test@thunderbite.com',
        ]);

        $response->assertRedirect('backstage/password/reset');
        $response->assertSessionHasErrors('email');
        Notification::assertNotSentTo(make(User::class, ['email' => 'test@thunderbite.com']), ResetPassword::class);
    }

    /** @test */
    public function emailIsRequired()
    {
        $response = $this->from('backstage/password/reset')->post('backstage/password/email', []);

        $response->assertRedirect('backstage/password/reset');
        $response->assertSessionHasErrors('email');
    }

    public function testEmailIsAValidEmail()
    {
        $response = $this->from('backstage/password/reset')->post('backstage/password/email', [
            'email' => 'invalid-email',
        ]);

        $response->assertRedirect('backstage/password/reset');
        $response->assertSessionHasErrors('email');
    }
}
