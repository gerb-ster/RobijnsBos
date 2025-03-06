<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use Inertia\Response;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class PlantController extends Controller
{
  /**
   * @return Response
   */
  public function index(): Response
  {
    return inertia('BackOffice/Plant/Index');
  }
}
