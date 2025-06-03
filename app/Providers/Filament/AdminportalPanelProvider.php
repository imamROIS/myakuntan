<?php

namespace App\Providers\Filament;

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
use Filament\Support\Enums\MaxWidth;
use App\Filament\Widgets\BalanceSheetReport;
use App\Filament\Widgets\CashFlowWidget;


class AdminportalPanelProvider extends PanelProvider
{
    public function widgets(): array
{
    return [
        BalanceSheetReport::class,
    ];
}

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('adminportal')
            ->path('adminportal')
            // ->spa()
            ->maxContentWidth(MaxWidth::Full)
            // ->simplePageMaxContentWidth(MaxWidth::Small)
            // ->sidebarCollapsibleOnDesktop()
            // ->collapsedSidebarWidth('9rem')
            ->topNavigation()
            // ->extraTopbarAttributes([
            //     'class' => 'shadow-md',
            //     'style' => 'background-color: var(--primary-500);', // Gunakan variabel CSS
            // ])
            ->darkMode(false)

            ->login()
            ->colors([
                'primary' => Color::hex('#211C84'),
                'secondary' => Color::hex('#3A36AE'),
                'accent' => Color::hex('#FFD700'),
                'gray' => Color::Gray,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                BalanceSheetReport::class,
                CashFlowWidget::class,
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
    protected function getResources(): array
{
    return [
        \App\Filament\Resources\JurnalHarianResource::class,
        // ... resource lainnya
    ];
}


}
