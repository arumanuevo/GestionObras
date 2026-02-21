<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $request->route('id'), (string) $user->getKey())) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return redirect($this->redirectPath())->with('verified', true);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Verificar si el usuario está aprobado
        if (!$user->approved) {
            // No autenticamos al usuario si no está aprobado
            return redirect()->route('unapproved');
        }

        // Solo autenticamos al usuario si está aprobado
        Auth::login($user, true);

        return redirect($this->redirectPath())->with('verified', true);
    }
}
