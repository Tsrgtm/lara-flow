<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->schema([
                Section::make('User Profile')
                    ->description('Manage basic account identification and security.')
                    ->icon('heroicon-m-user-circle')
                    ->columnSpan([
                        'default' => 4,
                        'sm' => 2,
                        'lg' => 2,
                        'xl' => 2,
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 1,
                        'xl' => 1,
                    ])
                    ->schema([
                        FileUpload::make('avatar')
                            ->visibility('public')
                            ->directory('avatars')
                            ->avatar()
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('username')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->minLength(3)
                            ->maxLength(20)
                            // Custom Regex: Only letters, numbers, and underscores
                            ->regex('/^[a-zA-Z0-9_]+$/')
                            ->validationMessages([
                                'unique' => 'This username is already taken by another account.',
                                'regex' => 'The username can only contain letters, numbers, and underscores.',
                                'minLength' => 'The username must be at least 3 characters long.',
                            ])
                            ->live(onBlur: true) // Validates as soon as the user clicks away
                            ->prefix('@')
                            ->helperText('Your unique identifier on the platform.'),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required(),
                        DateTimePicker::make('email_verified_at')
                            ->required()
                            ->afterStateHydrated(fn ($state, $set) => $state ?? $set('email_verified_at', now()))
                            ->default(now()),
                        TextInput::make('password')
                            ->password()
                            ->hiddenOn(EditRecord::class)
                            ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                            ->dehydrated(fn ($state) => filled($state)),
                        Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'user' => 'User',
                                'reseller' => 'Reseller',
                            ])
                            ->native(false)
                            ->required()
                            ->default('user'),
                        Select::make('parent_id')
                            ->label('Created By')
                            ->relationship(
                                name: 'parent',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query, $record) => $query->when(
                                    $record,
                                    fn ($q) => $q->where('id', '!=', $record->id)
                                )
                            )
                            ->default(auth()->id())
                            ->preload()
                            ->searchable()
                            ->allowHtml()
                            ->getOptionLabelFromRecordUsing(fn ($record): HtmlString => new HtmlString("
                                <div class='flex items-center gap-2'>
                                    <img src='{$record->avatar_url}' alt='{$record->name}'
                                         class='w-5 h-5 rounded-full object-cover border border-gray-300 dark:border-gray-600'
                                    />
                                    <div class='flex flex-col gap-.5'>
                                        <span class='text-xs font-medium leading-none'>{$record->name}</span>
                                        <span class='text-[9px] text-gray-500 dark:text-gray-400 leading-none'>{$record->email}</span>
                                    </div>
                                </div>
                            "))
                    ]),

                Section::make('Hosting Account & Limits')
                    ->description('Technical resource allocation for this user.')
                    ->icon('heroicon-m-cpu-chip')
                    ->relationship('hostingAccount')
                    ->collapsible()
                    ->columnSpan([
                        'default' => 4,
                        'sm' => 2,
                        'lg' => 2,
                        'xl' => 2,
                    ])
                    ->schema([
                        Toggle::make('is_suspended')
                            ->label('Suspend Hosting Service')
                            ->helperText('Activating this will lock the account and disable limit editing.')
                            ->live()
                            ->columnSpanFull(),

                        Grid::make(1) // Compact 3-column grid for limits
                        ->columns([
                            'default' => 1,
                            'sm' => 2,
                            'lg' => 1,
                            'xl' => 1,
                        ])
                        ->schema([
                            TextInput::make('disk_limit_mb') // Keep the name as the DB column
                            ->label('Disk Limit (GB)')
                                ->numeric()
                                ->suffix('GB')
                                ->live()
                                ->formatStateUsing(fn ($state) => $state ? $state / 1024 : null)
                                ->dehydrateStateUsing(fn ($state) => $state ? $state * 1024 : null)
                                ->helperText(fn (Get $get) => filled($get('disk_limit_mb'))
                                    ? number_format($get('disk_limit_mb') * 1024) . ' MB'
                                    : 'Enter limit in GB')
                                ->disabled(fn (Get $get) => $get('is_suspended')),

                            TextInput::make('bandwidth_limit_mb')
                                ->label('Bandwidth Limit (GB)')
                                ->numeric()
                                ->suffix('GB')
                                ->live()
                                ->formatStateUsing(fn ($state) => $state ? $state / 1024 : null)
                                ->dehydrateStateUsing(fn ($state) => $state ? $state * 1024 : null)
                                ->helperText(fn (Get $get) => filled($get('bandwidth_limit_mb'))
                                    ? number_format($get('bandwidth_limit_mb') * 1024) . ' MB'
                                    : 'Enter limit in GB')
                                ->disabled(fn (Get $get) => $get('is_suspended')),

                            TextInput::make('database_limit')
                                ->label('Databases')
                                ->numeric()
                                ->disabled(fn (Get $get) => $get('is_suspended')),

                            TextInput::make('email_limit')
                                ->label('Email Accounts')
                                ->numeric()
                                ->disabled(fn (Get $get) => $get('is_suspended')),

                            TextInput::make('domain_limit')
                                ->label('Addon Domains')
                                ->numeric()
                                ->disabled(fn (Get $get) => $get('is_suspended')),
                        ]),
                    ]),
            ]);
    }
}
