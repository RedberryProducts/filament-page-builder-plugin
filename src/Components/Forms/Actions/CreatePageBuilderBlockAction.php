<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Forms\Actions;

use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use RedberryProducts\PageBuilderPlugin\Components\Forms\PageBuilder;

class CreatePageBuilderBlockAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'create-page-builder-block';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->successNotificationTitle(__("filament-panels::resources/pages/create-record.notifications.created.title"));

        $this->form(function ($arguments, $form, PageBuilder $component, $livewire) {
            $blockType = $arguments['block_type'];

            return $form->schema(
                $component->getBlockSchema(
                    $blockType,
                    record: null,
                    component: $component,
                    livewire: $livewire,
                )
            );
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
}
