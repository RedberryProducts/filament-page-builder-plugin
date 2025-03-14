<?php

namespace RedberryProducts\PageBuilderPlugin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RedberryProducts\PageBuilderPlugin\PageBuilderPlugin
 */
class PageBuilderPlugin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RedberryProducts\PageBuilderPlugin\PageBuilderPlugin::class;
    }
}
