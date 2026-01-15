<?php

namespace App\Traits;

use App\Models\Log;
use App\Enums\LogAction;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    // Tracks processed residents to prevent multiple logs in one request
    protected static $processedResidentLogs = [];

    public static function bootAuditable()
    {
        static::updated(function ($model) {
            self::logAction($model, 'UPDATED');
        });

        static::created(function ($model) {
            // Your existing logic to silence child models on creation
            $silentModels = ['Demographic', 'HealthInformation', 'Resident', 'Household'];
            if (in_array(class_basename($model), $silentModels)) return;

            self::logAction($model, 'CREATED');
        });
    }

    protected static function logAction($model, $event)
    {
        $modelClass = class_basename($model);
        
        // 1. Identify the Resident ID this change belongs to
        $isChild = in_array($modelClass, ['Demographic', 'HealthInformation']);
        $targetResidentId = $isChild ? $model->resident_id : ($modelClass === 'Resident' ? $model->id : null);

        // 2. FORENSIC FIX: If this is a Resident-related update, check if we already logged it
        if ($event === 'UPDATED' && $targetResidentId) {
            if (isset(static::$processedResidentLogs[$targetResidentId])) {
                return; // Exit: Already logged this resident in this request
            }
            static::$processedResidentLogs[$targetResidentId] = true;
        }

        $modelName = $isChild ? 'RESIDENT' : strtoupper($modelClass);
        $actionString = "{$modelName}_{$event}";
        $enumAction = LogAction::tryFrom($actionString);

        if ($enumAction && Auth::check()) {
            Log::create([
                'user_id' => Auth::id(),
                'log_type' => $enumAction,
                'description' => "{$modelName} (ID: {$targetResidentId}) was UPDATED by " . Auth::user()->username,
                'date' => now(),
            ]);
        }
    }
}