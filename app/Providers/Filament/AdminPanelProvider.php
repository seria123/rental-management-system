<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\IncomeVsExpensesChartWidget;
use App\Filament\Widgets\MonthlyRentCollectedWidget;
use App\Filament\Widgets\OccupiedUnitsWidget;
use App\Filament\Widgets\OutstandingRentWidget;
use App\Filament\Widgets\PendingMaintenanceTableWidget;
use App\Filament\Widgets\PendingMaintenanceWidget;
use App\Filament\Widgets\RecentTenantsWidget;
use App\Filament\Widgets\RecentTransactionsWidget;
use App\Filament\Widgets\TotalExpensesWidget;
use App\Filament\Widgets\TotalPropertiesWidget;
use App\Filament\Widgets\TotalTenantsWidget;
use App\Filament\Widgets\TotalUnitsWidget;
use App\Filament\Widgets\VacantUnitsWidget;
use App\Http\Middleware\Filament\EnsureAdminRole;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')

            // Authentication
            ->login()
            ->authGuard('web')
            ->profile()
            ->passwordReset()

            // Navigation and application branding
            ->brandName('NDIRO ESTATES')
            ->brandLogo(asset('images/logo-dark.svg'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.ico'))

            // Primary colour palette
            ->colors([
                'primary' => [
                    50 => '#FFFBEB',
                    100 => '#FEF3C7',
                    200 => '#FDE68A',
                    300 => '#FCD34D',
                    400 => '#FBBF24',
                    500 => '#F59E0B',
                    600 => '#D97706',
                    700 => '#B45309',
                    800 => '#92400E',
                    900 => '#78350F',
                    950 => '#451A03',
                ],
                'gray' => Color::Slate,
                'success' => Color::Green,
                'danger' => Color::Red,
                'warning' => Color::Amber,
                'info' => Color::Blue,
            ])

            // Layout
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17rem')
            ->maxContentWidth('full')

            // Search and notifications
            ->globalSearch()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')

            // Custom Filament theme
            ->viteTheme('resources/css/filament/admin/theme.css')

            // Top navigation system status
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): string => '
                    <div class="fi-admin-status">
                        <span class="fi-admin-status-dot"></span>
                        <span>System operational</span>
                    </div>
                ',
            )

            // Navigation organisation
            ->navigationGroups([
                'Overview',
                'Property Management',
                'Financial Management',
                'Operations',
                'Communication',
                'Administration',
            ])

            // Resources
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\Filament\Resources'
            )

            // Pages
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\Filament\Pages'
            )

            ->pages([
                Dashboard::class,
            ])

            // Widgets
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\Filament\Widgets'
            )

            ->widgets([
                TotalPropertiesWidget::class,
                TotalUnitsWidget::class,
                OccupiedUnitsWidget::class,
                VacantUnitsWidget::class,
                TotalTenantsWidget::class,
                MonthlyRentCollectedWidget::class,
                OutstandingRentWidget::class,
                TotalExpensesWidget::class,
                PendingMaintenanceWidget::class,
                IncomeVsExpensesChartWidget::class,
                RecentTransactionsWidget::class,
                RecentTenantsWidget::class,
                PendingMaintenanceTableWidget::class,
            ])

            // Middleware
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            // Admin access protection
            ->authMiddleware([
                Authenticate::class,
                EnsureAdminRole::class,
            ]);
    }
}
