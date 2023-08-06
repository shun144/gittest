<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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

            // 管理者メニュー
            if (Gate::allows('isAdmin')){
                $event->menu->add(
                    ['text' => '店舗情報一覧','url' => route('admin.store'), 'icon' => 'fas fa-fw fa-store',],
                    ['text' => 'ログファイル一覧','url' => route('admin.log'), 'icon' => 'fas fa-fw fa-file',],
                );
            }
            // 管理者以外メニュー
            else {
                $event->menu->add(
                    ['text' => '配信', 'url' => route('owner.schedule'), 'icon' => 'fab fa-fw fa-line'],
                    ['text' => '連携LINEユーザ一覧', 'url' => route('owner.line_users'), 'icon' => 'fas fa-fw fa-user'],
                    ['text' => '配信履歴一覧','url' => route('owner.history'),'icon' => 'fas fa-fw fa-history'],
                    ['text' => 'アクション設定','url' => route('owner.action'),'icon' => 'fas fa-fw fa-history']
                ); 
            }
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
