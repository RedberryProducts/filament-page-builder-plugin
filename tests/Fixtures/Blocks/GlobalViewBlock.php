<?php

namespace Redberry\PageBuilderPlugin\Tests\Fixtures\Blocks;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;
use Redberry\PageBuilderPlugin\Traits\IsGlobalBlock;

class GlobalViewBlock extends BaseBlock
{
    use IsGlobalBlock;

    public static function getBlockSchema(?object $record = null): array
    {
        $schema = static::getBaseBlockSchema($record);
        return static::applyGlobalConfiguration($schema);
    }

    public static function getBaseBlockSchema(?object $record = null): array
    {
        return [
            TextInput::make('title')
                ->label('Title')
                ->required(),
            
            Textarea::make('content')
                ->label('Content')
                ->required(),
                
            TextInput::make('button_text')
                ->label('Button Text'),
        ];
    }
}