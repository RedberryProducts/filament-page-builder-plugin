<?php

namespace RedberryProducts\PageBuilderPlugin\Contracts;

abstract class BaseBlock
{
    public static function getBlockName(): string
    {
        return class_basename(static::class);
    }

    // TODO: consider allowing injecting of livewire/form/component/record for dynamic schemas.
    abstract public static function getBlockSchema(): array;

    public static function getCategory(): string
    {
        return '';
    }

    public static function getThumbnail(): string
    {
        return '';
    }

    public static function formatForListing(array $data): array
    {
        return $data;
    }

    public static function formatForSinglePreview(array $data): array
    {
        return $data;
    }

    public static function getBlockTitleAttribute(): ?string
    {
        return null;
    }

    public static function getBlockLabel(array $state, int $index)
    {
        $key = static::getBlockTitleAttribute();

        if ($key) {
            $label = data_get($state, $key);
            if ($label) {
                return static::getBlockName() . ' - ' . $label . ':';
            }
        }

        return static::getBlockName() . ' - ' . $index;
    }
}
