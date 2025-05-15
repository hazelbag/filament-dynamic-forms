<?php

namespace App\Filament\Resources\FormConfigurationResource\Pages;

use App\Filament\Resources\FormConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFormConfigurations extends ListRecords
{
    protected static string $resource = FormConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
