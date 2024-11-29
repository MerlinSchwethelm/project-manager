<?php

namespace App\Filament\User\Resources\UserTicketResource\Pages;

use App\Enums\TicketStatus;
use App\Filament\User\Resources\UserTicketResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUserTicket extends EditRecord
{
    protected static string $resource = UserTicketResource::class;

    protected function getHeaderActions(): array
    {
        if ($this->record->status === TicketStatus::CLOSED->value) {
            return [];
        }

        return [

            Actions\DeleteAction::make()
                ->label('Close Ticket')
                ->action(function () {
                    $this->record->update(['status' => 'closed']);
                    Notification::make()
                        ->title('Ticket has been marked as closed.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Close Ticket')
                ->modalDescription('Are you sure you want to close this ticket? This action cannot be undone.'),
        ];
    }
}
