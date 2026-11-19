<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class RolesAndPermissions extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static \UnitEnum|string|null $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Roles and Permissions';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Roles and Permissions';

    protected string $view = 'filament.pages.roles-and-permissions';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->canModule('system');
    }

    public function getViewData(): array
    {
        $modules = [
            'properties' => 'Properties & Units',
            'tenancy' => 'Tenants & Leases',
            'finance' => 'Financial Management',
            'operations' => 'Operations',
            'system' => 'System Management',
        ];

        $roles = [
            User::ROLE_ADMIN => ['label' => 'Administrator', 'color' => 'amber', 'desc' => 'Full access to every module and system settings.'],
            User::ROLE_DEVELOPER => ['label' => 'Developer', 'color' => 'violet', 'desc' => 'Full technical access, including system management.'],
            User::ROLE_OWNER => ['label' => 'Owner', 'color' => 'emerald', 'desc' => 'Portfolio oversight and financial visibility.'],
            User::ROLE_PROPERTY_MANAGER => ['label' => 'Property Manager', 'color' => 'sky', 'desc' => 'Day-to-day property, tenancy, finance and operations.'],
            User::ROLE_ACCOUNTANT => ['label' => 'Accountant', 'color' => 'blue', 'desc' => 'Payments, expenses, M-Pesa and financial reports.'],
            User::ROLE_AGENT => ['label' => 'Agent', 'color' => 'teal', 'desc' => 'Properties, tenants and operations.'],
            User::ROLE_CARETAKER => ['label' => 'Caretaker', 'color' => 'orange', 'desc' => 'Maintenance, move-outs and property visibility.'],
            User::ROLE_TENANT => ['label' => 'Tenant', 'color' => 'gray', 'desc' => 'No access to the administration panel.'],
        ];

        $rows = [];
        foreach ($roles as $role => $meta) {
            $granted = User::ROLE_MODULES[$role] ?? [];
            $rows[] = [
                'role' => $role,
                'label' => $meta['label'],
                'color' => $meta['color'],
                'desc' => $meta['desc'],
                'users' => User::where('role', $role)->count(),
                'access' => collect($modules)
                    ->map(fn ($label, $key) => in_array('*', $granted, true) || in_array($key, $granted, true))
                    ->toArray(),
                'panel' => $role !== User::ROLE_TENANT,
            ];
        }

        return [
            'modules' => $modules,
            'rows' => $rows,
        ];
    }
}
