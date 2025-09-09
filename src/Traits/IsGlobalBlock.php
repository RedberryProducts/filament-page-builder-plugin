<?php

namespace Redberry\PageBuilderPlugin\Traits;

use Redberry\PageBuilderPlugin\Models\GlobalBlockConfig;

trait IsGlobalBlock
{
    /**
     * Check if this is a global block
     */
    public static function isGlobalBlock(): bool
    {
        return true;
    }

    /**
     * Get the global configuration for this block
     */
    public static function getGlobalConfig(): ?GlobalBlockConfig
    {
        return GlobalBlockConfig::getForClass(static::class);
    }

    /**
     * Apply global configuration to the schema
     * For global blocks, return empty schema since configuration is managed centrally
     */
    public static function applyGlobalConfiguration(array $schema): array
    {
        // Global blocks should not show their configuration fields in the page builder
        // Configuration is managed in the Global Blocks resource
        return [];
    }

    /**
     * Get the configured data for this global block
     */
    public static function getGlobalBlockData(): array
    {
        $config = static::getGlobalConfig();
        return $config ? ($config->configuration ?? []) : [];
    }

    /**
     * Create or update the global block configuration
     */
    public static function updateGlobalConfig(array $configuration): void
    {
        $config = static::getGlobalConfig();
        
        if ($config) {
            $config->update(['configuration' => $configuration]);
        } else {
            GlobalBlockConfig::create([
                'name' => static::getBlockDisplayName(),
                'class_name' => static::class,
                'configuration' => $configuration,
            ]);
        }
    }

    /**
     * Get display name for this block
     */
    public static function getBlockDisplayName(): string
    {
        $shortName = class_basename(static::class);
        return str($shortName)->headline()->toString();
    }

    /**
     * Override the format methods to use global configuration
     */
    public static function formatForSingleView(array $data, ?object $record = null): array
    {
        $globalData = static::getGlobalBlockData();
        $mergedData = array_merge($data, $globalData);
        
        if (method_exists(parent::class, 'formatForSingleView')) {
            return parent::formatForSingleView($mergedData, $record);
        }
        
        return $mergedData;
    }

    public static function formatForListingView(array $data, ?object $record = null): array
    {
        return static::formatForSingleView($data, $record);
    }
}