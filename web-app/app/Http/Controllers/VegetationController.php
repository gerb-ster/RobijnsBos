<?php

namespace App\Http\Controllers;

use App\Models\Vegetation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class VegetationController extends Controller
{
  /**
   * @param string $shortCode
   * @return Redirector|RedirectResponse|Application
   */
  public function redirect(string $shortCode): Redirector|RedirectResponse|Application
  {
    $vegetation = Vegetation::where('qr_shortcode', $shortCode)->firstOrFail();
    return redirect(route('public.vegetation.show', $vegetation->uuid));
  }

  /**
   * @param Vegetation $vegetation
   * @return Response
   */
  public function show(Vegetation $vegetation): Response
  {
    $vegetation->load('species');

    return Inertia::render('Public/Vegetation/Show', [
      'vegetation' => $vegetation
    ]);
  }
}
