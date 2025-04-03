<?php

namespace Redberry\PageBuilderPlugin\Components\Forms\Actions;

use Filament\Forms\Components\Actions\Action;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilder;

class ReorderPageBuilderBlockAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reorder-page-builder-block';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function ($arguments, PageBuilder $component) {
            $flippedArray = array_flip($arguments['items']);

            $state = $component->getState();

            $newState = [];
            $originalIds = array_column($state, 'id');
            foreach ($flippedArray as $item => $index) {
                $newState[$index] = [
                    ...$state[array_search($item, $originalIds)],
                    'order' => $index,
                ];
            }

            $component->state($newState);
        });
    }
}
