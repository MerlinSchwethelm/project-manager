<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketStatistics extends ChartWidget
{
    protected static ?string $heading = 'Ticket Status Distribution';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        return [
            'labels' => TicketStatus::labels(),
            'datasets' => [
                [
                    'label' => 'Ticket Status',
                    'data' => [
                        Ticket::where('status', TicketStatus::OPEN->value)->count(),
                        Ticket::where('status', TicketStatus::CLOSED->value)->count(),
                        Ticket::where('status', TicketStatus::IN_PROGRESS->value)->count(),
                    ],
                    'backgroundColor' => ['#4caf50', '#f44336', '#ff9800'],
                ],
            ],
        ];
    }
}
