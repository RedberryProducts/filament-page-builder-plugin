<?php

namespace Redberry\PageBuilderPlugin\Traits;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Attributes\Computed;

trait ComponentLoadsPageBuilderBlocks
{
    public ?string $relationship = null;

    public ?Closure $modifyRelationshipQueryUsing = null;

    public array|Closure $blocks = [];

    public function getConstrainAppliedQuery(Model $record): Relation
    {
        $query = $record->{$this->relationship}()
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
}
