<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Forms;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use Filament\Pages\Page;
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

    protected ?Closure $renderEditActionButtonUsing = null;

    public array | Closure $blocks = [];

    public string $view = 'page-builder-plugin::forms.page-builder';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpanFull();

        $this->relationship('pageBuilderBlocks');

        $this->registerActions([
            fn (PageBuilder $component): Action => $component->getDeleteAction(),
            fn (PageBuilder $component): Action => $component->getEditAction(),
        ]);
    }

    // TODO: move this function to its own file for making reading easier.
    public function getEditAction(): Action
    {
        $action = Action::make($this->getEditActionName())
            ->successNotificationTitle('Block updated')
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
            ->action(function ($arguments, $data) {
                $block = $this->findPageBuilderBlock($arguments['item']);

                $block->update([
                    'data' => $data,
                ]);
            });

        if ($this->modifyEditActionUsing) {
            $action = $this->evaluate($this->modifyEditActionUsing, [
                'action' => $action,
            ]);
        }

        return $action;
    }

    #[Computed(true)]
    public function getDeleteAction(): Action
    {
        $action = Action::make($this->getDeleteActionName())
            ->requiresConfirmation()
            ->color('danger')
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

    private function findPageBuilderBlock($id): ?Model
    {
        return $this->getRecord()->{$this->relationship}()->find($id);
    }

    private function getBlockSchema(string $blockType, Model $record, Component $component, Page $livewire): array
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
