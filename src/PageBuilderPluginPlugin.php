<?php

namespace Redberry\PageBuilderPlugin;

use Filament\Contracts\Plugin;
use Filament\Panel;

class PageBuilderPluginPlugin implements Plugin
{
    public function getId(): string
    {
        return 'page-builder-plugin';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
