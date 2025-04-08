<?php

namespace Redberry\PageBuilderPlugin\Traits;

use Closure;

trait FormatsBlockLabelWithContext
{
    public function getBlockLabel(string $blockType, array $state, int $index)
    {
        $closure = Closure::fromCallable([$blockType, 'getBlockLabel']);

        return (string) $this->evaluate($closure, [
            'state' => $state,
            'index' => $index,
        ]);
    }
}
