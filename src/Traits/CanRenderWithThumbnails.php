<?php

namespace Redberry\PageBuilderPlugin\Traits;

use Closure;

trait CanRenderWithThumbnails
{
    protected bool | Closure $renderWithThumbnails = false;

    public function renderWithThumbnails(
        bool | Closure $renderWithThumbnails = true,
    ) {
        $this->renderWithThumbnails = $renderWithThumbnails;

        return $this;
    }

    public function getRenderWithThumbnails(): bool
    {
        return (bool) $this->evaluate($this->renderWithThumbnails);
    }
}
