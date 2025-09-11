<?php

namespace Redberry\PageBuilderPlugin\Components\Forms\Actions;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
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

    protected ?Closure $modifySelectionFieldUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'select-page-builder-block';
    }

    public function selectField(Closure $modifySelectionFieldUsing): static
    {
        $this->modifySelectionFieldUsing = $modifySelectionFieldUsing;

        return $this;
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
                    ->options($this->formatBlocksForRadio($component));
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
                ]) ?? $field;
            }

            return $form->schema([
                $field,
            ]);
        });

        $this->stickyModalFooter();

        $this->action(function ($data, $livewire, PageBuilder $component) {
            $blockType = $data['block_type'];

            $isGlobalBlock = class_exists($blockType) && method_exists($blockType, 'isGlobalBlock') && $blockType::isGlobalBlock();

            if ($isGlobalBlock) {
                $state = $component->getState() ?? [];

                $block = app($component->getModel())->{$component->relationship}()->make([
                    'block_type' => $blockType,
                    'data' => [],
                    'order' => count($state) + 1,
                ]);

                $block->id = $block->newUniqueId();

                $component->state([
                    ...$state,
                    $block->toArray(),
                ]);

                $component->callAfterStateUpdated();

                Notification::make()
                    ->title('Block added successfully')
                    ->success()
                    ->send();

                return;
            }

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
