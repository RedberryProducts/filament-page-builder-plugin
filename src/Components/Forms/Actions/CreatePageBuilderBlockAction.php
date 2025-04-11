<?php

namespace Redberry\PageBuilderPlugin\Components\Forms\Actions;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilder;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilderPreview;
use Redberry\PageBuilderPlugin\Traits\Actions\ModifiesPreviewField;

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

        $this->form(function ($arguments, Form $form, PageBuilder $component) {
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
                                        ),
                                    )->live(),
                            ]
                        )->columnSpan(1),
                    Hidden::make('block_type')->default($blockType),
                    Hidden::make('block_id')->default(app(config('page-builder-plugin.block_model_class'))->newUniqueId()),
                    $preview,
                ]
            )->columns(2);
        });

        $this->slideOver();

        $this->cancelParentActions();

        $this->modalWidth(MaxWidth::Screen);

        $this->action(function ($arguments, $data, PageBuilder $component) {
            $blockType = $arguments['block_type'];
            $state = $component->getState() ?? [];

            $block = app($component->getModel())->{$component->relationship}()->make([
                'block_type' => $blockType,
                'data' => $data['data'],
                'order' => count($state) + 1,
            ]);

            $block->id = $block->newUniqueId();

            $component->state([
                ...$state,
                $block->toArray(),
            ]);

            $this->sendSuccessNotification();

            $component->callAfterStateUpdated();

            $this->success();
        });
    }
}
