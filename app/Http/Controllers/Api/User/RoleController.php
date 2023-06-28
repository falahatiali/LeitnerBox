<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RoleCreateRequest;
use App\Http\Requests\User\RoleUpdateRequest;
use App\Http\Resources\Api\User\RoleResource;
use App\Http\Resources\Api\User\RoleResourceCollection;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Eloquent\Criteria\EndDate;
use App\Repositories\Eloquent\Criteria\SearchTerm;
use App\Repositories\Eloquent\Criteria\StartDate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct(private readonly RoleRepositoryInterface $roleRepository)
    {
    }

    public function index(Request $request): RoleResourceCollection
    {
        $query = $this->roleRepository->with(['permissions']);

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

        $roles = $query->paginate($request->input('per_page', 10));

        return new RoleResourceCollection($roles);
    }

    public function store(RoleCreateRequest $request): RoleResource
    {
        $role = $this->roleRepository->create($request->only(['name', 'description']));

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return new RoleResource($role);
    }

    public function update(RoleUpdateRequest $request, Role $role): RoleResource
    {
        $role->update($request->only(['name', 'description']));

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return new RoleResource($role);
    }

    public function destroy(Role $role)
    {
        if ($role->permissions()->count()) {
            return new ApiErrorResponse(
                'This role has permissions, please remove them first',
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($role->users()->count()) {
            return new ApiErrorResponse(
                'This role is assigned to users, ',
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $role->delete();

        return new ApiSuccessResponse([], [
            'message' => 'Role deleted successfully'
        ], Response::HTTP_NO_CONTENT);
    }
}
