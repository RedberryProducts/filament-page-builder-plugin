<?php

namespace Redberry\PageBuilderPlugin\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasPageBuilder
{
    public function pageBuilderBlocks(): MorphMany
    {
        return $this->morphMany(config('page-builder-plugin.block_model_class'), 'page_builder_blockable');
    }
}
