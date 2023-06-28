<?php

namespace Tests\Feature\Api\Acl;

use App\Http\Resources\Api\User\PermissionResource;
use App\Http\Resources\Api\User\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class RoleTest extends TestCase
{
    private const JSON_STRUCTURE = [
        'data' => [
            '*' => [
                'id',
                'name',
                'permissions' => [
                    '*' => [
                        'id',
                        'name',
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
    public function test_get_paginate_roles_with_permissions()
    {
        $role = Role::factory()->create([
            'name' => 'admin',
        ]);

        $user = User::factory()->create();
        $permission = Permission::factory(3)->create();

        $role->permissions()->attach($permission->pluck('id'));

        $role->users()->attach($user->id);

        $response = $this->actingAs($user)->getJson(route('roles.index'));

        $response->assertOk();

        $this->assertJson($response->getContent());

        $response->assertJsonStructure();
    }
    public function test_role_resource()
    {
        $role = Role::factory()->create();
        $permission = Permission::factory(3)->create();

        $role->permissions()->attach($permission->pluck('id'));

        $roleResource = new RoleResource($role);

        $this->assertEquals($role->id, $roleResource->id);
        $this->assertEquals([
            'id' => $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name,
            'description' => $role->description,
            'permissions' => PermissionResource::collection($role->permissions),
            'created_at' => $role->created_at,
        ], $roleResource->toArray(request()));
    }
    public function test_permission_resource()
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
    public function test_role_creation_failed_if_user_is_not_admin()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('roles.store'), [
            'name' => 'test',
        ]);

        $response->assertForbidden();
    }
    public function test_create_a_new_role_without_permissions()
    {
        $user = User::factory()->create();

        $admin = Role::factory()->create(['name' => 'admin']);
        $user->roles()->attach($admin);

        $response = $this->actingAs($user)->postJson(route('roles.store'), [
            'name' => 'test',
        ]);

        $response->assertCreated();

        $this->assertJson($response->getContent());
    }
    public function test_index_method_with_no_query_parameters()
    {
        $user = User::factory()->create();

        $admin = Role::factory()->create(['name' => 'admin']);
        $user->roles()->attach($admin);

        $response = $this->actingAs($user)->getJson(route('roles.index'));

        $response->assertOk();

        $this->assertJson($response->getContent());

        $response->assertJsonStructure(self::JSON_STRUCTURE);
    }
    public function test_index_with_term_parameter()
    {
        $user = User::factory()->create();

        $admin = Role::factory()->create(['name' => 'admin']);
        $user->roles()->attach($admin);

        $response = $this->actingAs($user)->getJson(route('roles.index', [
            'term' => 'admin',
        ]));

        $response->assertOk();

        $this->assertJson($response->getContent());

        $response->assertJsonFragment([
            'name' => 'admin',
        ]);

        $response->assertJsonStructure(self::JSON_STRUCTURE);
    }
    public function test_index_with_start_date_parameter()
    {
        $user = User::factory()->create();

        $admin = Role::factory()->create(['name' => 'admin']);
        $user->roles()->attach($admin);

        $response = $this->actingAs($user)->getJson(route('roles.index', [
            'start_date' => now()->subDay()->format('Y-m-d'),
        ]));

        $response->assertOk();

        $this->assertJson($response->getContent());

        $response->assertJsonFragment([
            'name' => 'admin',
        ]);

        $response->assertJsonStructure(self::JSON_STRUCTURE);
    }
    public function test_index_with_end_date_parameter()
    {
        $user = User::factory()->create();

        $admin = Role::factory()->create(['name' => 'admin']);
        $user->roles()->attach($admin);

        $response = $this->actingAs($user)->getJson(route('roles.index', [
            'end_date' => now()->addDay()->format('Y-m-d'),
        ]));

        $response->assertOk();

        $this->assertJson($response->getContent());

        $response->assertJsonFragment([
            'name' => 'admin',
        ]);

        $response->assertJsonStructure(self::JSON_STRUCTURE);
    }
    public function test_index_with_sort_parameter()
    {
        $user = User::factory()->create();

        $admin = Role::factory()->create(['name' => 'admin']);
        $roles = Role::factory(10)->create();

        $user->roles()->attach($admin);

        $response = $this->actingAs($user)->getJson(route('roles.index', [
            'sort' => 'id,desc',
        ]));

        $response->assertOk();

        $this->assertJson($response->getContent());

        $result = $response->json();
        $maxId = $roles->max('id');

        $this->assertEquals($maxId, $result['data'][0]['id']);
        $response->assertJsonStructure(self::JSON_STRUCTURE);
    }
}
