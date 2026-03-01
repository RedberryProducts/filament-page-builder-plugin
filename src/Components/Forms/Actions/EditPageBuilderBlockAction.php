<?php

namespace Redberry\PageBuilderPlugin\Components\Forms\Actions;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Width;
use Filament\Forms\Components\Hidden;
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

        $this->form(function ($arguments, Schema $schema, PageBuilder $component) {
            $block = $component->getState()[$arguments['index']];

            $preview = PageBuilderPreview::make('preview')
                ->singleItemPreview()
                ->pageBuilderField('');

            $preview = $this->getModifiedPreviewField($preview, $block['block_type']);

            $this->fillForm([
                'data' => $block['data'],
                'block_type' => $block['block_type'],
                'block_id' => $block['id'],
            ]);

            return $schema->components(
                [
                    Grid::make(1)
                        ->statePath('data')
                        ->schema(
                            [
                                Grid::make(1)
                                    ->schema(
                                        $component->getBlockSchema(
                                            $block['block_type'],
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

        $this->modalWidth(Width::Screen);

        $this->action(function ($arguments, $data, $action, PageBuilder $component) {
            $newState = $component->getState();

            $existingData = $newState[$arguments['index']]['data'] ?? [];
            $mergedData = array_merge($existingData, $data['data'] ?? []);

            $newState[$arguments['index']] = [
                ...$newState[$arguments['index']],
                'data' => $mergedData,
            ];

            $component->state($newState);

            $component->callAfterStateUpdated();

            $this->success();
        });
    }
}
