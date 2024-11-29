<?php

namespace App\Filament\User\Resources\UserTicketResource\Pages;

use App\Filament\User\Resources\UserTicketResource;
use App\Models\Ticket;
use Auth;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUserTickets extends ListRecords
{
    protected static string $resource = UserTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {

        return Ticket::whereUserId(Auth::user()->id);
    }
}
