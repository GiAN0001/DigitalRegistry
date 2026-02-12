<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; // Import the Spatie Role
use App\Models\User;                // Import your User model
use Illuminate\Support\Facades\Hash; // Import the Hash facade

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

     
        $superAdminRole = Role::firstOrCreate(['name' => 'super admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $helpDeskRole = Role::firstOrCreate(['name' => 'help desk']);

        $superUser = User::firstOrCreate(
            ['username' => 'superadmin'], //added by GIAN
            [
                'password'         => Hash::make('super123'), 
                'first_name'       => 'System',
                'last_name'        => 'Superuser',
                'email'            => 'superadmin@example.com',
                'contact'          => '0000000000',
                'status'           => 1,
                'barangay_role_id' => 1, 
                'added_by'         => null
            ]
        );
        $superUser->assignRole($superAdminRole);
        
        // --- 2. CREATE YOUR USER ACCOUNTS ---
        // We use firstOrCreate() to find the user by 'username' or create them.

        // -- ADMIN USER --
        $adminUser = User::firstOrCreate(
            ['username' => 'admin'], // Find by username
            [ // Data to use if creating new
                'password' => Hash::make('admin123'), // <-- Change this!
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@example.com',
                'contact' => '1234567890',
                'status' => 1,
                'barangay_role_id' => 1, // Assumes '1' (e.g., Captain) exists
                'added_by' => null
            ]
        );
        // Assign the role (Spatie is smart and won't add duplicates)
        $adminUser->assignRole($adminRole);


        // -- STAFF USER --
        $staffUser = User::firstOrCreate(
            ['username' => 'staff'], // Find by username
            [ // Data to use if creating new
                'password' => Hash::make('staff123'), // <-- Change this!
                'first_name' => 'Staff',
                'last_name' => 'User',
                'email' => 'staff@example.com',
                'contact' => '1234567891',
                'status' => 1,
                'barangay_role_id' => 2, // Assumes '2' (e.g., Secretary) exists
                'added_by' => $adminUser->id // Set the admin as the creator
            ]
        );
        $staffUser->assignRole($staffRole);


        // -- HELP DESK USER --
        $helpDeskUser = User::firstOrCreate(
            ['username' => 'helpdesk'], // Find by username
            [ // Data to use if creating new
                'password' => Hash::make('help123'), // <-- Change this!
                'first_name' => 'Help Desk',
                'last_name' => 'User',
                'email' => 'helpdesk@example.com',
                'contact' => '1234567892',
                'status' => 1,
                'barangay_role_id' => 3, // Assumes '3' (e.g., Treasurer) exists
                'added_by' => $adminUser->id // Set the admin as the creator
            ]
        );
        $helpDeskUser->assignRole($helpDeskRole);
    }
}