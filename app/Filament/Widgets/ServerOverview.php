<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Services\ServerStatsService;

class ServerOverview extends Widget
{
    protected string $view = 'filament.widgets.server-overview';
    protected int | string | array $columnSpan = 'full';

    public function getData(): array
    {
        // Injecting the service via the app helper to avoid static errors
        return app(ServerStatsService::class)->getFullDiagnostic();
    }
}
