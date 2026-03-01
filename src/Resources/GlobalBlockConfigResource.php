<?php

namespace Redberry\PageBuilderPlugin\Resources;

use BackedEnum;
use Exception;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Redberry\PageBuilderPlugin\Models\GlobalBlockConfig;
use Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource\Pages\EditGlobalBlock;
use Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource\Pages\ListGlobalBlocks;
use UnitEnum;

class GlobalBlockConfigResource extends Resource
{
    protected static ?string $model = GlobalBlockConfig::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = null;

    protected static ?string $navigationLabel = 'Global Blocks';

    protected static ?string $modelLabel = 'Global Block';

    protected static ?string $pluralModelLabel = 'Global Blocks';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('name')
                    ->label('Block Name')
                    ->required()
                    ->disabled(),

                Section::make('Block Configuration')
                    ->schema(function (?GlobalBlockConfig $record) {
                        if (! $record || ! class_exists($record->class_name)) {
                            return [];
                        }

                        try {
                            $blockClass = $record->class_name;
                            if (method_exists($blockClass, 'getBaseBlockSchema')) {
                                $schema = $blockClass::getBaseBlockSchema();
                            }
                            else {
                                $schema = $blockClass::getBlockSchema();
                            }

                            foreach ($schema as $field) {
                                /** @var Field $field */
                                $field = $field;
                                if (method_exists($field, 'getName')) {
                                    $fieldName   = $field->getName();
                                    $configValue = $record->getConfigValue($fieldName);
                                    if ($configValue !== null) {
                                        $field->default($configValue);
                                    }
                                }
                            }

                            return $schema;
                        }
                        catch (Exception $e) {
                            return [
                                Placeholder::make('error')
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
                TextColumn::make('name')
                    ->label('Block Name')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Configure'),
            ])
            ->toolbarActions([
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
            'index' => ListGlobalBlocks::route('/'),
            'edit'  => EditGlobalBlock::route('/{record}/edit'),
        ];
    }
}
