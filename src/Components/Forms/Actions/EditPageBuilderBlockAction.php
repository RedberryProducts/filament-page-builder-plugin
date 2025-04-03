<?php

namespace Redberry\PageBuilderPlugin\Components\Forms\Actions;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilder;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilderPreview;
use Redberry\PageBuilderPlugin\Traits\Actions\ModifiesPreviewField;

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
            $block = $component->getState()[$arguments['index']];

            $preview = PageBuilderPreview::make('preview')
                ->singleItemPreview()
                ->pageBuilderField('data');

            $preview = $this->getModifiedPreviewField($preview, $block['block_type']);

            $this->fillForm([
                'data' => $block['data'],
                'block_type' => $block['block_type'],
                'block_id' => $block['id'],
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
                                            $block['block_type'],
                                            record: null,
                                            component: $component,
                                            livewire: $livewire,
                                        ),
                                    )->live(),
                            ]
                        )->columnSpan(1),
                    Hidden::make('block_type'),
                    Hidden::make('block_id'),
                    $preview,
                ]
            )->columns(2);
        });

        $this->slideOver();

        $this->modalWidth(MaxWidth::Screen);

        $this->action(function ($arguments, $data, $action, PageBuilder $component) {
            $newState = $component->getState();

            $newState[$arguments['index']] = [
                ...$newState[$arguments['index']],
                'data' => $data['data'],
            ];

            $component->state($newState);

            $component->callAfterStateUpdated();

            $action->sendSuccessNotification();
        });
    }
}
