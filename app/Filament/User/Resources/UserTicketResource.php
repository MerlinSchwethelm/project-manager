<?php

namespace App\Filament\User\Resources;

use App\Enums\TicketPriorities;
use App\Enums\TicketStatus;
use App\Filament\User\Resources\UserTicketResource\Pages;
use App\Models\Screenshot;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserTicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * @return Builder<Model>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Forms\Components\DateTimePicker::make('deadline')
                    ->label('Deadline')
                    ->timezone('Europe/Berlin'),
                Forms\Components\MarkdownEditor::make('description')
                    ->label('Description')
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\Select::make('priority')
                    ->label('Priority')
                    ->options(TicketPriorities::getKeyValuePairs())
                    ->selectablePlaceholder(false)
                    ->default(TicketPriorities::LOW->value)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->disabled()
                    ->label('Status')
                    ->options(TicketStatus::getKeyValuePairs())
                    ->selectablePlaceholder(false)
                    ->default(TicketStatus::OPEN->value),
                Forms\Components\FileUpload::make('screenshots.path')
                    ->label('Upload Screenshots')
                    ->multiple()
                    ->directory('screenshots')
                    ->acceptedFileTypes(['image/*'])
                    ->maxFiles(5)
                    ->disk('public')
                    ->saveRelationshipsUsing(function (Ticket $ticket, array $state) {
                        $ticket->screenshots()->whereNotIn('path', collect($state)->pluck('path'))->each(function ($screenshot) {
                            $screenshot->delete();
                        });
                        foreach ($state as $screenshotPath) {
                            $ticket->screenshots()->create([
                                'user_id' => auth()->id(),
                                'path' => $screenshotPath,
                            ]);
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            TicketPriorities::LOW->value => ucwords(TicketPriorities::LOW->value),
                            TicketPriorities::MEDIUM->value => ucwords(TicketPriorities::MEDIUM->value),
                            TicketPriorities::HIGH->value => ucwords(TicketPriorities::HIGH->value),
                            TicketPriorities::CRITICAL->value => ucwords(TicketPriorities::CRITICAL->value),
                            default => $state,
                        };
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            TicketStatus::OPEN->value => ucwords(TicketStatus::OPEN->value),
                            TicketStatus::IN_PROGRESS->value => ucwords(str_replace('_', ' ', TicketStatus::IN_PROGRESS->value)),
                            TicketStatus::CLOSED->value => ucwords(TicketStatus::CLOSED->value),
                            default => $state,
                        };
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deadline')
                    ->date('H:i d F Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('screenshots.path')
                    ->label('Screenshots')
                    ->getStateUsing(function (Ticket $record) {
                        return $record->screenshots->map(fn (Screenshot $screenshot) => $screenshot->url)->toArray();
                    }),
            ])

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserTickets::route('/'),
            'create' => Pages\CreateUserTicket::route('/create'),
            'edit' => Pages\EditUserTicket::route('/{record}/edit'),
            'view' => Pages\ViewUserTicket::route('/{record}/view'),
        ];
    }
}
