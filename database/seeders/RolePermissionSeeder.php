<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Booking
            'booking.view',
            'booking.confirm',
            'booking.cancel',
            
            // Venue
            'venue.view',
            'venue.create',
            'venue.update',
            'venue.delete',
            
            // Refund
            'refund.view',
            'refund.approve',
            'refund.execute',
            
            // Financial
            'finance.view',
            'finance.export',
            'settlement.create',
            
            // System
            'user.view',
            'user.manage',
            'audit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions(Permission::all());

        $operator = Role::firstOrCreate(['name' => 'admin-operator']);
        $operator->syncPermissions([
            'booking.view', 'booking.confirm', 'booking.cancel',
            'venue.view', 'venue.create', 'venue.update',
            'refund.view',
        ]);

        $finance = Role::firstOrCreate(['name' => 'admin-finance']);
        $finance->syncPermissions([
            'booking.view',
            'refund.view', 'refund.approve', 'refund.execute',
            'finance.view', 'finance.export', 'settlement.create',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'admin-viewer']);
        $viewer->syncPermissions([
            'booking.view', 'venue.view', 'refund.view', 'finance.view', 'audit.view',
        ]);
    }
}
