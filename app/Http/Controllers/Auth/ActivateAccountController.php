<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\TbPasswordStrenght1;
use Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivateAccountController extends Controller
{
    use AuthenticatesUsers;

    public function index($token)
    {
        // Find the user
        $user = User::where('ott', $token)->first();

        // If user not found return error
        if ($user === null) {
            abort(404);
        }

        // Show active account screen
        return view('auth.activate', [
            'user' => $user,
        ]);
    }

    public function update($token)
    {
        // Password validation
        $data = request()->validate([
            'email' => 'required|email',
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Try to find the user
        $user = User::where('ott', $token)->first();

        // If user not found return error
        if ($user === null) {
            abort(404);
        }

        // Password save
        $user->update([
            'password' => bcrypt($data['password']),
            'ott' => null,
        ]);

        // Login user
        auth()->login($user);

        // Set the message
        session()->flash('success', 'Your account has been activated!');

        // Authentication passed...
        return redirect()->route('backstage.dashboard.index');
    }
}
