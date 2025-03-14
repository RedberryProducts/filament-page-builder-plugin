<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Forms;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;

// TODO: make this reorder-able
class PageBuilder extends Field
{
    public ?string $relationship = null;

    public ?Closure $renderDeleteActionButtonUsing = null;

    public array|Closure $blocks = [];

    public string $view = 'page-builder-plugin::forms.page-builder';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpanFull();

        $this->relationship('pageBuilderBlocks');

        $this->registerActions([
            fn (PageBuilder $component): Action => $component->getDeleteAction(),
        ]);
    }

    #[Computed(true)]
    public function getDeleteAction(): Action
    {
        return Action::make($this->getDeleteActionName())
            ->requiresConfirmation()
            ->color('danger')
            ->action(function () {
                dd('delete');
            });
    }

    public function renderDeleteActionButton()
    {
        return $this->renderDeleteActionButtonUsing
            ? $this->evaluate($this->renderDeleteActionButtonUsing)
            : view('filament::components.button.index', [
                'slot' => $this->getDeleteAction()->getLabel(),
                'icon' => 'heroicon-o-trash',
            ]);
    }

    public function getDeleteActionName(): string
    {
        return 'delete';
    }

    public function blocks(
        array|Closure $blocks,
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
