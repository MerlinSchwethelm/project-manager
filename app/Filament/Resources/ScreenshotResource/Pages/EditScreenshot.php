<?php

namespace App\Filament\Resources\ScreenshotResource\Pages;

use App\Filament\Resources\ScreenshotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScreenshot extends EditRecord
{
    protected static string $resource = ScreenshotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
