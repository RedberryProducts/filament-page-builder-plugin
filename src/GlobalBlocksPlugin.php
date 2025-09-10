<?php

namespace Redberry\PageBuilderPlugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource;

class GlobalBlocksPlugin implements Plugin
{
    protected bool $enableGlobalBlocksResource = true;
    protected string $globalResourceClass = GlobalBlockConfigResource::class;

    public static function make(): static
    {
        return app(static::class);
    }

    public function enableGlobalBlocks(bool $enable = true): static
    {
        $this->enableGlobalBlocksResource = $enable;
        return $this;
    }

    public function resource(string $resourceClass): static
    {
        $this->globalResourceClass = $resourceClass;
        return $this;
    }

    public function getId(): string
    {
        return 'page-builder-global-blocks';
    }

    public function register(Panel $panel): void
    {
        if (!$this->enableGlobalBlocksResource) {
            return;
        }

        if (!class_exists($this->globalResourceClass)) {
            return;
        }

        $panel->resources([
            $this->globalResourceClass,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
