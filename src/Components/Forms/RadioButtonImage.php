<?php

namespace Redberry\PageBuilderPlugin\Components\Forms;

use Closure;
use Filament\Forms\Components\Concerns\CanDisableOptions;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasGridDirection;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Field;
use Illuminate\Contracts\Support\Htmlable;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;

class RadioButtonImage extends Field
{
    use CanDisableOptions;
    use HasExtraInputAttributes;
    use HasGridDirection;
    use HasOptions;

    public string $view = 'page-builder-plugin::forms.radio-button-image';

    /**
     * Returns the thumbnail for the given block type.
     *
     * @param  class-string<BaseBlock>  $blockType
     * @return string|null
     */
    public function getBlockThumbnail(string $blockType): string | Htmlable | null
    {
        $closure = Closure::fromCallable([$blockType, 'getThumbnail']);

        return $this->evaluate($closure) ?? null;
    }
}
