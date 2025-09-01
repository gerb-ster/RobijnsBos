<?php

namespace App\Http\Controllers\BackOffice\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Auth\LoginRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
  /**
   * Handle an incoming authentication request.
   * @throws ValidationException
   */
  public function store(LoginRequest $request): RedirectResponse
  {
    $request->authenticate();

    $request->session()->regenerate();

    return redirect()->intended(route('public.vegetation.map'));
  }

  /**
   * @param Request $request
   * @return RedirectResponse
   */
  public function forgotPassword(Request $request): RedirectResponse
  {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
      $request->only('email')
    );

    return $status === Password::ResetLinkSent
      ? back()->with(['status' => __($status)])
      : back()->withErrors(['email' => __($status)]);
  }

  /**
   * @param Request $request
   * @return RedirectResponse
   */
  public function resetPassword(Request $request): RedirectResponse
  {
    $request->validate([
      'token' => 'required',
      'email' => 'required|email',
      'password' => 'required|confirmed',
    ]);

    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function (User $user, string $password) {
        $user->forceFill([
          'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));
      }
    );

    return $status === Password::PasswordReset
      ? redirect()->route('login')->with('status', __($status))
      : back()->withErrors(['email' => [__($status)]]);
  }

  /**
   * Destroy an authenticated session.
   */
  public function destroy(Request $request): RedirectResponse
  {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect(route('public.vegetation.map'));
  }
}
