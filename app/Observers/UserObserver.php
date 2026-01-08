<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $user->hostingAccount()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'disk_limit_mb'      => 10240,   // 10 GB
                'bandwidth_limit_mb' => 102400,  // 100 GB
                'database_limit'     => 5,
                'email_limit'        => 10,
                'domain_limit'       => 3,
                'is_suspended'       => false,
            ]
        );
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // This will delete the hosting account record from the database
        // when the user is deleted (including soft deletes if applicable).
        $user->hostingAccount()->delete();
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        // If you are using Soft Deletes, this ensures the hosting account
        // is permanently removed when the user is force-deleted.
        $user->hostingAccount()->forceDelete();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

}
