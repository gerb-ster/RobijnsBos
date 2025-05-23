<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
  /**
   * The root template that's loaded on the first page visit.
   *
   * @see https://inertiajs.com/server-side-setup#root-template
   * @var string
   */
  protected $rootView = 'app';

  /**
   * Determines the current asset version.
   *
   * @see https://inertiajs.com/asset-versioning
   * @param Request $request
   * @return string|null
   */
  public function version(Request $request): ?string
  {
    return parent::version($request);
  }

  /**
   * Defines the props that are shared by default.
   *
   * @see https://inertiajs.com/shared-data
   * @param Request $request
   * @return array
   */
  public function share(Request $request): array
  {
    return array_merge(parent::share($request), [
      'auth' => function () use ($request) {
        return [
          'user' => $request->user() ? [
            'id' => $request->user()->id,
            'uuid' => $request->user()->uuid,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'locale' => $request->user()->locale,
            'canAdministrate' => $request->user()->can('administrate'),
            'canAccessBackOffice' => $request->user()->can('accessBackOffice'),
          ] : null,
        ];
      },
      'flash' => function () use ($request) {
        return [
          'success' => $request->session()->get('success'),
          'warning' => $request->session()->get('warning'),
          'error' => $request->session()->get('error'),
        ];
      },
    ]);
  }
}
