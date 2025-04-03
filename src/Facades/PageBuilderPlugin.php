<?php

namespace Redberry\PageBuilderPlugin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Redberry\PageBuilderPlugin\PageBuilderPlugin
 */
class PageBuilderPlugin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Redberry\PageBuilderPlugin\PageBuilderPlugin::class;
    }
}
