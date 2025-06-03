<?php

namespace Redberry\PageBuilderPlugin\Components\Forms;

use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\View\ComponentAttributeBag;
use Redberry\PageBuilderPlugin\Components\Forms\Actions\CreatePageBuilderBlockAction;
use Redberry\PageBuilderPlugin\Components\Forms\Actions\DeletePageBuilderBlockAction;
use Redberry\PageBuilderPlugin\Components\Forms\Actions\EditPageBuilderBlockAction;
use Redberry\PageBuilderPlugin\Components\Forms\Actions\ReorderPageBuilderBlockAction;
use Redberry\PageBuilderPlugin\Components\Forms\Actions\SelectBlockAction;
use Redberry\PageBuilderPlugin\Traits\CanRenderWithThumbnails;
use Redberry\PageBuilderPlugin\Traits\ComponentLoadsPageBuilderBlocks;
use Redberry\PageBuilderPlugin\Traits\FormatsBlockLabelWithContext;

class PageBuilder extends Field
{
    use ComponentLoadsPageBuilderBlocks;
    use FormatsBlockLabelWithContext;
    use CanRenderWithThumbnails;

    public bool | Closure $reorderable = false;

    protected ?Closure $renderDeleteActionButtonUsing = null;

    protected ?Closure $modifyDeleteActionUsing = null;

    protected ?Closure $modifyEditActionUsing = null;

    protected ?Closure $modifyCreateActionUsing = null;

    protected ?Closure $modifySelectBlockActionUsing = null;

    protected ?Closure $renderEditActionButtonUsing = null;

    protected ?Closure $modifyReorderActionUsing = null;

    protected ?Closure $renderReorderActionButtonUsing = null;

    public string $view = 'page-builder-plugin::forms.page-builder';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpanFull();

        $this->relationship();

        $this->registerActions([
            fn (self $component): Action => $component->getDeleteAction(),
            fn (self $component): Action => $component->getEditAction(),
            fn (self $component): Action => $component->getSelectBlockAction(),
            fn (self $component): Action => $component->getCreateAction(),
            fn (self $component): Action => $component->getReorderAction(),
        ]);
    }

    public function getReorderAction(): Action
    {
        $action = ReorderPageBuilderBlockAction::make($this->getReorderActionName())
            ->hidden(! $this->getReorderable())
            ->disabled($this->isDisabled());

        if ($this->modifyReorderActionUsing) {
            $action = $this->evaluate($this->modifyReorderActionUsing, [
                'action' => $action,
            ]);
        }

        return $action;
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
            ->renderWithThumbnails($this->getRenderWithThumbnails())
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

    public function renderDeleteActionButton(string $item, int $index)
    {
        $statePath = $this->getStatePath();
        $deleteAction = $this->getDeleteAction();

        $attributes = [
            'slot' => $deleteAction->getLabel(),
            'labelSrOnly' => true,
            'icon' => 'heroicon-o-trash',
            'color' => 'danger',
            'disabled' => $deleteAction->isDisabled(),
            'attributes' => new ComponentAttributeBag([
                'wire:click' => "mountFormComponentAction('$statePath', '{$this->getDeleteActionName()}', { item: '$item', index: '$index' } )",
            ]),
        ];

        if ($this->renderDeleteActionButtonUsing) {
            return $this->evaluate($this->renderDeleteActionButtonUsing, [
                'action' => $deleteAction,
                'item' => $item,
                'index' => $index,
                'attributes' => $attributes,
            ]);
        }

        return view('filament::components.button.index', $attributes);
    }

    public function renderEditActionButton(string $item, $index)
    {
        $statePath = $this->getStatePath();
        $editAction = $this->getEditAction();

        $attributes = [
            'slot' => $editAction->getLabel(),
            'labelSrOnly' => true,
            'icon' => 'heroicon-o-pencil-square',
            'disabled' => $editAction->isDisabled(),
            'color' => 'primary',
            'attributes' => new ComponentAttributeBag([
                'wire:click' => "mountFormComponentAction('$statePath', '{$this->getEditActionName()}', { item: '$item', index: '$index' } )",
            ]),
        ];

        if ($this->renderEditActionButtonUsing) {
            return $this->evaluate($this->renderEditActionButtonUsing, [
                'action' => $editAction,
                'item' => $item,
                'index' => $index,
                'attributes' => $attributes,
            ]);
        }

        return view('filament::components.button.index', $attributes);
    }

    public function renderReorderActionButton(string $item, $index)
    {
        $reorderAction = $this->getReorderAction();

        $attributes = [
            'icon' => 'heroicon-o-arrows-up-down',
            'disabled' => $reorderAction->isDisabled(),
            'color' => 'gray',
            'attributes' => new ComponentAttributeBag([
                'x-sortable-handle' => 'x-sortable-handle',
                'x-on:click.stop' => 'x-on:click.stop',
            ]),
        ];

        if ($this->renderReorderActionButtonUsing) {
            return $this->evaluate($this->renderReorderActionButtonUsing, [
                'action' => $reorderAction,
                'item' => $item,
                'index' => $index,
                'attributes' => $attributes,
            ]);
        }

        return view('filament::components.icon-button', $attributes);
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

    public function reorderActionButton(Closure $renderReorderActionButtonUsing)
    {
        $this->renderReorderActionButtonUsing = $renderReorderActionButtonUsing;

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

    public function getReorderActionName(): string
    {
        return 'reorder';
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

    public function reorderAction(
        Closure $modifyReorderActionUsing,
    ) {
        $this->modifyReorderActionUsing = $modifyReorderActionUsing;

        return $this;
    }

    public function reorderable(
        bool | Closure $reorderable = true,
    ) {
        $this->reorderable = $reorderable;

        return $this;
    }

    public function getReorderable(): bool
    {
        return (bool) $this->evaluate($this->reorderable);
    }

    public function getBlockSchema(string $blockType): array
    {
        $closure = Closure::fromCallable([$blockType, 'getBlockSchema']);

        return (array) $this->evaluate($closure);
    }

    public function relationship(
        string $relationship = 'pageBuilderBlocks',
        ?Closure $modifyRelationshipQueryUsing = null,
    ) {
        $this->relationship = $relationship;
        $this->modifyRelationshipQueryUsing = $modifyRelationshipQueryUsing;

        $this->loadStateFromRelationshipsUsing(function ($record, PageBuilder $component) {
            /** @var Collection */
            $blocks = $this->getConstrainAppliedQuery($record)
                ->get();

            $component->state($blocks->toArray());
        });

        $this->saveRelationshipsUsing(function (Model $record, $state) {
            $state = $state ?? [];
            $query = $this->getConstrainAppliedQuery($record);
            $existingIds = $query->clone()->pluck('id');

            $recordsNeedingDeletion = $existingIds->diff(collect($state)->pluck('id'));

            try {
                DB::beginTransaction();
                $query->clone()->whereIn('id', $recordsNeedingDeletion)->delete();

                $record->{$this->relationship}()->upsert(array_map(function ($item) use ($record) {
                    $item['updated_at'] = now()->format('Y-m-d H:i:s');
                    $item['created_at'] = (new Carbon($item['created_at'] ?? null))->format('Y-m-d H:i:s');

                    $relationshipName = config('page-builder-plugin.polymorphic_relationship_name', 'page_builder_block');

                    return [
                        ...$item,
                        'data' => json_encode($item['data'] ?? []),
                        "{$relationshipName}_id" => $record->getKey(),
                        "{$relationshipName}_type" => $record->getMorphClass(),
                    ];
                }, $state), uniqueBy: ['id'], update: ['data', 'order', 'updated_at']);

                DB::commit();

            } catch (\Throwable $th) {
                DB::rollBack();

                Notification::make()
                    ->title('failed saving page builder blocks')
                    ->body($th->getMessage())
                    ->danger()
                    ->send();

                throw new Halt();
            }

        });

        $this->dehydrated(false);

        return $this;
    }

    public function renderPreviewWithIframes(
        bool | Closure $condition,
        string | Closure $createUrl,
        string | Closure | null $updateUrl = null,
        bool | Closure $autoResize = true,
    ) {
        $condition = (bool) $this->evaluate($condition);
        $autoResize = (bool) $this->evaluate($autoResize);

        if (! $updateUrl) {
            $updateUrl = $createUrl;
        }

        $this->createAction(function (CreatePageBuilderBlockAction $action) use ($createUrl, $autoResize) {
            return $action->pageBuilderPreviewField(function (PageBuilderPreview $field) use ($createUrl, $autoResize) {
                return $field->iframeUrl($createUrl)->autoResizeIframe($autoResize);
            });
        });

        $this->editAction(function (EditPageBuilderBlockAction $action) use ($updateUrl, $autoResize) {
            return $action->pageBuilderPreviewField(function (PageBuilderPreview $field) use ($updateUrl, $autoResize) {
                return $field->iframeUrl($updateUrl)->autoResizeIframe($autoResize);
            });
        });

        return $this;
    }
}
