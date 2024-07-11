<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\AppRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seede
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        $user = User::findOrFail(1);
//        $role = 3;
//        $role = Role::findById($role, 'api');
//
//        $user->syncRoles($role);

//        $role = AppRole::create([
//            'name' => 'merchant',
//            'display_name' => 'Merchant',
//            'guard_name' => 'api',
//            'is_default' => 1,
//            'created_by' => 1,
//            'updated_by' => 1,
//        ]);

//        $role = Role::find(3); // super admin
//        $role = Role::find(6); // merchant


        $permissions = [
//            'view-permissions',
//
//            'create-roles',
//            'update-roles',
//            'delete-roles',
//            'view-roles',
//
//            'create-admins',
//            'update-admins',
//            'delete-admins',
//            'view-admins',
//            'view-customers',
//            'create-merchant-users',
//            'update-merchant-users',
//            'delete-merchant-users',
//            'view-merchant-users',
//            'view-riders',
//            'view-riders-online',
//            'view-riders-on-trip',
//            'view-riders-analysis',
//
//            'create-settlements',
//            'view-rider-settlements',
//            'view-merchant-settlements',
//
//            'create-merchants',
//            'update-merchants',
//            'delete-merchants',
//            'view-merchants',
//            'view-merchants-analysis',
//
//            'create-blogs',
//            'update-blogs',
//            'delete-blogs',
//            'view-blogs',
//
//            'view-activities',
//
//            'view-package-transactions',
//            'view-rentals-transactions',
//            'view-movers-transactions',
//
//            'create-type-settings',
//            'update-type-settings',
//            'delete-type-settings',
//            'view-type-settings',
//
//            'create-settings',
//            'update-settings',
//            'delete-settings',
//            'view-settings',
//
//            'create-sizes',
//            'update-sizes',
//            'delete-sizes',
//            'view-sizes',
//
//            'create-vehicle-types',
//            'update-vehicle-types',
//            'delete-vehicle-types',
//            'view-vehicle-types',
//
//            'create-delivery-types',
//            'update-delivery-types',
//            'delete-delivery-types',
//            'view-delivery-types',
//
//            'assign-rental-orders',
//            'reject-rental-orders',
//            'complete-rental-orders',
//            'view-rental-orders',
//
//            'create-rentals-vehicles',
//            'update-rentals-vehicles',
//            'delete-rentals-vehicles',
//            'view-rentals-vehicles',
//
//            'view-movers-orders',
//            'view-packages-orders',
//            'view-packages',
//
//            'approve-riders-vehicles',
//            'reject-riders-vehicles',
//            'delete-riders-vehicles',
//            'view-riders-vehicles',
//
//            'approve-driver-licenses',
//            'reject-driver-licenses',
//            'delete-driver-licenses',
//            'view-driver-licenses',
//
//            'approve-citizenship',
//            'reject-citizenship',
//            'delete-citizenship',
//            'view-citizenship',
        ];

        $permissions = [
//            'assign-rental-orders',
//            'reject-rental-orders',
//            'complete-rental-orders',
//            'view-rental-orders',
//
//            'create-rentals-vehicles',
//            'update-rentals-vehicles',
//            'delete-rentals-vehicles',
//            'view-rentals-vehicles',
        ];


//        foreach ($permissions as $permission) {
//            Permission::create([
//                'name' => $permission,
//                'display_name' => ucwords(str_replace("-", " ", $permission)),
//                'guard_name' => 'api',
//                'created_by' => 1,
//                'updated_by' => 1,
//            ]);
//        }

//        $role->givePermissionTo([$permissions]);

    }
}
