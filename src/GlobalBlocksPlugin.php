<?php

namespace Redberry\PageBuilderPlugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource;

class GlobalBlocksPlugin implements Plugin
{
    protected bool $enableGlobalBlocksResource = true;

    protected string $globalResourceClass = GlobalBlockConfigResource::class;

    protected ?string $navigationGroup = null;

    protected ?int $navigationSort = null;

    protected ?string $navigationIcon = null;

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

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationSort(int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function navigationIcon(string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function getId(): string
    {
        return 'page-builder-global-blocks';
    }

    public function register(Panel $panel): void
    {
        if (! $this->enableGlobalBlocksResource) {
            return;
        }

        if (! class_exists($this->globalResourceClass)) {
            return;
        }

        if ($this->navigationGroup !== null) {
            $this->globalResourceClass::navigationGroup($this->navigationGroup);
        }

        if ($this->navigationSort !== null) {
            $this->globalResourceClass::navigationSort($this->navigationSort);
        }

        if ($this->navigationIcon !== null) {
            $this->globalResourceClass::navigationIcon($this->navigationIcon);
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
