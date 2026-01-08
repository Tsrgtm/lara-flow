<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Pages\EmailVerification\CustomEmailPrompt;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Platform;
use Filament\Support\Enums\Width;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Support\Facades\FilamentView;

class AdminPanelProvider extends PanelProvider
{
    public function boot()
    {

        FilamentView::registerRenderHook(
            'panels::sidebar.footer',
            fn (): string => view('filament.sidebar-footer', [
                'stats' => app(\App\Services\ServerStatsService::class)->getFullDiagnostic(),
                'ip'    => request()->server('SERVER_ADDR') ?? $_SERVER['SERVER_ADDR'] ?? '127.0.0.1',
            ])->render(),
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
//            ->globalSearch(false)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchFieldSuffix(fn (): ?string => match (Platform::detect()) {
                Platform::Windows, Platform::Linux => 'CTRL+K',
                Platform::Mac => '⌘K',
                default => null,
            })
            ->plugins([
                GlobalSearchModalPlugin::make(),
            ])
            ->multiFactorAuthentication([
                EmailAuthentication::make()
                    ->codeExpiryMinutes(5),
                AppAuthentication::make()
                    ->brandName('Lara Flow')
                    ->recoverable()
                    ->recoveryCodeCount(10),
            ])
            ->navigationItems([
                NavigationItem::make('Sites')
                    ->url('#') // Replace with SitesResource::getUrl()
                    ->icon('heroicon-o-globe-alt')
                    ->sort(6),

                NavigationItem::make('Databases')
                    ->url('#') // Replace with DatabasesResource::getUrl()
                    ->icon('heroicon-o-circle-stack')
                    ->sort(7),

                NavigationItem::make('File Manager')
                    ->url('#') // Link to file manager page
                    ->icon('heroicon-o-folder')
                    ->sort(8),

                NavigationItem::make('SSL Manager')
                    ->icon('heroicon-o-lock-closed')
                    ->url('#')
                    ->sort(9),

                NavigationItem::make('Logs')
                    ->url('#')
                    ->icon('heroicon-o-document-text')
                    ->sort(10),

                NavigationItem::make('Settings')
                    ->url('#')
                    ->icon('heroicon-o-cog')
                    ->sort(11),

            ])
            ->emailVerification(CustomEmailPrompt::class)
            ->emailChangeVerification()
            ->profile(isSimple: false)
            ->brandLogo(fn () => view('components.logo'))
//            ->sidebarCollapsibleOnDesktop()
//            ->collapsedSidebarWidth('9rem')
            ->sidebarWidth('260px')
            ->maxContentWidth(Width::Full)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->authGuard('web')
            ->login()
            ->colors([
                'primary' => Color::Green,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
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
