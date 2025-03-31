<?php

namespace RedberryProducts\PageBuilderPlugin\Traits;

use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;

trait ComponentLoadsPageBuilderBlocks
{
    public ?string $relationship = null;

    public ?Closure $modifyRelationshipQueryUsing = null;

    public array | Closure $blocks = [];

    public function getConstrainAppliedQuery(Model $record)
    {
        $query =  $record->{$this->relationship}()
            ->whereIn('block_type', $this->getBlocks());

        if ($this->modifyRelationshipQueryUsing) {
            $query = $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $query,
                'record' => $record,
            ]) ?? $query;
        }

        return $query;
    }

    public function blocks(
        array | Closure $blocks,
    ) {
        $this->blocks = $blocks;
        Select::class;
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
}
