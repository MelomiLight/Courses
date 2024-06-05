<?php

namespace App\Http\Controllers\API;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class VerifyEmailController extends Controller
{
    public function verifyEmail(Request $request): RedirectResponse
    {

        $userId = $request->route('id');
        $hash = $request->route('hash');


        $user = User::find($userId);


        if (!$user) {
            return redirect('/')->with('message', 'User not found');
        }


        if (!hash_equals((string)$hash, sha1($user->getEmailForVerification()))) {
            return redirect('/')->with('message', 'Invalid verification link');
        }


        if ($user->hasVerifiedEmail()) {
            return redirect('/')->with('message', 'Email already verified');
        }


        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect('/')->with('message', 'Email successfully verified');
    }

    public function resendEmail(Request $request): RedirectResponse
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);


        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return back()->with('message', 'Email already verified');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    }
}
