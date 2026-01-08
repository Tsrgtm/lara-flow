<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\View;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->getStateUsing(fn ($record): HtmlString => new HtmlString("
                                <div class='flex items-center gap-2'>
                                    <img src='{$record->avatar_url}' alt='{$record->name}'
                                         class='w-10 h-10 rounded-full object-cover border border-gray-300 dark:border-gray-600'
                                    />
                                    <div class='flex flex-col gap-1'>
                                        <span class='font-medium leading-none'>{$record->name}</span>
                                        <span class='text-[10px] text-gray-500 dark:text-gray-400 leading-none'>@{$record->username}</span>
                                    </div>
                                </div>
                            ")),
                TextColumn::make('email')
                    ->label('Email address')
                    ->getStateUsing(fn ($record) => new HtmlString(
                        sprintf(
                            '<a href="mailto:%s" class="text-primary-600 hover:underline">%s</a>',
                            e($record->email),
                            e($record->email),
                        )
                    ))
                    ->searchable(),
                TextColumn::make('hosting_limits')
                    ->label('Hosting Limits')
                    ->view('components.hosting-limits'),
                IconColumn::make('verified')
                    ->state(fn ($record) => (bool) $record->verified)
                    ->boolean(),
                TextColumn::make('role')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('toggle_suspension')
                        ->label(fn ($record): string => $record->hostingAccount?->is_suspended ? 'Unsuspend' : 'Suspend')
                        ->color(fn ($record): string => $record->hostingAccount?->is_suspended ? 'info' : 'warning')
                        ->icon(fn ($record): string => $record->hostingAccount?->is_suspended ? 'heroicon-m-arrow-path' : 'heroicon-m-pause-circle')
                        ->visible(fn ($record): bool =>
                            auth()->user()->role === 'admin' &&
                            $record->id !== auth()->id()
                        )
                        ->modalHeading(fn ($record) => $record->hostingAccount?->is_suspended
                            ? "Restore access for {$record->name}?"
                            : "Suspend access for {$record->name}?"
                        )
                        ->modalDescription(fn ($record): string => $record->hostingAccount?->is_suspended
                            ? "This will reactivate all hosting services for this user. They will regain access to their dashboard and websites immediately."
                            : "This will immediately block service access for this user. You can restore their access at any time by unsuspending the account."
                        )

                        ->requiresConfirmation()
                        ->modalIcon(fn ($record) => $record->hostingAccount?->is_suspended ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                        ->modalSubmitActionLabel(fn ($record) => $record->hostingAccount?->is_suspended ? 'Confirm Activation' : 'Confirm Suspension')

                        ->action(function ($record) {
                            $account = $record->hostingAccount;

                            if ($account) {
                                $account->update([
                                    'is_suspended' => !$account->is_suspended,
                                ]);

                                Notification::make()
                                    ->title($account->is_suspended ? 'Account Suspended' : 'Account Reinstated')
                                    ->icon($account->is_suspended ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                                    ->iconColor($account->is_suspended ? 'danger' : 'success')
                                    ->send();
                            }
                        }),
                    DeleteAction::make()
                        ->hidden(fn ($record): bool => $record->id === auth()->id())
                        ->requiresConfirmation(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
