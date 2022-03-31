<?php

namespace Tests\Feature\Backstage\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function aVisitorCanViewTheLoginForm(): void
    {
        $response = $this->get('/backstage/login');
        $response->assertOk();
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function aLoggedInUserCannotViewTheLoginForm(): void
    {
        $this->signIn();
        $response = $this->get('/backstage/login');
        $response->assertRedirect('/backstage');
    }

    /** @test */
    public function aVisitorCanLoginWithCorrectCredentials(): void
    {
        $user = create(User::class, [
            'password' => bcrypt($password = 'secret'),
        ]);

        $response = $this->post('/backstage/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/backstage');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function aVisitorCannotLoginWithIncorrectPassword(): void
    {
        $user = create(User::class, [
            'password' => bcrypt($password = 'secret'),
        ]);

        $response = $this->from('/backstage/login')->post('/backstage/login', [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect('/backstage/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    /** @test */
    public function aVisitorCannotLoginWithEmailThatDoesNotExist(): void
    {
        $response = $this->from('/backstage/login')->post('/backstage/login', [
            'email' => 'nobody@example.com',
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect('/backstage/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    /** @test */
    public function aUserCanLogout(): void
    {
        $this->signIn();

        $response = $this->from('/backstage')->post('/backstage/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function aVisitorCannotLogoutWhenNotAuthenticated(): void
    {
        $response = $this->post('/backstage/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function aVisitorCannotMakeMoreThanFiveAttemptsInOneMinute(): void
    {
        $user = create(User::class, [
            'password' => bcrypt($password = 'secret'),
        ]);

        foreach (range(0, 5) as $_) {
            $response = $this->from('/backstage/login')->post('/backstage/login', [
                'email' => $user->email,
                'password' => 'invalid-password',
            ]);
        }

        $response->assertRedirect('/backstage/login');
        $response->assertSessionHasErrors('email');
        $this->assertRegExp(
            $this->getTooManyLoginAttemptsMessage(),
            collect(
                $response
                    ->baseResponse
                    ->getSession()
                    ->get('errors')
                    ->getBag('default')
                    ->get('email')
            )->first()
        );
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    protected function getTooManyLoginAttemptsMessage()
    {
        return sprintf('/^%s$/', str_replace('\:seconds', '\d+', preg_quote(__('auth.throttle'), '/')));
    }
}
