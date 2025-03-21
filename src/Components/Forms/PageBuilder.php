<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Forms;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Components\Component;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;

// TODO: make this reorder-able
class PageBuilder extends Field
{
    protected ?string $relationship = null;

    protected ?Closure $renderDeleteActionButtonUsing = null;

    protected ?Closure $modifyDeleteActionUsing = null;

    protected ?Closure $modifyEditActionUsing = null;

    protected ?Closure $modifyCreateActionUsing = null;

    protected ?Closure $modifySelectBlockActionUsing = null;

    protected ?Closure $renderEditActionButtonUsing = null;

    public array | Closure $blocks = [];

    public string $view = 'page-builder-plugin::forms.page-builder';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpanFull();

        $this->relationship('pageBuilderBlocks');

        $this->registerActions([
            fn (self $component): Action => $component->getDeleteAction(),
            fn (self $component): Action => $component->getEditAction(),
            fn (self $component): Action => $component->getSelectBlockAction(),
            fn (self $component): Action => $component->getCreateAction(),
        ]);
    }

    public function getCreateAction(): Action
    {
        $action = Action::make($this->getCreateActionName())
            ->successNotificationTitle(__(
                "filament-panels::resources/pages/create-record.notifications.created.title"
            ))
            ->form(function ($arguments, Form $form, Action $action, PageBuilder $component, Page $livewire) {
                $blockType = $arguments['block_type'];

                return $form->schema(
                    $this->getBlockSchema(
                        $blockType,
                        record: null,
                        component: $component,
                        livewire: $livewire,
                    )
                );
            })
            ->disabled($this->isDisabled())
            ->slideOver()
            ->cancelParentActions()
            ->modalWidth(MaxWidth::Screen)
            ->action(function ($arguments, $data, $action, PageBuilder $component) {
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

        if ($this->modifyCreateActionUsing) {
            $action = $this->evaluate($this->modifyCreateActionUsing, [
                'action' => $action,
            ]);
        }

        return $action;
    }

    public function getSelectBlockAction(): Action
    {
        $action = Action::make($this->getSelectBlockActionName())
            ->button()
            ->icon('heroicon-o-plus')
            ->translateLabel()
            ->disabled($this->isDisabled())
            ->color('primary')
            ->form(function (Form $form) {
                return $form->schema([
                    Select::make('block_type')
                        ->native(false)
                        ->label('Block Type')
                        ->translateLabel()
                        ->options($this->formatBlocksForSelect())
                ]);
            })
            ->action(function ($data, EditRecord $livewire, Action $action) {
                $livewire->mountFormComponentAction(
                    $this->getStatePath(),
                    $this->getCreateActionName(),
                    $data
                );
                $action->halt();
            });

        if ($this->modifySelectBlockActionUsing) {
            $action = $this->evaluate($this->modifySelectBlockActionUsing, [
                'action' => $action,
            ]);
        }

        return $action;
    }

    // TODO: move this function to its own file for making reading easier.
    public function getEditAction(): Action
    {
        $action =  Action::make($this->getEditActionName())
            ->successNotificationTitle(__("filament-panels::resources/pages/edit-record.notifications.saved.title"))
            ->disabled($this->isDisabled())
            ->form(function ($arguments, Form $form, Action $action, PageBuilder $component, Page $livewire) {
                $block = $this->findPageBuilderBlock($arguments['item']);

                $action->fillForm($block->data);

                return $form->schema(
                    $this->getBlockSchema(
                        $block->block_type,
                        $block,
                        $component,
                        $livewire,
                    )
                );
            })
            ->slideOver()
            ->modalWidth(MaxWidth::Screen)
            ->action(function ($arguments, $data, $action) {
                $block = $this->findPageBuilderBlock($arguments['item']);

                $result = $block->update([
                    'data' => $data,
                ]);

                if (! $result) {
                    $action->failure();

                    return;
                }

                $action->sendSuccessNotification();
            });

        if ($this->modifyEditActionUsing) {
            $action = $this->evaluate($this->modifyEditActionUsing, [
                'action' => $action,
            ]);
        }

        return $action;
    }

    public function getDeleteAction(): Action
    {
        $action = Action::make($this->getDeleteActionName())
            ->requiresConfirmation()
            ->color('danger')
            ->disabled($this->isDisabled())
            ->hiddenLabel()
            ->successNotificationTitle('Block deleted')
            ->action(function ($arguments, Action $action, PageBuilder $component) {
                $block = $this->findPageBuilderBlock($arguments['item']);

                $result = $block->delete();

                if (! $result) {
                    $action->failure();

                    return;
                }

                $items = $component->getState();
                $itemKey = array_search($arguments['item'], array_column($items, 'id'));
                unset($items[$itemKey]);

                $component->state($items);

                $action->sendSuccessNotification();

                $component->callAfterStateUpdated();
            });

        if ($this->modifyDeleteActionUsing) {
            $action = $this->evaluate($this->modifyDeleteActionUsing, [
                'action' => $action,
            ]);
        }

        return $action;
    }

    public function renderDeleteActionButton(string $item)
    {
        $statePath = $this->getStatePath();
        $deleteAction = $this->getDeleteAction();

        $attributes = [
            'slot' => $deleteAction->getLabel(),
            'labelSrOnly' => true,
            'icon' => 'heroicon-o-trash',
            'color' => 'danger',
            'disabled' => $deleteAction->isDisabled(),
            'attributes' => collect([
                'wire:click' => "mountFormComponentAction('$statePath', '{$this->getDeleteActionName()}', { item: '$item' } )",
            ]),
        ];

        if ($this->renderDeleteActionButtonUsing) {
            return $this->evaluate($this->renderDeleteActionButtonUsing, [
                'action' => $deleteAction,
                'item' => $item,
            ]);
        }

        return view('filament::components.button.index', $attributes);
    }

    public function renderEditActionButton(string $item)
    {
        $statePath = $this->getStatePath();
        $editAction = $this->getEditAction();

        $attributes = [
            'slot' => $editAction->getLabel(),
            'labelSrOnly' => true,
            'icon' => 'heroicon-o-pencil-square',
            'disabled' => $editAction->isDisabled(),
            'color' => 'primary',
            'attributes' => collect([
                'wire:click' => "mountFormComponentAction('$statePath', '{$this->getEditActionName()}', { item: '$item' } )",
            ]),
        ];

        if ($this->renderEditActionButtonUsing) {
            return $this->evaluate($this->renderEditActionButtonUsing, [
                'action' => $editAction,
                'item' => $item,
            ]);
        }

        return view('filament::components.button.index', $attributes);
    }

    public function deleteActionButton(Closure $renderDeleteActionButtonUsing)
    {
        $this->renderDeleteActionButtonUsing = $renderDeleteActionButtonUsing;

        return $this;
    }

    public function editActionButton(Closure $renderEditActionButtonUsing)
    {
        $this->renderEditActionButtonUsing = $renderEditActionButtonUsing;

        return $this;
    }

    public function getDeleteActionName(): string
    {
        return 'delete';
    }

    public function getEditActionName(): string
    {
        return 'edit';
    }

    public function getCreateActionName(): string
    {
        return 'create';
    }

    public function getSelectBlockActionName(): string
    {
        return 'select-block';
    }

    public function deleteAction(
        Closure $modifyDeleteActionUsing,
    ) {
        $this->modifyDeleteActionUsing = $modifyDeleteActionUsing;

        return $this;
    }

    public function editAction(
        Closure $modifyEditActionUsing,
    ) {
        $this->modifyEditActionUsing = $modifyEditActionUsing;

        return $this;
    }

    public function blocks(
        array | Closure $blocks,
    ) {
        $this->blocks = $blocks;

        return $this;
    }

    #[Computed(true)]
    public function getBlocks(): array
    {
        $evaluated = $this->evaluate($this->blocks);

        if (is_array($evaluated)) {
            return $evaluated;
        }

        return [];
    }

    #[Computed(true)]
    private function formatBlocksForSelect(): array
    {
        $blocks = $this->getBlocks();

        $formatted = [];

        foreach ($blocks as $block) {
            $category = $block::getCategory();

            $formatted[$category][$block] = $block::getBlockName();
        }

        return $formatted;
    }

    private function findPageBuilderBlock($id): Model|null
    {
        return $this->getRecord()->{$this->relationship}()->find($id);
    }

    private function getBlockSchema(string $blockType, ?Model $record = null, Component $component, Page $livewire): array
    {
        return $blockType::getBlockSchema(
            record: $record,
            action: $this,
            component: $component,
            livewire: $livewire
        );
    }

    public function relationship(
        string $relationship,
    ) {
        $this->relationship = $relationship;

        $this->loadStateFromRelationshipsUsing(function ($record, PageBuilder $component) {
            /** @var Collection */
            $blocks = $record->{$this->relationship}()
                ->whereIn('block_type', $component->getBlocks())
                ->get();

            $component->state($blocks->toArray());
        });

        return $this;
    }
}
