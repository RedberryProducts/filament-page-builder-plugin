<?php

namespace Redberry\PageBuilderPlugin\Tests\Fixtures\Blocks;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;

class ViewBlock extends BaseBlock
{
    public static function getBlockSchema(): array
    {
        return [
            Section::make('hero button')
                ->statePath('hero_button')
                ->schema([
                    TextInput::make('text'),
                    TextInput::make('url')
                        ->url()
                        ->required(),
                ]),
        ];
    }

    public static function getView(): ?string
    {
        return 'blocks.view';
    }
}
