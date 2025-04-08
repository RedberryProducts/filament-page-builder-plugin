<?php

namespace Redberry\PageBuilderPlugin\Abstracts;

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

abstract class BaseBlock
{
    public static function getBlockName(): string
    {
        return class_basename(static::class);
    }

    public abstract static function getBlockSchema(): array;

    public static function getCategory(): string
    {
        return '';
    }

    public static function formatForListingView(array $data): array
    {
        return static::formatForSingleView($data);
    }

    public static function formatForSingleView(array $data): array
    {
        return $data;
    }

    public static function generatedStorageUrl(string $path): string
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

        if (is_array($path) && count($path) > 0) {
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

    public static function getBlockTitleAttribute(): ?string
    {
        return null;
    }

    public static function getView(): ?string
    {
        return null;
    }

    public static function getBlockLabel(array $state, ?int $index = null): mixed
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

        return static::getBlockName() . ' - ' . ($index + 1);
    }
}
