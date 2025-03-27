<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Forms\Actions;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use RedberryProducts\PageBuilderPlugin\Components\Forms\PageBuilder;
use RedberryProducts\PageBuilderPlugin\Components\Forms\PageBuilderPreview;
use RedberryProducts\PageBuilderPlugin\Traits\Actions\ModifiesPreviewField;

class EditPageBuilderBlockAction extends Action
{
    use ModifiesPreviewField;

    public static function getDefaultName(): ?string
    {
        return 'edit-page-builder-block';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->successNotificationTitle(__('filament-panels::resources/pages/edit-record.notifications.saved.title'));

        $this->form(function ($arguments, Form $form, PageBuilder $component, Page $livewire) {
            $block = $component->findPageBuilderBlock($arguments['item']);


            $preview = PageBuilderPreview::make('preview')
                ->singleItemPreview()
                ->pageBuilderField('data');

            $preview = $this->getModifiedPreviewField($preview, $block->block_type);

            $this->fillForm([
                'data' => $block->data,
                'block_type' => $block->block_type,
            ]);

            return $form->schema(
                [
                    Grid::make(1)
                        ->statePath('data')
                        ->schema(
                            [
                                Grid::make(1)
                                    ->schema(
                                        $component->getBlockSchema(
                                            $block->block_type,
                                            record: null,
                                            component: $component,
                                            livewire: $livewire,
                                        ),
                                    )->live(),
                            ]
                        )->columnSpan(1),
                    Hidden::make('block_type')->default($block->block_type),
                    $preview,
                ]
            )->columns(2);
        });

        $this->slideOver();

        $this->modalWidth(MaxWidth::Screen);

        $this->action(function ($arguments, $data, $action, PageBuilder $component) {
            // TODO: update only the block data not database record
            $block = $component->findPageBuilderBlock($arguments['item']);

            $result = $block->update([
                'data' => $data['data'],
            ]);

            if (! $result) {
                $action->failure();

                return;
            }

            $action->sendSuccessNotification();
        });
    }
}
