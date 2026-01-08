<?php

namespace App\Providers;

use App\Mail\CustomVerifyEmailMail;
use App\Models\User;
use Filament\Auth\Notifications\VerifyEmail as FilamentVerifyEmail;
use App\Observers\UserObserver;
use Filament\Facades\Filament;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);


//        FilamentVerifyEmail::toMailUsing(function ($notifiable) {
//            // Same URL logic as before
//            $panel = Filament::getCurrentPanel();
//            $routeName = $panel
//                ? $panel->getPath() . '.auth.verify-email'
//                : 'filament.admin.auth.verify-email';  // your default panel
//
//            $verifyUrl = URL::temporarySignedRoute(
//                $routeName,
//                Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
//                ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
//            );
//
//            return new CustomVerifyEmailMail($verifyUrl, $notifiable->name ?? $notifiable->email);
//        });
    }
}
