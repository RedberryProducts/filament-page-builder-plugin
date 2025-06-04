<?php

namespace Redberry\PageBuilderPlugin\Components\Forms\Actions;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\MaxWidth;
use Illuminate\View\ComponentAttributeBag;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilder;
use Redberry\PageBuilderPlugin\Components\Forms\RadioButtonImage;
use Redberry\PageBuilderPlugin\Traits\CanRenderWithThumbnails;
use Redberry\PageBuilderPlugin\Traits\FormatsBlockCategories;

class SelectBlockAction extends Action
{
    use CanRenderWithThumbnails;
    use FormatsBlockCategories;

    public ?Closure $modifySelectionFieldUsing = null;

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
                $field = RadioButtonImage::make('block_type')
                    ->translateLabel()
                    ->disableOptionWhen(
                        fn ($value) => (bool) $this->evaluate(Closure::fromCallable([$value, 'getIsSelectionDisabled']))
                    )
                    ->required()
                    ->columns([
                        'default' => 3,
                    ])
                    ->allTabAttributes(new ComponentAttributeBag([
                        'icon' => 'heroicon-o-rectangle-stack',
                    ]))
                    ->columnSpanFull()
                    ->options($this->formatBlocksForSelect($component));
            } else {
                $field = Select::make('block_type')
                 ->native(false)
                 ->translateLabel()
                 ->disableOptionWhen(
                     fn ($value) => (bool) $this->evaluate(Closure::fromCallable([$value, 'getIsSelectionDisabled'])),
                 )
                 ->required()
                 ->translateLabel()
                 ->options($this->formatBlocksForSelect($component));
            }

            if ($this->modifySelectionFieldUsing) {
                $field = $this->evaluate($this->modifySelectionFieldUsing, [
                    'field' => $field,
                    'component' => $component,
                ]);
            }

            return $form->schema([
                $field
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

    private function formatBlocksForRadio(PageBuilder $component): array
    {
        $blocks = $component->getBlocks();

        $formatted = [];

        foreach ($blocks as $block) {
            $category = $block::getCategory();

            $formatted[$category][$block] = $block::getBlockName();
        }

        return $formatted;
    }

    private function formatBlocksForSelect(PageBuilder $component): array
    {
        $blocks = $component->getBlocks();

        $formatted = [];

        foreach ($blocks as $block) {
            $category = $this->getCategoryTitle($block::getCategory());

            $formatted[$category][$block] = $block::getBlockName();
        }

        return $formatted;
    }
}
