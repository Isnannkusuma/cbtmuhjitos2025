<?php

namespace App\Providers\Filament;

use App\Filament\Resources\AksesUjianResource;
use App\Filament\Resources\GuruResource;
use App\Filament\Resources\HasilUjianResource;
use App\Filament\Resources\MapelResource;
use App\Filament\Resources\SiswaResource;
use App\Filament\Resources\SoalResource;
use App\Filament\Resources\UjianResource;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\AksesUjianPieChart;
use App\Filament\Widgets\RecentExams;
use App\Filament\Widgets\StatsOverview;
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
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('MUHJITOS.CBT')
            ->brandLogo(asset('storage/assets/logo.png'))
            ->brandLogoHeight('3rem')
            ->brandName('MUHJITOS.CBT')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->databaseNotifications()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->favicon(asset('storage/assets/logo.png'))
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                StatsOverview::class,
                AksesUjianPieChart::class,
                RecentExams::class
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
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make()
                        ->items([
                            ...Dashboard::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Managemen Pengguna')
                        ->items([
                            ...SiswaResource::getNavigationItems(),
                            ...GuruResource::getNavigationItems(),
                            ...UserResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Managemen Akademik')
                        ->items([
                            ...MapelResource::getNavigationItems(),
                            ...UjianResource::getNavigationItems(),
                            ...SoalResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Pelaksanaan Ujian')
                        ->items([
                            ...AksesUjianResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Hasil & Penilaian')
                        ->items([
                            ...HasilUjianResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Setting')
                        ->items([
                            NavigationItem::make('Edit Profile')
                                ->url(route('filament.admin.pages.edit-profile'))
                                ->icon('heroicon-o-cog-6-tooth'),
                        ]),
                ]);
            })
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                   ->setTitle('My Profile')
                   ->setNavigationLabel('My Profile')
                   ->shouldRegisterNavigation(true)
                //    ->shouldShowSanctumTokens()
                //    ->shouldShowBrowserSessionsForm()
                   ->shouldShowAvatarForm()
            ])
            ->sidebarFullyCollapsibleOnDesktop();
    }
}
