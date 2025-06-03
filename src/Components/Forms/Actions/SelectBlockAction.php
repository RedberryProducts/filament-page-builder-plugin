<?php

namespace Redberry\PageBuilderPlugin\Components\Forms\Actions;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\MaxWidth;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilder;
use Redberry\PageBuilderPlugin\Components\Forms\RadioButtonImage;
use Redberry\PageBuilderPlugin\Traits\CanRenderWithThumbnails;

class SelectBlockAction extends Action
{
    use CanRenderWithThumbnails;

    public static function getDefaultName(): ?string
    {
        return 'select-page-builder-block';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->button();

        $this->icon('heroicon-o-plus');

        $this->modalWidth(MaxWidth::FiveExtraLarge);

        $this->color('primary');

        $this->form(function ($form, PageBuilder $component) {
            if ($this->getRenderWithThumbnails()) {
                return $form->schema([
                    RadioButtonImage::make('block_type')
                        ->translateLabel()
                        ->disableOptionWhen(
                            fn ($value) => (bool) $this->evaluate(Closure::fromCallable([$value, 'getIsSelectionDisabled']))
                        )
                        ->required()
                        ->columns(3)
                        ->columnSpanFull()
                        ->options($this->formatBlocksForSelect($component)),
                ]);
            }

            return $form->schema([
                Select::make('block_type')
                    ->native(false)
                    ->translateLabel()
                    ->disableOptionWhen(
                        fn ($value) => (bool) $this->evaluate(Closure::fromCallable([$value, 'getIsSelectionDisabled'])),
                    )
                    ->required()
                    ->translateLabel()
                    ->options($this->formatBlocksForSelect($component)),
            ]);
        });

        $this->stickyModalFooter();

        $this->action(function ($data, $livewire, PageBuilder $component) {
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
