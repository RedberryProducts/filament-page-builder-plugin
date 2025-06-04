<?php

namespace Redberry\PageBuilderPlugin\Traits;

use Closure;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlockCategory;

trait FormatsBlockCategories
{
    public function isCategoryClass(string $category): bool
    {
        return class_exists($category) && is_subclass_of($category, BaseBlockCategory::class);
    }

    public function getCategoryTitle(string $category): string
    {
        return class_exists($category) && method_exists($category, 'getCategoryName')
            ? (string) $this->evaluate(Closure::fromCallable([$category, 'getCategoryName']))
            : $category;
    }

}
