<?php

namespace App\Filament\Resources;

use App\Enums\TicketPriorities;
use App\Enums\TicketStatus;
use App\Filament\Resources\TicketResource\Pages;
use App\Models\Screenshot;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\MarkdownEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('priority')
                    ->default(TicketPriorities::LOW->value)
                    ->options(TicketPriorities::getKeyValuePairs())
                    ->selectablePlaceholder(false)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->default(TicketStatus::OPEN->value)
                    ->options(TicketStatus::getKeyValuePairs())
                    ->selectablePlaceholder(false)
                    ->required(),
                Forms\Components\DateTimePicker::make('deadline'),
                Forms\Components\FileUpload::make('screenshots')
                    ->label('Upload Screenshots')
                    ->multiple()
                    ->directory('screenshots')
                    ->acceptedFileTypes(['image/*'])
                    ->maxFiles(5)
                    ->disk('public')
                    ->formatStateUsing(
                        function (?Ticket $record) {
                            if (! $record) {
                                return [];
                            }

                            return $record->screenshots->map(fn (Screenshot $screenshot) => $screenshot->path)->toArray();
                        }
                    )
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
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('screenshots.path')
                    ->label('Screenshots')
                    ->getStateUsing(function (Ticket $record) {
                        return $record->screenshots->map(fn ($screenshot) => $screenshot->url)->toArray();
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
