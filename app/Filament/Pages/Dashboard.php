<?php

namespace App\Filament\Pages;

use App\Filament\Resources\TicketResource\Widgets\TicketStatistics;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            TicketStatistics::class,
        ];
    }
}