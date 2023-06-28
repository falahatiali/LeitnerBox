<?php

namespace Tests\Feature\Api\Acl;

use App\Http\Resources\Api\User\PermissionResource;
use App\Http\Resources\Api\User\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    private const JSON_STRUCTURE = [
        'data' => [
            '*' => [
                'id',
                'name',
                'roles' => [
                    '*' => [
                        'id',
                        'name',
                        'display_name',
                        'description',
                        'created_at',
                    ],
                ],
                'created_at',
            ],
        ],
        'links',
        'meta',
    ];

    public function test_get_paginate_permissions_with_roles()
    {
        $user = User::factory()->create();

        $admin = Role::factory()->create(['name' => 'admin']);
        $user->roles()->attach($admin);

        $permission = Permission::factory(4)->create();

        $admin->permissions()->attach($permission->pluck('id'));

        $response = $this->actingAs($user)->getJson(route('permissions.index'));

        $response->assertOk();
        $response->assertJsonFragment([
            'name' => $permission->first()->name,
        ]);

        $response->assertJsonStructure(self::JSON_STRUCTURE);
    }
    public function test_permissions_resource()
    {
        // Create a permission
        $permission = Permission::factory()->create();

        $permissionResource = new PermissionResource($permission);

        $this->assertEquals([
            'id' => $permission->id,
            'name' => $permission->name,
            'description' => $permission->description,
            'roles' => RoleResource::collection($permission->roles),
            'created_at' => $permission->created_at,
        ], $permissionResource->toArray(request()));
    }
    public function test_permission_creation_failed_if_user_is_not_admin()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('permissions.store'), [
            'name' => 'test',
        ]);

        $response->assertForbidden();
    }
    public function test_create_a_new_permission_without_roles()
    {
        $user = User::factory()->create();

        $admin = Role::factory()->create(['name' => 'admin']);

        $user->roles()->attach($admin);

        $response = $this->actingAs($user)->postJson(route('permissions.store'), [
            'name' => 'test',
        ]);

        $response->assertCreated();

        $response->assertJsonFragment([
            'name' => 'test',
        ]);
    }
}
