@php
    $user = Auth::user();
    $navItems = [];

    if ($user && $user->hasAnyRole(['admin', 'developer'])) {
        // Super Admin
        $navItems = [
            ['label' => 'Dashboard', 'route' => route('dashboard'), 'icon' => 'home', 'active' => ['dashboard']],
            ['label' => 'User Management', 'route' => route('filament.admin.resources.users.index'), 'icon' => 'users', 'active' => ['filament.admin.resources.users.*']],
            ['label' => 'Property Management', 'route' => route('properties.index'), 'icon' => 'building', 'active' => ['properties.*']],
            ['label' => 'Landlord Management', 'route' => route('tenants.index'), 'icon' => 'user', 'active' => ['tenants.*']],
            ['label' => 'Tenant Management', 'route' => route('tenants.index'), 'icon' => 'users', 'active' => ['tenants.*']],
            ['label' => 'Agent Management', 'route' => route('filament.admin.resources.agents.index'), 'icon' => 'user', 'active' => ['filament.admin.resources.agents.*']],
            ['label' => 'Caretaker Management', 'route' => route('filament.admin.resources.caretakers.index'), 'icon' => 'user', 'active' => ['filament.admin.resources.caretakers.*']],
            ['label' => 'Payments & Transactions', 'route' => route('payments.index'), 'icon' => 'credit-card', 'active' => ['payments.*']],
            ['label' => 'Reports & Analytics', 'route' => route('reports.index'), 'icon' => 'chart', 'active' => ['reports.*']],
            ['label' => 'System Settings', 'route' => route('settings.index'), 'icon' => 'key', 'active' => ['settings.*']],
            ['label' => 'Audit Logs', 'route' => route('admin.activity-log'), 'icon' => 'file-text', 'active' => ['admin.activity-log']],
        ];
    } elseif ($user && $user->hasAnyRole(['owner'])) {
        // Property Owner / Landlord
        $navItems = [
            ['label' => 'Dashboard', 'route' => route('dashboard'), 'icon' => 'home', 'active' => ['dashboard']],
            ['label' => 'My Properties', 'route' => route('properties.index'), 'icon' => 'building', 'active' => ['properties.*']],
            ['label' => 'Units Management', 'route' => route('units.index'), 'icon' => 'layers', 'active' => ['units.*']],
            ['label' => 'Tenants', 'route' => route('tenants.index'), 'icon' => 'users', 'active' => ['tenants.*']],
            ['label' => 'Rent Collection', 'route' => route('payments.index'), 'icon' => 'credit-card', 'active' => ['payments.*']],
            ['label' => 'Expenses', 'route' => route('expenses.index'), 'icon' => 'receipt', 'active' => ['expenses.*']],
            ['label' => 'Maintenance Reports', 'route' => route('notifications.index'), 'icon' => 'tool', 'active' => ['notifications.*']],
            ['label' => 'Financial Reports', 'route' => route('reports.index'), 'icon' => 'chart', 'active' => ['reports.*']],
            ['label' => 'Occupancy Reports', 'route' => route('reports.index'), 'icon' => 'bar-chart', 'active' => ['reports.*']],
            ['label' => 'Statements', 'route' => route('reports.index'), 'icon' => 'file-text', 'active' => ['reports.*']],
            ['label' => 'Profile & Settings', 'route' => route('profile.show'), 'icon' => 'user', 'active' => ['profile.*', 'settings.*']],
        ];
    } elseif ($user && $user->hasAnyRole(['agent'])) {
        // Agent - follow Property Manager/Agent scope as requested
        $navItems = [
            ['label' => 'Dashboard', 'route' => route('dashboard'), 'icon' => 'home', 'active' => ['dashboard']],
            ['label' => 'Assigned Properties', 'route' => route('properties.index'), 'icon' => 'building', 'active' => ['properties.*']],
            ['label' => 'Units', 'route' => route('units.index'), 'icon' => 'layers', 'active' => ['units.*']],
            ['label' => 'Tenant Management', 'route' => route('tenants.index'), 'icon' => 'users', 'active' => ['tenants.*']],
            ['label' => 'Lease Agreements', 'route' => route('tenants.index'), 'icon' => 'file-text', 'active' => ['tenants.*']],
            ['label' => 'Rent Collection', 'route' => route('payments.index'), 'icon' => 'credit-card', 'active' => ['payments.*']],
            ['label' => 'Move-in Requests', 'route' => '#', 'icon' => 'arrow-right', 'active' => []],
            ['label' => 'Move-out Requests', 'route' => route('move-out-requests.index'), 'icon' => 'sign-out', 'active' => ['move-out-requests.*']],
            ['label' => 'Maintenance Requests', 'route' => route('notifications.index'), 'icon' => 'tool', 'active' => ['notifications.*']],
            ['label' => 'Inspections', 'route' => '#', 'icon' => 'search', 'active' => []],
            ['label' => 'Expenses', 'route' => route('expenses.index'), 'icon' => 'receipt', 'active' => ['expenses.*']],
            ['label' => 'Reports', 'route' => route('reports.index'), 'icon' => 'chart', 'active' => ['reports.*']],
        ];
    } elseif ($user && $user->hasAnyRole(['property_manager'])) {
        // Property Manager / Agent
        $navItems = [
            ['label' => 'Dashboard', 'route' => route('dashboard'), 'icon' => 'home', 'active' => ['dashboard']],
            ['label' => 'Assigned Properties', 'route' => route('properties.index'), 'icon' => 'building', 'active' => ['properties.*']],
            ['label' => 'Units', 'route' => route('units.index'), 'icon' => 'layers', 'active' => ['units.*']],
            ['label' => 'Tenant Management', 'route' => route('tenants.index'), 'icon' => 'users', 'active' => ['tenants.*']],
            ['label' => 'Lease Agreements', 'route' => route('tenants.index'), 'icon' => 'file-text', 'active' => ['tenants.*']],
            ['label' => 'Rent Collection', 'route' => route('payments.index'), 'icon' => 'credit-card', 'active' => ['payments.*']],
            ['label' => 'Move-in Requests', 'route' => '#', 'icon' => 'arrow-right', 'active' => []],
            ['label' => 'Move-out Requests', 'route' => route('move-out-requests.index'), 'icon' => 'sign-out', 'active' => ['move-out-requests.*']],
            ['label' => 'Maintenance Requests', 'route' => route('notifications.index'), 'icon' => 'tool', 'active' => ['notifications.*']],
            ['label' => 'Inspections', 'route' => '#', 'icon' => 'search', 'active' => []],
            ['label' => 'Expenses', 'route' => route('expenses.index'), 'icon' => 'receipt', 'active' => ['expenses.*']],
            ['label' => 'Reports', 'route' => route('reports.index'), 'icon' => 'chart', 'active' => ['reports.*']],
        ];
    } elseif ($user && $user->hasAnyRole(['caretaker'])) {
        // Property Manager / Agent
        $navItems = [
            ['label' => 'Dashboard', 'route' => route('dashboard'), 'icon' => 'home', 'active' => ['dashboard']],
            ['label' => 'Assigned Properties', 'route' => route('properties.index'), 'icon' => 'building', 'active' => ['properties.*']],
            ['label' => 'Units', 'route' => route('units.index'), 'icon' => 'layers', 'active' => ['units.*']],
            ['label' => 'Tenant Management', 'route' => route('tenants.index'), 'icon' => 'users', 'active' => ['tenants.*']],
            ['label' => 'Lease Agreements', 'route' => route('tenants.index'), 'icon' => 'file-text', 'active' => ['tenants.*']],
            ['label' => 'Rent Collection', 'route' => route('payments.index'), 'icon' => 'credit-card', 'active' => ['payments.*']],
            ['label' => 'Move-in Requests', 'route' => '#', 'icon' => 'arrow-right', 'active' => []],
            ['label' => 'Move-out Requests', 'route' => route('move-out-requests.index'), 'icon' => 'sign-out', 'active' => ['move-out-requests.*']],
            ['label' => 'Maintenance Requests', 'route' => route('notifications.index'), 'icon' => 'tool', 'active' => ['notifications.*']],
            ['label' => 'Inspections', 'route' => '#', 'icon' => 'search', 'active' => []],
            ['label' => 'Expenses', 'route' => route('expenses.index'), 'icon' => 'receipt', 'active' => ['expenses.*']],
            ['label' => 'Reports', 'route' => route('reports.index'), 'icon' => 'chart', 'active' => ['reports.*']],
        ];
    } elseif ($user && $user->hasAnyRole(['caretaker'])) {
        // Caretaker
        $navItems = [
            ['label' => 'Dashboard', 'route' => route('dashboard'), 'icon' => 'home', 'active' => ['dashboard']],
            ['label' => 'Assigned Properties', 'route' => route('properties.index'), 'icon' => 'building', 'active' => ['properties.*']],
            ['label' => 'Occupied Units', 'route' => route('units.index'), 'icon' => 'check-circle', 'active' => ['units.*']],
            ['label' => 'Vacant Units', 'route' => route('units.index'), 'icon' => 'circle', 'active' => ['units.*']],
            ['label' => 'Tenant Directory', 'route' => route('tenants.index'), 'icon' => 'users', 'active' => ['tenants.*']],
            ['label' => 'Rent Payment Verification', 'route' => route('payments.index'), 'icon' => 'credit-card', 'active' => ['payments.*']],
            ['label' => 'Maintenance Requests', 'route' => route('notifications.index'), 'icon' => 'tool', 'active' => ['notifications.*']],
            ['label' => 'Maintenance Updates', 'route' => route('notifications.index'), 'icon' => 'refresh-cw', 'active' => ['notifications.*']],
            ['label' => 'Property Inspections', 'route' => '#', 'icon' => 'search', 'active' => []],
            ['label' => 'Incident Reports', 'route' => '#', 'icon' => 'alert-triangle', 'active' => []],
            ['label' => 'Announcements', 'route' => route('notifications.index'), 'icon' => 'bell', 'active' => ['notifications.*']],
            ['label' => 'Profile', 'route' => route('profile.show'), 'icon' => 'user', 'active' => ['profile.*']],
        ];
    } elseif ($user && $user->hasAnyRole(['tenant'])) {
        // Tenant
        $navItems = [
            ['label' => 'Dashboard', 'route' => route('portal.dashboard'), 'icon' => 'home', 'active' => ['portal.dashboard']],
            ['label' => 'My Unit', 'route' => route('portal.dashboard'), 'icon' => 'home', 'active' => ['portal.*']],
            ['label' => 'My Lease', 'route' => route('portal.statements'), 'icon' => 'file-text', 'active' => ['portal.statements']],
            ['label' => 'My Rent', 'route' => route('portal.statements'), 'icon' => 'credit-card', 'active' => ['portal.statements']],
            ['label' => 'Payment History', 'route' => route('payments.index'), 'icon' => 'clock', 'active' => ['payments.*']],
            ['label' => 'Pay Rent', 'route' => route('payments.create'), 'icon' => 'dollar-sign', 'active' => ['payments.create']],
            ['label' => 'M-Pesa Payments', 'route' => route('payments.create'), 'icon' => 'smartphone', 'active' => ['payments.*']],
            ['label' => 'Maintenance Requests', 'route' => route('portal.maintenance.create'), 'icon' => 'tool', 'active' => ['portal.maintenance.*']],
            ['label' => 'Move-out Requests', 'route' => '#', 'icon' => 'sign-out', 'active' => []],
            ['label' => 'Notices & Announcements', 'route' => route('notifications.index'), 'icon' => 'bell', 'active' => ['notifications.*']],
            ['label' => 'My Profile', 'route' => route('profile.show'), 'icon' => 'user', 'active' => ['profile.*']],
            ['label' => 'Support', 'route' => route('notifications.index'), 'icon' => 'help-circle', 'active' => ['notifications.*']],
        ];
    } else {
        $navItems = [
            ['label' => 'Dashboard', 'route' => route('dashboard'), 'icon' => 'home', 'active' => ['dashboard']],
            ['label' => 'Properties', 'route' => route('properties.index'), 'icon' => 'building', 'active' => ['properties.index', 'properties.show']],
            ['label' => 'Units', 'route' => route('units.index'), 'icon' => 'layers', 'active' => ['units.index']],
            ['label' => 'Tenants', 'route' => route('tenants.index'), 'icon' => 'users', 'active' => ['tenants.index']],
            ['label' => 'Payments', 'route' => route('payments.index'), 'icon' => 'credit-card', 'active' => ['payments.index']],
            ['label' => 'Profile', 'route' => route('profile.show'), 'icon' => 'user', 'active' => ['profile.*']],
            ['label' => 'Settings', 'route' => route('settings.index'), 'icon' => 'key', 'active' => ['settings.*']],
        ];
    }

    // Add move-out requests for roles that need it (if not already added)
    if ($user && $user->hasAnyRole(['admin', 'developer', 'property_manager']) && ! collect($navItems)->contains(fn($i) => str_contains($i['label'], 'Move-out'))) {
        // already handled above for some
    }
@endphp

<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <div class="brand-mark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M3 11.5 12 4l9 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5 10.5V20h14v-9.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 20v-6h6v6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div>
            <p class="brand-label">Portfolio</p>
            <h1 class="brand-name">Ndiro Estates</h1>
        </div>
    </div>

    <nav class="sidebar-nav" aria-label="Sidebar navigation">
        @foreach ($navItems as $item)
            @php $isActive = request()->routeIs($item['active']); @endphp
            <a href="{{ $item['route'] }}" class="nav-item {{ $isActive ? 'active' : '' }}">
                @include('layouts.partials.icon', ['name' => $item['icon']])
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
            <div>
                <p class="user-name">{{ $user->name ?? 'User' }}</p>
                <p class="user-role">{{ ucfirst(str_replace('_', ' ', $user->role ?? 'admin')) }}</p>
            </div>
        </div>

        <div class="mt-4 space-y-2">
            <a href="{{ route('profile.edit') }}" class="secondary-action">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-7 8a7 7 0 0 1 14 0" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="secondary-action w-full text-left">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 17l5-5-5-5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 12H9" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Log out
                </button>
            </form>
        </div>
    </div>
</aside>
