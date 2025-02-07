<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
  public function index(): Response
  {
    return Inertia::render('Public/Home/Index', [
      'event' => 'hello world!'
    ]);
  }
}
