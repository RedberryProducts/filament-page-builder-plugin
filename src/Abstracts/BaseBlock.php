<?php

namespace Redberry\PageBuilderPlugin\Abstracts;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

abstract class BaseBlock
{
    public static function getBlockName(): string
    {
        return class_basename(static::class);
    }

    abstract public static function getBlockSchema(): array;

    /**
     * @return class-string<BaseCategory>|string
     */
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

    public static function getBlockTitleAttribute(): ?string
    {
        return null;
    }

    public static function getView(): ?string
    {
        return null;
    }

    public static function getThumbnail(): string | Htmlable | null
    {
        return null;
    }

    public static function getIsSelectionDisabled(): bool
    {
        return false;
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

    public static function getBaseBlockSchema(?object $record = null): array
    {
        return [
            // schema
        ];
    }
}
