<?php

namespace App\Filament\User\Resources\UserTicketResource\Pages;

use App\Enums\TicketStatus;
use App\Filament\User\Resources\UserTicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserTicket extends CreateRecord
{
    protected static string $resource = UserTicketResource::class;

    /**
     * @param  array<string, string>  $data
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        $data['status'] = TicketStatus::OPEN;

        return $data;
    }
}
