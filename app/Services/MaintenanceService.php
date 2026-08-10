<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class MaintenanceService
{
    private static function getFilePath(): string
    {
        return storage_path('framework/maintenance_mode_enabled');
    }

    /**
     * Check if maintenance mode is active.
     */
    public static function isEnabled(): bool
    {
        if (file_exists(self::getFilePath())) {
            return true;
        }

        return (bool) Cache::get('maintenance_mode_enabled', false);
    }

    /**
     * Enable maintenance mode.
     */
    public static function enable(): void
    {
        @file_put_contents(self::getFilePath(), json_encode([
            'enabled_at' => now()->toIso8601String(),
        ]));
        Cache::forever('maintenance_mode_enabled', true);
    }

    /**
     * Disable maintenance mode.
     */
    public static function disable(): void
    {
        $file = self::getFilePath();
        if (file_exists($file)) {
            @unlink($file);
        }
        Cache::forget('maintenance_mode_enabled');
    }

    /**
     * Toggle maintenance mode state.
     */
    public static function toggle(): bool
    {
        if (self::isEnabled()) {
            self::disable();
            return false;
        } else {
            self::enable();
            return true;
        }
    }
}
