<?php

namespace Redberry\PageBuilderPlugin\Components\Forms;

use Closure;
use Filament\Forms\Components\Concerns\CanDisableOptions;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasGridDirection;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Field;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\ComponentAttributeBag;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;
use Redberry\PageBuilderPlugin\Traits\FormatsBlockCategories;

class RadioButtonImage extends Field
{
    use CanDisableOptions;
    use HasExtraInputAttributes;
    use HasGridDirection;
    use HasOptions;
    use FormatsBlockCategories;

    public string $view = 'page-builder-plugin::forms.radio-button-image';

    public ComponentAttributeBag|Closure|null $allTabAttributes = null;

    /**
     * Returns the thumbnail for the given block type.
     *
     * @param  class-string<BaseBlock>  $blockType
     * @return string|null
     */
    public function getBlockThumbnail(string $blockType): string|Htmlable|null
    {
        $closure = Closure::fromCallable([$blockType, 'getThumbnail']);

        return $this->evaluate($closure) ?? null;
    }

    public function allTabAttributes(
        ComponentAttributeBag|Closure $attributes = new ComponentAttributeBag()
    ): static {
        $this->allTabAttributes = $attributes;

        return $this;
    }

    public function getAllTabAttributes(): ComponentAttributeBag
    {
        return $this->evaluate($this->allTabAttributes) ?? new ComponentAttributeBag();
    }

    public function getFormattedOptions(): array
    {
        $options = $this->getOptions();

        if (is_callable($options)) {
            $options = $this->evaluate($options);
        }

        $options = collect($options);

        return $options->flatMap(function ($categoryOptions, $category) {
            return collect($categoryOptions)->mapWithKeys(function ($option, $key) use ($category) {
                return [
                    $key => [
                        'label' => $option,
                        'thumbnail' => $this->getBlockThumbnail($key),
                        'category' => $this->getCategoryTitle($category),
                        'category_class' => $category,
                        'class' => $key,
                    ],
                ];
            });
        })->toArray();
    }
}
