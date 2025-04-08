<?php

namespace Redberry\PageBuilderPlugin\Components\Forms\Actions;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilder;

class DeletePageBuilderBlockAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'delete-page-builder-block';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->requiresConfirmation();

        $this->color('danger');

        $this->hiddenLabel();

        $this->label(__('filament-actions::delete.single.label'));

        $this->modalHeading(function ($arguments, $component) {
            $block = $component->getState()[$arguments['index']];

            $closure = Closure::fromCallable([$block['block_type'], 'getBlockLabel']);

            $label = (string) $this->evaluate($closure);

            return __('filament-actions::delete.single.modal.heading', ['label' => $label]);
        });

        $this->modalSubmitActionLabel(__('filament-actions::delete.single.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-actions::delete.single.notifications.deleted.title'));

        $this->action(function ($arguments, Action $action, PageBuilder $component) {
            $items = $component->getState();
            unset($items[$arguments['index']]);

            $component->state($items);

            $action->sendSuccessNotification();

            $component->callAfterStateUpdated();
        });
    }
}
