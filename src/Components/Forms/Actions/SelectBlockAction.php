<?php

namespace Redberry\PageBuilderPlugin\Components\Forms\Actions;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilder;

class SelectBlockAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'select-page-builder-block';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->button();

        $this->icon('heroicon-o-plus');

        $this->color('primary');

        $this->form(function ($form, PageBuilder $component) {
            return $form->schema([
                Select::make('block_type')
                    ->native(false)
                    ->translateLabel()
                    ->required()
                    ->translateLabel()
                    ->options($this->formatBlocksForSelect($component)),
            ]);
        });

        $this->action(function ($data, $livewire, Action $action, PageBuilder $component) {
            $livewire->mountFormComponentAction(
                $component->getStatePath(),
                $component->getCreateActionName(),
                $data
            );
            $this->halt();
        });
    }

    private function formatBlocksForSelect(PageBuilder $component): array
    {
        $blocks = $component->getBlocks();

        $formatted = [];

        foreach ($blocks as $block) {
            $category = $block::getCategory();

            $formatted[$category][$block] = $block::getBlockName();
        }

        return $formatted;
    }
}
