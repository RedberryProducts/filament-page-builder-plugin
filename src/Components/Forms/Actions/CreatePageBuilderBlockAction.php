<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Forms\Actions;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use RedberryProducts\PageBuilderPlugin\Components\Forms\PageBuilder;
use RedberryProducts\PageBuilderPlugin\Components\Forms\PageBuilderPreview;

class CreatePageBuilderBlockAction extends Action
{
    public Closure|null $modifyPreviewUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'create-page-builder-block';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->successNotificationTitle(__('filament-panels::resources/pages/create-record.notifications.created.title'));

        $this->form(function ($arguments, Form $form, PageBuilder $component, $livewire) {
            $blockType = $arguments['block_type'];

            $preview = PageBuilderPreview::make('preview')
                ->singleItemPreview()
                ->pageBuilderField('data');

            if ($this->modifyPreviewUsing) {
                $preview = $this->evaluate($this->modifyPreviewUsing, [
                    'field' => $preview,
                    'action' => $this,
                    'blockType' => $blockType,
                ]) ?? $preview;
            }


            return $form->schema(
                [
                    Grid::make(1)
                        ->statePath('data')
                        ->schema(
                            [
                                Grid::make(1)
                                    ->schema(
                                        $component->getBlockSchema(
                                            $blockType,
                                            record: null,
                                            component: $component,
                                            livewire: $livewire,
                                        ),
                                    )->live(),
                                Hidden::make('block_type')->default($blockType),
                            ]
                        )->columnSpan(1),
                    $preview,
                ]
            )->columns(2);
        });

        $this->slideOver();

        $this->cancelParentActions();

        $this->modalWidth(MaxWidth::Screen);

        $this->action(function ($arguments, $data, $action, PageBuilder $component) {
            $blockType = $arguments['block_type'];

            $block = $component->getRecord()->{$component->relationship}()->create([
                'block_type' => $blockType,
                'data' => $data,
            ]);

            $component->state([
                ...$component->getState(),
                $block->toArray(),
            ]);

            $action->sendSuccessNotification();

            $component->callAfterStateUpdated();
        });
    }

    public function pageBuilderPreviewField(Closure $modifyPreviewUsing): static
    {
        $this->modifyPreviewUsing = $modifyPreviewUsing;

        return $this;
    }
}
