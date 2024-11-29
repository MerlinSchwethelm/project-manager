<?php

namespace App\Filament\User\Resources\UserTicketResource\Pages;

use App\Filament\User\Resources\UserTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserTicket extends ViewRecord
{
    protected static string $resource = UserTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
