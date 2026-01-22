<?php

namespace App\Traits;

use App\Models\Log;
use App\Enums\LogAction;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static $processedLogs = [];

    public static function bootAuditable()
    {
        static::created(fn ($model) => self::handleAudit($model, 'CREATED'));
        static::updated(fn ($model) => self::handleAudit($model, 'UPDATED'));
        static::deleted(fn ($model) => self::handleAudit($model, 'DELETED'));
    }

    protected static function handleAudit($model, $event)
    {
        $modelClass = class_basename($model);
        
        $silentOnCreate = ['Demographic', 'HealthInformation', 'Resident', 'Household'];
        if ($event === 'CREATED' && in_array($modelClass, $silentOnCreate)) {
            return;
        }

        $logTargetId = in_array($modelClass, ['Demographic', 'HealthInformation']) 
            ? $model->resident_id 
            : $model->id;
        
        $cacheKey = "{$modelClass}_{$logTargetId}_{$event}";

        if ($event === 'UPDATED') {
            if (isset(static::$processedLogs[$cacheKey])) return;

            $dirty = $model->getDirty();
            unset(
                $dirty['updated_at'], 
                $dirty['password'], 
                $dirty['remember_token'],
                $dirty['updated_by'],
                $dirty['added_by']
            );

            if (empty($dirty)) return;

            static::$processedLogs[$cacheKey] = true;
            self::logAction($model, $event, $dirty);
        } else {
            if (isset(static::$processedLogs[$cacheKey])) return;
            static::$processedLogs[$cacheKey] = true;
            self::logAction($model, $event);
        }
    }

    protected static function logAction($model, $event, $dirty = [])
    {
        $actor = Auth::user();
        if (!$actor) return;
        
        $actorName = $actor->first_name . ' ' . $actor->last_name; 
        $modelClass = class_basename($model);
        $isChild = in_array($modelClass, ['Demographic', 'HealthInformation']);
        $modelType = $isChild ? 'resident' : strtolower($modelClass);
        
        $targetIdentifier = match(true) {
            $model instanceof \App\Models\User || $model instanceof \App\Models\Resident => $model->first_name . ' ' . $model->last_name,
            $isChild => ($model->resident->first_name ?? 'Unknown') . ' ' . ($model->resident->last_name ?? 'Resident'),
            $model instanceof \App\Models\Household => "Household " . $model->household_number,
            default => 'Record'
        };

        $detail = ($event === 'CREATED') ? "new record" : (($event === 'DELETED') ? "deleted record" : "details");

        if ($event === 'UPDATED') {
            $changes = [];
            foreach ($dirty as $field => $newValue) {
                $oldValue = $model->getOriginal($field);
                
                $old = self::formatValue($field, $oldValue);
                $new = self::formatValue($field, $newValue);
                
                // MAPPING: Convert technical column names to readable words
                $readableField = match($field) {
                    'area_id' => 'street/area',
                    'residency_type_id' => 'ownership status',
                    'house_structure_id' => 'house structure',
                    'barangay_role_id' => 'account role',
                    default => str_replace('_', ' ', $field)
                };

                $changes[] = "{$readableField} ('{$old}' -> '{$new}')";
            }
            $detail = implode(', ', $changes);
        }

        $logTypeString = strtoupper($modelType) . "_{$event}";
        $enumAction = LogAction::tryFrom($logTypeString);

        if ($enumAction) {
            Log::create([
                'user_id' => $actor->id,
                'log_type' => $enumAction,
                'description' => "{$actorName} {$event} {$modelType} {$targetIdentifier}: {$detail}",
                'date' => now(), 
            ]);
        }
    }

    private static function formatValue($field, $value): string
    {
        if (is_null($value)) return 'empty';
        if (is_bool($value)) return $value ? 'Yes' : 'No';

        // 1. AREA ID RESOLUTION: Look up the street name
        if ($field === 'area_id') {
            return \App\Models\AreaStreet::find($value)?->street_name ?? "Area #{$value}";
        }

        // 2. OWNERSHIP STATUS RESOLUTION: Look up residency type
        if ($field === 'residency_type_id') {
            return \App\Models\ResidencyType::find($value)?-> name ?? "Status #{$value}";
        }

        if ($field === 'barangay_role_id') {
            return \App\Models\BarangayRole::find($value)?->name ?? "Role #{$value}";
        }
        
        if ($field === 'house_structure_id') {
            return \App\Models\HouseStructure::find($value)?->house_structure_type ?? "Structure #{$value}";
        }

        $str = (string) $value;
        return strlen($str) > 25 ? substr($str, 0, 22) . '...' : $str;
    }
}