<?php

namespace Redberry\PageBuilderPlugin\Abstracts;

use Illuminate\View\ComponentAttributeBag;

abstract class BaseBlockCategory
{
    abstract public static function getCategoryName(): string;

    public static function getCategoryIcon(): ?string
    {
        return null;
    }

    public static function getCategoryAttributes(): ComponentAttributeBag
    {
        return new ComponentAttributeBag;
    }
}
