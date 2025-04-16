<?php

namespace App\Http\Controllers\BackOffice\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Administration\Group\CreateRequest;
use App\Http\Requests\BackOffice\Administration\Group\UpdateRequest;
use App\Models\Area;
use App\Models\Group;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Response;

/**
 * Class GroupController
 * @package App\Http\Controllers
 */
class GroupController extends Controller
{
  /**
   * @param Area $area
   * @return Response
   */
  public function create(Area $area): Response
  {
    return inertia('BackOffice/Administration/Group/Create', [
      'area' => $area,
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param Area $area
   * @param CreateRequest $request
   * @return Redirector|Application|RedirectResponse
   */
  public function store(Area $area, CreateRequest $request): Redirector|Application|RedirectResponse
  {
    $validated = $request->validated();
    $validated['area_id'] = $area->id;

    Group::create($validated);

    return redirect(route('areas.index'))
      ->with('success', 'groups.messages.created');
  }

  /**
   * @param Area $area
   * @param Group $group
   * @return Response
   */
  public function show(Area $area, Group $group): Response
  {
    return inertia('BackOffice/Administration/Group/Show', [
      'area' => $area,
      'group' => $group
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param Area $area
   * @param UpdateRequest $request
   * @param Group $group
   * @return Application|RedirectResponse|Redirector
   */
  public function update(Area $area, UpdateRequest $request, Group $group): Redirector|RedirectResponse|Application
  {
    $validated = $request->validated();

    // update area
    $group->update($validated);

    return redirect(route('areas.index'))
      ->with('success', 'groups.messages.updated');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Area $area
   * @return Redirector|RedirectResponse|Application
   */
  public function destroy(Area $area): Redirector|RedirectResponse|Application
  {
    $area->delete();

    return redirect(route('areas.index'))
      ->with('success', 'groups.messages.deleted');
  }

}
