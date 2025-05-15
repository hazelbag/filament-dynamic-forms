<?php

namespace App\Filament\Resources\FormConfigurationResource\Pages;

use App\Filament\Resources\FormConfigurationResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditFormConfiguration extends EditRecord
{
    protected static string $resource = FormConfigurationResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->hasSubmissions()) {
            Notification::make()
                ->title('Form cannot be edited')
                ->body('This form has submissions and cannot be edited.')
                ->danger()
                ->send();

            $this->redirect(static::getResource()::getUrl('index'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->disabled($this->record->hasSubmissions())
                ->tooltip($this->record->hasSubmissions() ? 'Forms with submissions cannot be deleted' : null),
        ];
    }
}
