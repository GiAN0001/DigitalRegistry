<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Resident;
use Illuminate\Support\Str;

class AssignGlobalIdsToResidents extends Command
{
    protected $signature = 'app:assign-global-ids';
    protected $description = 'Assigns a unique global_id to all existing residents matching by identity fingerprint across cycles';

    public function handle()
    {
        $this->info('Assigning global IDs to residents...');
        $residents = Resident::with('demographic')->get();
        
        $fingerprints = [];
        $updatedCount = 0;

        foreach ($residents as $resident) {
            if ($resident->global_id) {
                continue;
            }

            // Fingerprint includes First Name, Last Name, and Birthdate
            $firstName = strtolower(trim($resident->first_name));
            $lastName = strtolower(trim($resident->last_name));
            $birthdate = $resident->demographic ? $resident->demographic->birthdate : 'unknown';
            $birthplace = $resident->demographic ? $resident->demographic->birthplace : 'unknown';

            $fingerprint = md5("{$firstName}|{$lastName}|{$birthdate}|{$birthplace}");

            if (!isset($fingerprints[$fingerprint])) {
                $fingerprints[$fingerprint] = (string) Str::uuid();
            }

            $resident->global_id = $fingerprints[$fingerprint];
            $resident->save();
            $updatedCount++;
        }

        $this->info("Successfully assigned global_id to {$updatedCount} residents!");
    }
}
