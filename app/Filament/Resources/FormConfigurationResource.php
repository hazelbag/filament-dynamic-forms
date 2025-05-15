<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormConfigurationResource\Pages;
use App\Filament\Resources\FormConfigurationResource\RelationManagers;
use App\Models\FormConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FormConfigurationResource extends Resource
{
    protected static ?string $model = FormConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Form Builder';

    protected static ?string $modelLabel = 'Form Configuration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('A unique identifier for this form'),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->helperText('The title displayed to users'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->helperText('Whether this form is active and can be used'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Form Fields')
                    ->schema([
                        Forms\Components\Repeater::make('fields')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Field identifier (no spaces)'),

                                Forms\Components\TextInput::make('label')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Label displayed to users'),

                                Forms\Components\Select::make('type')
                                    ->options([
                                        'text' => 'Text Input',
                                        'textarea' => 'Text Area',
                                        'number' => 'Number Input',
                                        'select' => 'Dropdown Select',
                                        'multiselect' => 'Multi-Select',
                                        'checkbox' => 'Checkbox',
                                        'radio' => 'Radio Buttons',
                                        'date' => 'Date Picker',
                                        'datetime' => 'Date & Time Picker',
                                        'file' => 'File Upload',
                                    ])
                                    ->required()
                                    ->reactive(),

                                Forms\Components\Textarea::make('help_text')
                                    ->maxLength(500)
                                    ->helperText('Help text displayed to users'),

                                Forms\Components\Toggle::make('required')
                                    ->default(false),

                                // Dynamic options based on field type
                                Forms\Components\Group::make()
                                    ->schema(function (Forms\Get $get) {
                                        $type = $get('type');

                                        if (in_array($type, ['select', 'multiselect', 'radio'])) {
                                            return [
                                                Forms\Components\Repeater::make('options')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('label')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('value')
                                                            ->required(),
                                                    ])
                                                    ->columns(2)
                                                    ->required()
                                            ];
                                        }

                                        if ($type === 'number') {
                                            return [
                                                Forms\Components\Grid::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('min')
                                                            ->numeric(),
                                                        Forms\Components\TextInput::make('max')
                                                            ->numeric(),
                                                        Forms\Components\TextInput::make('step')
                                                            ->numeric()
                                                            ->default(1),
                                                    ])
                                                    ->columns(3)
                                            ];
                                        }

                                        if (in_array($type, ['text', 'textarea'])) {
                                            return [
                                                Forms\Components\Grid::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('min_length')
                                                            ->numeric(),
                                                        Forms\Components\TextInput::make('max_length')
                                                            ->numeric(),
                                                        Forms\Components\TextInput::make('placeholder')
                                                            ->maxLength(255),
                                                    ])
                                                    ->columns(3)
                                            ];
                                        }

                                        return [];
                                    }),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->collapsible()
                            ->reorderable()
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fields')
                    ->formatStateUsing(function ($state): string {
                        if (is_string($state)) {
                            $state = json_decode($state, true) ?? [];
                        }
                        return count((array) $state) . ' fields';
                    })
                    ->label('Fields Count'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->disabled(fn (FormConfiguration $record): bool => $record->hasSubmissions())
                    ->tooltip(fn (FormConfiguration $record): ?string => $record->hasSubmissions() ? 'Forms with submissions cannot be edited' : null),
                Tables\Actions\Action::make('preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn (FormConfiguration $record): string => route('form.preview', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if (!$record->hasSubmissions()) {
                                    $record->delete();
                                }
                            });
                        }),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                        }),
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
            'index' => Pages\ListFormConfigurations::route('/'),
            'create' => Pages\CreateFormConfiguration::route('/create'),
            'edit' => Pages\EditFormConfiguration::route('/{record}/edit'),
        ];
    }
}
