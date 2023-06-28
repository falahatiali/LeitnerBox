<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PermissionCreateRequest;
use App\Http\Requests\User\PermissionUpdateRequest;
use App\Http\Resources\Api\User\PermissionResource;
use App\Http\Resources\Api\User\PermissionResourceCollection;
use App\Http\Responses\ApiErrorResponse;
use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Eloquent\Criteria\EndDate;
use App\Repositories\Eloquent\Criteria\SearchTerm;
use App\Repositories\Eloquent\Criteria\StartDate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function __construct(private readonly PermissionRepositoryInterface $permissionRepository)
    {
    }

    public function index(Request $request)
    {
        $query = $this->permissionRepository->with(['roles']);

        if ($request->has('term')) {
            $search = new SearchTerm();
            $search->setParams($request->input('term'));

            $query->withCriteria([
                $search
            ]);
        }

        if ($request->has('start_date')) {
            $startDate = new StartDate();
            $startDate->setParams($request->input('start_date'));

            $query->withCriteria([
                $startDate
            ]);
        }

        if ($request->has('end_date')) {
            $endDate = new EndDate();
            $endDate->setParams($request->input('end_date'));

            $query->withCriteria([
                $endDate
            ]);
        }

        if ($request->has('sort')) {
            $column = $request->input('sort');
            if (Str::contains($column, ',')) {
                list($column, $direction) = explode(',', $column);
            }

            $query = $query->sort($column, $direction ?? 'asc');
        }

        $permissions = $query->paginate($request->input('per_page', 10));

        return new PermissionResourceCollection($permissions);
    }

    public function store(PermissionCreateRequest $request)
    {
        $permission = $request->user()->permissions()->create($request->validated());

        if ($request->has('roles')) {
            $permission->roles()->attach($request->input('roles'));
        }

        return new PermissionResource($permission);
    }

    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        $permission->update($request->validated());

        if ($request->has('roles')) {
            $permission->roles()->sync($request->input('roles'));
        }

        return new PermissionResource($permission);
    }

    public function destroy(Permission $permission)
    {
        if ($permission->roles()->count() > 0) {
            return new ApiErrorResponse(
                'This permission has roles attached to it. Please remove the roles first.',
                422);
        }

        $permission->delete();

        return response()->noContent();
    }
}
