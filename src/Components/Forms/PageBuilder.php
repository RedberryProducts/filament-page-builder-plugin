<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Forms;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Pages\Page;
use Filament\Support\Components\Component;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use RedberryProducts\PageBuilderPlugin\Components\Forms\Actions\CreatePageBuilderBlockAction;
use RedberryProducts\PageBuilderPlugin\Components\Forms\Actions\DeletePageBuilderBlockAction;
use RedberryProducts\PageBuilderPlugin\Components\Forms\Actions\EditPageBuilderBlockAction;
use RedberryProducts\PageBuilderPlugin\Components\Forms\Actions\SelectBlockAction;

// TODO: make this reorder-able
class PageBuilder extends Field
{
    public ?string $relationship = null;

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
        $action = CreatePageBuilderBlockAction::make($this->getCreateActionName())
            ->disabled($this->isDisabled());

        if ($this->modifyCreateActionUsing) {
            $action = $this->evaluate($this->modifyCreateActionUsing, [
                'action' => $action,
            ]);
        }

        return $action;
    }

    public function getSelectBlockAction(): Action
    {
        $action = SelectBlockAction::make($this->getSelectBlockActionName())
            ->label('Add block')
            ->translateLabel()
            ->disabled($this->isDisabled());

        if ($this->modifySelectBlockActionUsing) {
            $action = $this->evaluate($this->modifySelectBlockActionUsing, [
                'action' => $action,
            ]);
        }

        return $action;
    }

    public function getEditAction(): Action
    {
        $action = EditPageBuilderBlockAction::make($this->getEditActionName())
            ->disabled($this->isDisabled());

        if ($this->modifyEditActionUsing) {
            $action = $this->evaluate($this->modifyEditActionUsing, [
                'action' => $action,
            ]);
        }

        return $action;
    }

    public function getDeleteAction(): Action
    {
        $action = DeletePageBuilderBlockAction::make($this->getDeleteActionName())
            ->disabled($this->isDisabled());

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

    public function createAction(
        Closure $modifyCreateActionUsing,
    ) {
        $this->modifyCreateActionUsing = $modifyCreateActionUsing;

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

    public function findPageBuilderBlock($id): ?Model
    {
        return $this->getRecord()->{$this->relationship}()->find($id);
    }

    public function getBlockSchema(string $blockType, ?Model $record, Component $component, Page $livewire): array
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

        $this->saveRelationshipsUsing(function (PageBuilder $component, Model $record, $state) {
            $record->{$this->relationship}()->upsert(array_map(function ($item) {
                return [
                    'id' => $item['id'] ?? null,
                    'block_type' => $item['block_type'],
                    'data' => json_encode($item['data'] ?? []),
                ];
            }, $state), uniqueBy: ['id'], update: ['data']);
        });

        $this->dehydrated(false);

        return $this;
    }

    public function renderPreviewWithIframes(
        bool|Closure $value = true,
        string|Closure $createUrl,
        string|Closure $updateUrl,
    ) {
        $value = (bool) $this->evaluate($value);

        $this->createAction(function (CreatePageBuilderBlockAction $action) use ($createUrl) {
            return $action->pageBuilderPreviewField(function (PageBuilderPreview $field) use ($createUrl) {
                return $field->iframeUrl($createUrl)->renderWithIframe();
            });
        });

        $this->editAction(function (EditPageBuilderBlockAction $action) use ($updateUrl) {
            return $action->pageBuilderPreviewField(function (PageBuilderPreview $field) use ($updateUrl) {
                return $field->iframeUrl($updateUrl)->renderWithIframe();
            });
        });

        return $this;
    }
}
