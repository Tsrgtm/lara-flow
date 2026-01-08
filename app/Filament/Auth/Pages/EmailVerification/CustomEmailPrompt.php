<?php

namespace App\Filament\Auth\Pages\EmailVerification;

use App\Notifications\CustomVerifyEmailNotification;
use Filament\Auth\Pages\EmailVerification\EmailVerificationPrompt;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use LogicException;

class CustomEmailPrompt extends EmailVerificationPrompt
{
    protected function sendEmailVerificationNotification(MustVerifyEmail $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new LogicException("Model [{$userClass}] does not have a [notify()] method.");
        }

        $notification = new CustomVerifyEmailNotification();
        $notification->url = Filament::getVerifyEmailUrl($user);

        $user->notify($notification);
    }
}
