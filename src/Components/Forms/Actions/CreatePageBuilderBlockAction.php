<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Forms\Actions;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use RedberryProducts\PageBuilderPlugin\Components\Forms\PageBuilder;
use RedberryProducts\PageBuilderPlugin\Components\Forms\PageBuilderPreview;
use RedberryProducts\PageBuilderPlugin\Traits\Actions\ModifiesPreviewField;

class CreatePageBuilderBlockAction extends Action
{
    use ModifiesPreviewField;

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

            $preview = $this->getModifiedPreviewField($preview, $blockType);

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
                            ]
                        )->columnSpan(1),
                    Hidden::make('block_type')->default($blockType),
                    $preview,
                ]
            )->columns(2);
        });

        $this->slideOver();

        $this->cancelParentActions();

        $this->modalWidth(MaxWidth::Screen);

        $this->action(function ($arguments, $data, $action, PageBuilder $component) {
            $blockType = $arguments['block_type'];
            $block =  app($component->getModel())->{$component->relationship}()->make([
                'block_type' => $blockType,
                'data' => $data['data'],
            ]);
            $block->id = $block->newUniqueId();

            $component->state([
                ...$component->getState() ?? [],
                $block->toArray(),
            ]);

            $action->sendSuccessNotification();

            $component->callAfterStateUpdated();
        });
    }
}
