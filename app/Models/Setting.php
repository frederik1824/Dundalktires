<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['branch_id', 'key', 'value', 'type'];

    public static function get(string $key, mixed $default = null, ?int $branchId = null): mixed
    {
        // Support callers that pass branch_id as 2nd argument: Setting::get($key, $branchId)
        if ($branchId === null && is_int($default)) {
            $branchId = $default;
            $default = null;
        }

        $cacheKey = 'setting.'.($branchId ?? 'global').".{$key}";

        $row = Cache::remember($cacheKey, 3600, function () use ($key, $branchId) {
            if ($branchId !== null) {
                $m = static::where('key', $key)->where('branch_id', $branchId)->first();
                if ($m) {
                    return ['value' => $m->value, 'type' => $m->type];
                }
            }

            $global = static::where('key', $key)->whereNull('branch_id')->first();

            return $global ? ['value' => $global->value, 'type' => $global->type] : null;
        });

        if (! $row) {
            return $default;
        }

        return match ($row['type']) {
            'boolean' => filter_var($row['value'], FILTER_VALIDATE_BOOLEAN),
            'decimal', 'float' => (float) $row['value'],
            'integer' => (int) $row['value'],
            'json' => json_decode($row['value'], true),
            default => $row['value'],
        };
    }

    public static function set(string $key, mixed $value, string $type = 'string', ?int $branchId = null): void
    {
        static::updateOrCreate(
            ['key' => $key, 'branch_id' => $branchId],
            ['value' => is_array($value) ? json_encode($value) : (string) $value, 'type' => $type]
        );

        Cache::forget('setting.'.($branchId ?? 'global').".{$key}");
        Cache::forget("setting.{$branchId}.{$key}");
    }
}
