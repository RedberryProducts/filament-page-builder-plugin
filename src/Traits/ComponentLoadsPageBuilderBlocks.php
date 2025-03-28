<?php

namespace RedberryProducts\PageBuilderPlugin\Traits;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;

trait ComponentLoadsPageBuilderBlocks
{
    public ?string $relationship = null;

    public array | Closure $blocks = [];

    public function getConstrainAppliedQuery(Model $record)
    {
        // TODO: refactor this to not use relationship function
        return $record->{$this->relationship}()
            ->whereIn('block_type', $this->getBlocks());
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
}
