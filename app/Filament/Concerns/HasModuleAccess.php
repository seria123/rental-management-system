<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Grants or denies a resource based on the current user's module access.
 *
 * A resource declares which module it belongs to via:
 *     protected static string $accessModule = 'finance';
 */
trait HasModuleAccess
{
    protected static function accessModule(): string
    {
        return property_exists(static::class, 'accessModule')
            ? static::$accessModule
            : 'system';
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->canModule(static::accessModule());
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }
}
