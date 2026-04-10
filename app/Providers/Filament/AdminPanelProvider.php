<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ActiveRentalWidget;
use App\Filament\Widgets\EquipmentWidget;
use App\Filament\Widgets\LoyaltyMemberWidget;
use App\Filament\Widgets\MaintenanceOverviewWidget;
use App\Filament\Widgets\RentalOverviewWidget;
use App\Models\Equipment;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
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
            ->login()
            ->topbar(true)
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Slate,
                'primary' => '#069494',
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->favicon(asset('images/Logo.jpg'))
            ->font('ubuntu')
            ->brandLogo(asset('images/Logo.jpg'))
            ->brandLogoHeight('3rem')
            ->renderHook('panels::topbar.start', fn() => view('components.current-datetime'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                RentalOverviewWidget::class,
                ActiveRentalWidget::class,
                EquipmentWidget::class,
                MaintenanceOverviewWidget::class,
                LoyaltyMemberWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
