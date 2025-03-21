<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Forms\Actions;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use RedberryProducts\PageBuilderPlugin\Components\Forms\PageBuilder;

class EditPageBuilderBlockAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'edit-page-builder-block';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->successNotificationTitle(__("filament-panels::resources/pages/edit-record.notifications.saved.title"));

        $this->form(function ($arguments, Form $form, PageBuilder  $component, Page $livewire) {
            $block = $component->findPageBuilderBlock($arguments['item']);

            $this->fillForm($block->data);

            return $form->schema(
                $component->getBlockSchema(
                    $block->block_type,
                    $block,
                    $component,
                    $livewire,
                )
            );
        });

        $this->slideOver();

        $this->modalWidth(MaxWidth::Screen);

        $this->action(function ($arguments, $data, $action, PageBuilder $component) {
            $block = $component->findPageBuilderBlock($arguments['item']);

            $result = $block->update([
                'data' => $data,
            ]);

            if (! $result) {
                $action->failure();

                return;
            }

            $action->sendSuccessNotification();
        });
    }
}
