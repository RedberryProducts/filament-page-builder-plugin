<?php

namespace Redberry\PageBuilderPlugin\Traits\Actions;

use Closure;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilderPreview;

trait ModifiesPreviewField
{
    public ?Closure $modifyPreviewUsing = null;

    public function pageBuilderPreviewField(Closure $modifyPreviewUsing): static
    {
        $this->modifyPreviewUsing = $modifyPreviewUsing;

        return $this;
    }

    private function getModifiedPreviewField(PageBuilderPreview $preview, $blockType): mixed
    {
        return $this->evaluate($this->modifyPreviewUsing, [
            'field' => $preview,
            'action' => $this,
            'blockType' => $blockType,
        ]) ?? $preview;
    }
}
