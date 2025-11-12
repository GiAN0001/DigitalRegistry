<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- Import the DB facade
use Illuminate\Support\Carbon; // <-- Import Carbon for timestamps

class LookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Fill `barangay_roles` (Job Titles)
        DB::table('barangay_roles')->insert([
            ['name' => 'Barangay Captain', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Barangay Secretary', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Barangay Treasurer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kagawad', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'SK Chairperson', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 2. Fill `area_streets` (Puroks/Streets)
        DB::table('area_streets')->insert([
            ['purok_name' => 'J. RIZAL', 'street_name' => 'J. RIZAL', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Macalintal Compound', 'street_name' => 'J. RIZAL', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Sunny Ridge Residences', 'street_name' => 'J. RIZAL', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'RJP Compound', 'street_name' => 'J. RIZAL', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'C. CASTANEDA', 'street_name' => 'C. CASTANEDA', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'DONA BASILISA YANGCO', 'street_name' => 'DONA BASILISA YANGCO', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => '1ST ST, CASTANEDA', 'street_name' => '1ST ST, CASTANEDA', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => '2ND ST, CASTANEDA', 'street_name' => '2ND ST, CASTANEDA', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => '3RD ST, CASTANEDA', 'street_name' => '3RD ST, CASTANEDA', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => '4TH ST, CASTANEDA', 'street_name' => '4TH ST, CASTANEDA', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => '5TH ST, CASTANEDA', 'street_name' => '5TH ST, CASTANEDA', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'GK CASTANEDA', 'street_name' => 'GK CASTANEDA', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'GK MARTHA', 'street_name' => 'GK MARTHA', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'CIRCLE', 'street_name' => 'CIRCLE', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'GENEROSO PASCUAL', 'street_name' => 'GENEROSO PASCUAL', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'ALLEY 1', 'street_name' => 'ALLEY 1', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'ALLEY 2', 'street_name' => 'ALLEY 2', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'ALLEY 3', 'street_name' => 'ALLEY 3', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'ALLEY 4', 'street_name' => 'ALLEY 4', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'ALLEY 5', 'street_name' => 'ALLEY 5', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'ALLEY 6', 'street_name' => 'ALLEY 6', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'ALLEY 7', 'street_name' => 'ALLEY 7', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'ALLEY 8', 'street_name' => 'ALLEY 8', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Dreamland Subdivision', 'street_name' => 'JUPITER ST.', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Dreamland Subdivision', 'street_name' => 'MARS ST.', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Dreamland Subdivision', 'street_name' => 'NEPTUNE ST.', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Dreamland Subdivision', 'street_name' => 'SATURN ST.', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Dreamland Subdivision', 'street_name' => 'URANUS ST.', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Dreamland Subdivision', 'street_name' => 'MERCURY ST.', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Dreamland Subdivision', 'street_name' => 'VENUS ST.', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Merryland Village', 'street_name' => 'SAN PEDRO ST.', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Wonderland Townhomes', 'street_name' => 'SUNFLOWER ST.', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Wonderland Townhomes', 'street_name' => 'YELLOW BELL ST.', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Wonderland Townhomes', 'street_name' => 'SAMPAGUITA ST.', 'created_at' => $now, 'updated_at' => $now],
            ['purok_name' => 'Wonderland Townhomes', 'street_name' => 'ORCHID ST.', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. Fill `house_structures`
        DB::table('house_structures')->insert([
            ['house_structure_type' => 'Stone', 'created_at' => $now, 'updated_at' => $now],
            ['house_structure_type' => 'Wood', 'created_at' => $now, 'updated_at' => $now],
            ['house_structure_type' => 'Mostly Stone - Partly Wood', 'created_at' => $now, 'updated_at' => $now],
            ['house_structure_type' => 'Mostly Wood - Partly Stone', 'created_at' => $now, 'updated_at' => $now],
            ['house_structure_type' => 'Other', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 4. Fill `household_roles` (Role in the family)
        DB::table('household_roles')->insert([
            ['name' => 'Head', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Spouse', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Child', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Parent', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sibling', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Grandparent', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Other', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 5. Fill `residency_types`
        DB::table('residency_types')->insert([
            ['name' => 'Owner', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tenant - Owner', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tenant - Sharer', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 6. Fill `pet_types`
        DB::table('pet_types')->insert([
            ['name' => 'Dog', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cat', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bird', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fish', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Other', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 7. Fill `document_types`
        DB::table('document_types')->insert([
            ['name' => 'Barangay Clearance', 'default_fee' => 50.00, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Certificate of Indigency', 'default_fee' => 0.00, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Certificate of Residency', 'default_fee' => 0.00, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cedula', 'default_fee' => 20.00, 'created_at' => $now, 'updated_at' => $now],
        ]);
        
        // 8. Fill `facilities`
        DB::table('facilities')->insert([
            ['facility_type' => 'Covered Court', 'non_resident_rate' => 500.00, 'created_at' => $now, 'updated_at' => $now],
        ]);
        
        // 9. Fill `equipments`
        DB::table('equipments')->insert([
            ['equipment_type' => 'Plastic Chair', 'total_quantity' => 100, 'created_at' => $now, 'updated_at' => $now],
            ['equipment_type' => 'Folding Table', 'total_quantity' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['equipment_type' => 'Tent (10x10)', 'total_quantity' => 5, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}