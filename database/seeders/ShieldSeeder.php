<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

use App\Models\User;


class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_applicant","view_any_applicant","create_applicant","update_applicant","delete_applicant","delete_any_applicant","view_player","view_any_player","create_player","update_player","delete_player","delete_any_player","view_team","view_any_team","create_team","update_team","delete_team","delete_any_team","restore_applicant","restore_any_applicant","replicate_applicant","reorder_applicant","force_delete_applicant","force_delete_any_applicant","restore_player","restore_any_player","replicate_player","reorder_player","force_delete_player","force_delete_any_player","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","restore_team","restore_any_team","replicate_team","reorder_team","force_delete_team","force_delete_any_team","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user"]},{"name":"coach","guard_name":"web","permissions":["view_applicant","view_any_applicant","create_applicant","update_applicant","delete_applicant","delete_any_applicant","view_player","view_any_player","create_player","update_player","delete_player","delete_any_player","view_team","view_any_team","restore_applicant","restore_any_applicant","replicate_applicant","reorder_applicant","force_delete_applicant","force_delete_any_applicant","restore_player","restore_any_player","replicate_player","reorder_player","force_delete_player","force_delete_any_player"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'), // Use a hashed password
        ]);

        $user->assignRole('super_admin');

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
