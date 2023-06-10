<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Auth;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // $event->menu->remove('change_password');
            // dd(auth()->user()['role']);
            if (auth()->user()['role'] == 'admin'){
                $event->menu->add(
                    [
                        'text' => '店舗情報一覧',
                        'url' => route('admin.store'),
                        'icon' => 'fas fa-fw fa-user',
                    ],
                    // [
                    // 'text' => 'チャート',
                    // 'url' => route('admin.chart'),
                    // 'icon' => 'fas fa-fw fa-user',
                    // ]
                );
            }
            // 管理者以外
            else {
                $event->menu->add(
                    [
                        'text' => '配信',
                        'url' => route('owner.schedule'),
                        'icon' => 'fas fa-fw fa-user',
                    ],
                    [
                        'text' => '連携LINEユーザ一覧',
                        'url' => route('owner.line_users'),
                        'icon' => 'fas fa-fw fa-user',
                    ],

                ); 
            }
            // $event->menu->add(
            //     // [
            //     // 'text' => 'プロフィール',
            //     // 'url' => route('admin.profile'),
            //     // 'icon' => 'fas fa-fw fa-user',
            //     // ],
            //     [
            //     'text' => '店舗情報一覧',
            //     'url' => route('admin.store'),
            //     'icon' => 'fas fa-fw fa-user',
            //     ],
            //     // [
            //     // 'text' => 'スタッフ情報一覧',
            //     // 'url' => route('admin.member'),
            //     // 'icon' => 'fas fa-fw fa-user',
            //     // ],
            //     [
            //     'text' => '配信',
            //     'url' => route('admin.message'),
            //     'icon' => 'fas fa-fw fa-user',
            //     ],
            //     // [
            //     // 'text' => 'チャート',
            //     // 'url' => route('admin.chart'),
            //     // 'icon' => 'fas fa-fw fa-user',
            //     // ]
            // );
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
