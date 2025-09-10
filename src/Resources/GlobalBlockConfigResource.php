<?php

namespace Redberry\PageBuilderPlugin\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Redberry\PageBuilderPlugin\Models\GlobalBlockConfig;
use Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource\Pages;

class GlobalBlockConfigResource extends Resource
{
    protected static ?string $model = GlobalBlockConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $navigationLabel = 'Global Blocks';

    protected static ?string $modelLabel = 'Global Block';

    protected static ?string $pluralModelLabel = 'Global Blocks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Block Name')
                    ->required()
                    ->disabled(),

                Forms\Components\Section::make('Block Configuration')
                    ->schema(function (?GlobalBlockConfig $record) {
                        if (!$record || !class_exists($record->class_name)) {
                            return [];
                        }

                        try {
                            $blockClass = $record->class_name;
                            if (method_exists($blockClass, 'getBaseBlockSchema')) {
                                $schema = $blockClass::getBaseBlockSchema();
                            } else {
                                $schema = $blockClass::getBlockSchema();
                            }

                            foreach ($schema as $field) {
                                if (method_exists($field, 'getName')) {
                                    $fieldName = $field->getName();
                                    $configValue = $record->getConfigValue($fieldName);
                                    if ($configValue !== null) {
                                        $field->default($configValue);
                                    }
                                }
                            }

                            return $schema;
                        } catch (\Exception $e) {
                            return [
                                Forms\Components\Placeholder::make('error')
                                    ->label('Error')
                                    ->content('Unable to load block schema: ' . $e->getMessage()),
                            ];
                        }
                    })
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Block Name')
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Configure'),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('name');
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
            'index' => Pages\ListGlobalBlocks::route('/'),
            'edit' => Pages\EditGlobalBlock::route('/{record}/edit'),
        ];
    }
}
