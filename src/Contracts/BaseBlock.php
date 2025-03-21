<?php

namespace RedberryProducts\PageBuilderPlugin\Contracts;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

abstract class BaseBlock
{
    use EvaluatesClosures;

    public static function getBlockName(): string
    {
        return class_basename(static::class);
    }

    public static function getBlockSchema(...$arguments): array
    {
        if (! method_exists(static::class, 'blockSchema')) {
            throw new \Exception('Method blockSchema not found in ' . static::class);
        }
        $closure = Closure::fromCallable([static::class, 'blockSchema']);
        return app()->call($closure, $arguments);
    }

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

        return static::getBlockName() . ' - ' . $index + 1;
    }
}
