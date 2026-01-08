<?php

namespace App\Models;

use App\Mail\CustomVerifyEmailMail;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Concerns\InteractsWithEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable implements HasAvatar, FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasEmailAuthentication, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery, InteractsWithEmailAuthentication;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getAvatarUrlAttribute(): string
    {
        // If user uploaded avatar exists
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        // Generate deterministic colors based on user identity
        $seed = crc32($this->id . $this->email);

        $backgroundColors = [
            '0F172A', // slate-900
            '1E293B', // slate-800
            '020617', // dark
            '312E81', // indigo
            '3B0764', // violet
            '14532D', // green
            '7C2D12', // orange
            '701A75', // purple
        ];

        $textColors = [
            'F8FAFC', // slate-50
            'E0F2FE', // sky-100
            'ECFEFF', // cyan-50
            'FAF5FF', // violet-50
            'FFF7ED', // orange-50
        ];

        $background = $backgroundColors[$seed % count($backgroundColors)];
        $color = $textColors[$seed % count($textColors)];

        $name = urlencode($this->name ?? 'User');

        return "https://ui-avatars.com/api/?" . http_build_query([
                'name' => $name,
                'color' => $color,
                'background' => $background,
                'bold' => true,
                'size' => 256,
                'format' => 'png',
            ]);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function hostingAccount()
    {
        return $this->hasOne(HostingAccount::class);
    }

    public function sendEmailVerificationNotification()
    {
        // We use the URL generator to create the temporary signed URL Laravel expects
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())]
        );

        // Send your custom Mailable
        Mail::to($this->email)->send(new CustomVerifyEmailMail($verificationUrl, $this->name));
    }
}
