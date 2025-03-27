<?php

namespace RedberryProducts\PageBuilderPlugin\Contracts;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

// TODO: this should not be in contracts...
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
        return self::formatForSinglePreview($data);
    }

    private static function generatedStorageUrl(string $path): string
    {
        return Storage::url($path);
    }

    public static function getUrlForFile(array | string | null $path = null): ?string
    {
        if (! $path) {
            return null;
        }

        if (is_string($path)) {
            return static::generatedStorageUrl($path);
        }

        if (count($path) > 0) {
            $filePath = array_values($path)[0];
            if (is_string($filePath)) {
                return static::generatedStorageUrl($filePath);
            }
            if ($filePath instanceof TemporaryUploadedFile) {
                return $filePath->temporaryUrl();
            }
        }

        return null;
    }

    public static function formatForSinglePreview(array $data): array
    {
        return $data;
    }

    public static function getBlockTitleAttribute(): ?string
    {
        return null;
    }

    public static function getView(): ?string
    {
        return null;
    }

    public static function getBlockLabel(array $state, ?int $index = null)
    {
        $key = static::getBlockTitleAttribute();

        if ($key) {
            $label = data_get($state, $key);
            if ($label) {
                return static::getBlockName() . ' - ' . $label . ':';
            }
        }

        if (is_null($index)) {
            return static::getBlockName();
        }

        return static::getBlockName() . ' - ' . $index + 1;
    }
}
