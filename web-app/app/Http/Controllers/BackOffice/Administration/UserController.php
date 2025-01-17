<?php

namespace app\Http\Controllers\BackOffice\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @param int $page
     * @param int $itemsPerPage
     * @param array $sortBy
     * @param string|null $search
     * @param bool $withTrashed
     * @return array
     */
    private function listUsers(
        int $page,
        int $itemsPerPage,
        array $sortBy,
        ?string $search,
        bool $withTrashed
    ): array
    {
        $queryBuilder = new User();

        if ($withTrashed) {
            $queryBuilder->withTrashed();
        }

        if (!empty($sortBy)) {
            // these joins are only needed for sorting
            foreach ($sortBy as $sortByRule) {
                $queryBuilder->orderBy($sortByRule['key'], $sortByRule['order']);
            }
        }

        if (!empty($search)) {
            $queryBuilder->where('name','LIKE', "%$search%")
                ->orWhere('email','LIKE', "%$search%");
        }

        // do a count
        $countBeforePaging = $queryBuilder->count();

        // now set limit
        $queryBuilder->limit($itemsPerPage)->offset(($page - 1) * $itemsPerPage);

        return [
            'items' => $queryBuilder->get()->toArray(),
            'total' => $countBeforePaging
        ];
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        return inertia('BackOffice/User/Index');
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $withTrashed = $request->boolean('withTrashed');
        $page = $request->integer('page');
        $itemsPerPage = $request->integer('itemsPerPage');
        $sortBy = $request->post('sortBy');
        $search = $request->post('search');

        return response()->json(
            $this->listUsers($page, $itemsPerPage, $sortBy, $search, $withTrashed)
        );
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        return inertia('BackOffice/User/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserUpdateRequest $request
     * @return Redirector|Application|RedirectResponse
     */
    public function store(UserUpdateRequest $request): Redirector|Application|RedirectResponse
    {
        $validated = $request->validated();

        $validated['admin'] = $request->boolean('admin');

        $user = User::create($validated);

        // sync sections
        if (array_key_exists('sections', $validated) && !empty($validated['sections'])) {
            $user->sections()->sync($validated['sections']);
        }

        return redirect(route('user.index'))
            ->with('success', 'users.messages.created');
    }

    /**
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return inertia('BackOffice/User/Show', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return Application|RedirectResponse|Redirector
     */
    public function update(UserUpdateRequest $request, User $user): Redirector|RedirectResponse|Application
    {
        $validated = $request->validated();

        $validated['admin'] = $request->boolean('admin');
        $validated['inactive'] = $request->boolean('inactive');

        // update user
        $user->update($validated);

        return redirect(route('user.index'))
            ->with('success', 'users.messages.updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Redirector|RedirectResponse|Application
     */
    public function destroy(User $user): Redirector|RedirectResponse|Application
    {
        if ($user->id === Auth::user()->id) {
            return redirect(route('user.index'))
                ->with('warning', 'users.messages.cannotDeleteYourself');
        }

        $user->delete();

        return redirect(route('user.index'))
            ->with('success', 'users.messages.deleted');
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param int $userId
     * @return Redirector|RedirectResponse|Application
     */
    public function restore(int $userId): Redirector|RedirectResponse|Application
    {
        User::withTrashed()->find($userId)->restore();

        return redirect(route('user.index'))
            ->with('success', 'users.messages.restored');
    }

    /**
     * Set the new Locale
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setLocale(Request $request): JsonResponse
    {
        $locale = $request->post('locale');

        Auth::user()->update([
           'locale' =>$locale
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}
