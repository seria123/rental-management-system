<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_PROPERTY_MANAGER = 'property_manager';

    public const ROLE_ACCOUNTANT = 'accountant';

    public const ROLE_CARETAKER = 'caretaker';

    public const ROLE_TENANT = 'tenant';

    public const ROLE_DEVELOPER = 'developer';

    public const ROLE_OWNER = 'owner';

    public const ROLE_AGENT = 'agent';

    /**
     * Roles allowed to sign in to the administration panel.
     * Filament is restricted to administrators only.
     */
    public const STAFF_ROLES = [
        self::ROLE_ADMIN,
    ];

    /**
     * Module access map. `*` grants every module.
     *
     * Modules: properties, tenancy, finance, operations, system
     */
    public const ROLE_MODULES = [
        self::ROLE_ADMIN => ['*'],
        self::ROLE_DEVELOPER => ['*'],
        self::ROLE_OWNER => ['properties', 'tenancy', 'finance', 'operations'],
        self::ROLE_AGENT => ['properties', 'tenancy', 'operations'],
        self::ROLE_PROPERTY_MANAGER => ['properties', 'tenancy', 'finance', 'operations'],
        self::ROLE_ACCOUNTANT => ['finance', 'tenancy'],
        self::ROLE_CARETAKER => ['operations', 'properties'],
        self::ROLE_TENANT => [],
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's notification preferences.
     */
    public function notificationPreferences(): HasOne
    {
        return $this->hasOne(UserNotificationPreference::class);
    }

    /**
     * Get the user's two-factor settings.
     */
    public function twoFactorSettings(): HasOne
    {
        return $this->hasOne(UserTwoFactorSetting::class);
    }

    /**
     * Get the user's active sessions.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get the user's activity logs.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(UserActivityLog::class);
    }

    /**
     * Get the user's tenant record (if the user is a tenant).
     */
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    public function isRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array|string $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($this->role, $roles, true);
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Check if user can access system settings.
     */
    public function canAccessSettings(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_OWNER]);
    }

    /**
     * Check if user can access the admin panel (Filament).
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->status !== 'inactive'
            && $this->isRole(self::ROLE_ADMIN);
    }

    /**
     * Check if the user is staff (any non-tenant staff role).
     */
    public function isStaff(): bool
    {
        return $this->canAccessAdminPanel();
    }

    /**
     * Check whether the user may access a panel module.
     */
    public function canModule(string $module): bool
    {
        if (! $this->canAccessAdminPanel()) {
            return false;
        }

        $modules = self::ROLE_MODULES[$this->role] ?? [];

        return in_array('*', $modules, true) || in_array($module, $modules, true);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->canAccessAdminPanel();
    }

    /**
     * Check if user is management staff (admin, owner, agent, caretaker, property_manager).
     */
    public function isManagement(): bool
    {
        return $this->hasAnyRole([
            self::ROLE_ADMIN,
            self::ROLE_OWNER,
            self::ROLE_AGENT,
            self::ROLE_CARETAKER,
            self::ROLE_PROPERTY_MANAGER,
        ]);
    }

    /**
     * Check if user can manage properties (admin, owner, property_manager).
     */
    public function canManageProperties(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_OWNER, self::ROLE_PROPERTY_MANAGER]);
    }

    /**
     * Check if user can manage units (admin, owner, property_manager, agent).
     */
    public function canManageUnits(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_OWNER, self::ROLE_PROPERTY_MANAGER, self::ROLE_AGENT]);
    }

    /**
     * Check if user can manage tenants (admin, owner, property_manager, agent).
     */
    public function canManageTenants(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_OWNER, self::ROLE_PROPERTY_MANAGER, self::ROLE_AGENT]);
    }

    /**
     * Check if user can view/manage payments (admin, owner, property_manager, accountant).
     */
    public function canManagePayments(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_OWNER, self::ROLE_PROPERTY_MANAGER, self::ROLE_ACCOUNTANT]);
    }

    /**
     * Check if user can manage maintenance (admin, owner, property_manager, caretaker).
     */
    public function canManageMaintenance(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_OWNER, self::ROLE_PROPERTY_MANAGER, self::ROLE_CARETAKER]);
    }

    /**
     * Check if user can manage move-out requests (admin, owner, property_manager).
     */
    public function canManageMoveOuts(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_OWNER, self::ROLE_PROPERTY_MANAGER]);
    }

    /**
     * Check if user can access a specific property (ownership or assignment).
     */
    public function canAccessProperty(int $propertyId): bool
    {
        if ($this->hasAnyRole([self::ROLE_ADMIN])) {
            return true;
        }

        if ($this->hasAnyRole([self::ROLE_OWNER])) {
            return Property::where('id', $propertyId)
                ->where('property_owner_id', $this->id)
                ->exists();
        }

        // Check if user is assigned to this property via profile
        $profile = $this->profile;
        if ($profile && $profile->assigned_property) {
            $assignedProperty = Property::find($profile->assigned_property);
            if ($assignedProperty && $assignedProperty->id === $propertyId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can access a specific unit.
     */
    public function canAccessUnit(int $unitId): bool
    {
        if ($this->hasAnyRole([self::ROLE_ADMIN])) {
            return true;
        }

        $unit = Unit::find($unitId);
        if (! $unit) {
            return false;
        }

        return $this->canAccessProperty($unit->property_id);
    }

    /**
     * Check if user can access a specific tenant.
     */
    public function canAccessTenant(int $tenantId): bool
    {
        if ($this->hasAnyRole([self::ROLE_ADMIN])) {
            return true;
        }

        $tenant = Tenant::find($tenantId);
        if (! $tenant) {
            return false;
        }

        // Tenants can only access their own record
        if ($this->hasAnyRole([self::ROLE_TENANT])) {
            $userTenant = Tenant::where('email', $this->email)->first();

            return $userTenant && $userTenant->id === $tenantId;
        }

        return $this->canAccessUnit($tenant->unit_id ?? 0);
    }

    /**
     * Get properties accessible by the user.
     */
    public function accessibleProperties()
    {
        if ($this->hasAnyRole([self::ROLE_ADMIN])) {
            return Property::query();
        }

        if ($this->hasAnyRole([self::ROLE_OWNER])) {
            return Property::where('property_owner_id', $this->id);
        }

        // Check assigned property via profile
        $profile = $this->profile;
        if ($profile && $profile->assigned_property) {
            return Property::where('id', $profile->assigned_property);
        }

        return Property::where('id', 0); // No access
    }

    /**
     * Get units accessible by the user.
     */
    public function accessibleUnits()
    {
        if ($this->hasAnyRole([self::ROLE_ADMIN])) {
            return Unit::query();
        }

        return Unit::whereIn('property_id', $this->accessibleProperties()->pluck('properties.id'));
    }

    /**
     * Check if user can access property settings.
     */
    public function canAccessPropertySettings(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_OWNER, self::ROLE_PROPERTY_MANAGER, self::ROLE_AGENT]);
    }

    /**
     * Check if user can access payment settings.
     */
    public function canAccessPaymentSettings(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_OWNER]);
    }

    /**
     * Check if user can access security settings.
     */
    public function canAccessSecuritySettings(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_OWNER]);
    }

    /**
     * Check if user can access backup/system settings.
     */
    public function canAccessSystemSettings(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN]);
    }

    /**
     * Get role-specific profile data.
     */
    public function getRoleSpecificData(): array
    {
        $profile = $this->profile;

        return match ($this->role) {
            self::ROLE_ADMIN => [
                'label' => 'Administrator Information',
                'fields' => [
                    'Account Access Level' => 'Full system access',
                    'Role' => ucfirst(str_replace('_', ' ', $this->role)),
                ],
            ],
            self::ROLE_OWNER => [
                'label' => 'Owner Information',
                'fields' => [
                    'Business Name' => $profile?->business_name ?? 'Not set',
                    'National ID' => $profile?->national_id ?? 'Not set',
                    'KRA PIN' => $profile?->kra_pin ?? 'Not set',
                    'Contact' => $profile?->phone ?? 'Not set',
                    'Payment Details' => $profile?->payment_details ? 'Configured' : 'Not set',
                ],
            ],
            self::ROLE_AGENT => [
                'label' => 'Agent Information',
                'fields' => [
                    'Agency Name' => $profile?->agency_name ?? 'Not set',
                    'Contact' => $profile?->phone ?? 'Not set',
                    'Assigned Properties' => $profile?->assigned_property ?? 'Not set',
                    'Employment Status' => 'Active',
                ],
            ],
            self::ROLE_CARETAKER => [
                'label' => 'Caretaker Information',
                'fields' => [
                    'Assigned Property' => $profile?->assigned_property ?? 'Not set',
                    'Contact' => $profile?->phone ?? 'Not set',
                    'Emergency Contact' => $profile?->emergency_contact_name ?? 'Not set',
                    'Emergency Phone' => $profile?->emergency_contact_phone ?? 'Not set',
                ],
            ],
            self::ROLE_TENANT => [
                'label' => 'Tenancy Information',
                'fields' => [
                    'National ID' => $profile?->national_id ?? 'Not set',
                    'Emergency Contact' => $profile?->emergency_contact_name ?? 'Not set',
                    'Emergency Phone' => $profile?->emergency_contact_phone ?? 'Not set',
                    'Current Tenancy' => $this->getCurrentTenancyInfo(),
                ],
            ],
            default => [
                'label' => 'Account Information',
                'fields' => [
                    'Role' => ucfirst(str_replace('_', ' ', $this->role)),
                ],
            ],
        };
    }

    /**
     * Get the user's last login date from activity logs.
     */
    public function getLastLoginAttribute(): ?string
    {
        $lastLogin = $this->activityLogs()
            ->where('event', 'login')
            ->latest('created_at')
            ->first();

        return $lastLogin?->created_at?->format('d M Y, h:i A');
    }

    /**
     * Get the current tenancy information for a tenant user.
     */
    public function getCurrentTenancyInfo(): string
    {
        $tenant = $this->tenant;

        if (! $tenant) {
            return 'Not set';
        }

        $lease = $tenant->currentLease;

        if (! $lease) {
            return 'No active lease';
        }

        $unit = $lease->unit;

        return sprintf(
            '%s — %s to %s — %s/month',
            $unit?->name ?? 'Unit #'.($unit?->id ?? 'N/A'),
            $lease->start_date?->format('d M Y') ?? 'N/A',
            $lease->end_date?->format('d M Y') ?? 'N/A',
            $lease->monthly_rent ? number_format($lease->monthly_rent, 2) : 'N/A'
        );
    }

    /**
     * Ensure profile and notification preferences exist.
     */
    public function ensureRelatedRecords(): void
    {
        if (! $this->profile) {
            UserProfile::create(['user_id' => $this->id]);
        }

        if (! $this->notificationPreferences) {
            UserNotificationPreference::create(['user_id' => $this->id]);
        }

        if (! $this->twoFactorSettings) {
            UserTwoFactorSetting::create(['user_id' => $this->id]);
        }
    }
}
