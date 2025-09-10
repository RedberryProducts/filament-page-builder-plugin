<?php

namespace Redberry\PageBuilderPlugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource;

class GlobalBlocksPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'page-builder-global-blocks';
    }

    public function register(Panel $panel): void
    {
        if (! config('page-builder-plugin.global_blocks.enabled', true)) {
            return;
        }

        $resourceClass = config(
            'page-builder-plugin.global_blocks.resource',
            GlobalBlockConfigResource::class
        );

        if (! class_exists($resourceClass)) {
            return;
        }

        $panel->resources([
            $resourceClass,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
