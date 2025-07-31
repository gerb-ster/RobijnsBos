<?php

namespace App\Http\Controllers\BackOffice\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Auth\LoginRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
  /**
   * Display the login view.
   */
  public function create(): Response
  {
    return Inertia::render('Public/Auth/Login');
  }

  /**
   * Handle an incoming authentication request.
   * @throws ValidationException
   */
  public function store(LoginRequest $request): RedirectResponse
  {
    $request->authenticate();

    $request->session()->regenerate();

    if(Auth::user()->role_id === Role::VOLUNTEER) {
      return redirect()->intended(route('public.vegetation.map'));
    }

    return redirect()->intended(route('vegetation.index'));
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
