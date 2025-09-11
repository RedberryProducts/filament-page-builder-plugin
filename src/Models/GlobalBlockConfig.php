<?php

namespace Redberry\PageBuilderPlugin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;
use ReflectionClass;

/**
 * @property string $id
 * @property string $name
 * @property class-string<BaseBlock> $class_name
 * @property array $configuration
 */
class GlobalBlockConfig extends Model
{
    use HasFactory;

    protected $table = 'global_block_configs';

    protected $fillable = [
        'name',
        'class_name',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'array',
    ];

    /**
     * Get the configuration value for a specific field
     */
    public function getConfigValue(string $field, $default = null)
    {
        return data_get($this->configuration, $field, $default);
    }

    /**
     * Set a configuration value for a specific field
     */
    public function setConfigValue(string $field, $value): void
    {
        $config = $this->configuration ?? [];
        $config[$field] = $value;
        $this->configuration = $config;
    }

    /**
     * Get configuration for a specific global block class
     */
    public static function getForClass(string $className): ?self
    {
        if (! static::tableExists()) {
            return null;
        }

        return static::where('class_name', $className)->first();
    }

    /**
     * Refresh global blocks by scanning for new global block classes
     */
    public static function refreshGlobalBlocks(): void
    {
        if (! static::tableExists()) {
            return;
        }

        $globalBlocks = static::discoverGlobalBlocks();

        foreach ($globalBlocks as $blockClass) {
            static::firstOrCreate([
                'class_name' => $blockClass,
            ], [
                'name' => static::getBlockDisplayName($blockClass),
                'configuration' => static::getDefaultConfiguration($blockClass),
            ]);
        }
    }

    /**
     * Discover all global block classes
     */
    protected static function discoverGlobalBlocks(): array
    {
        $globalBlocks = [];
        $appPath = app_path();

        $discoveryPaths = config('page-builder-plugin.global_blocks_discovery_paths', [
            'app/Filament/*/Blocks/Globals',
        ]);

        $globalsDirectories = [];

        foreach ($discoveryPaths as $pattern) {
            $fullPattern = $appPath . '/' . ltrim($pattern, 'app/');

            if (str_contains($pattern, '*')) {
                $directories = File::glob($fullPattern);
                $globalsDirectories = array_merge($globalsDirectories, $directories);
            } else {
                if (File::isDirectory($fullPattern)) {
                    $globalsDirectories[] = $fullPattern;
                }
            }
        }

        foreach ($globalsDirectories as $directory) {
            $files = File::glob($directory . '/*.php');

            foreach ($files as $file) {
                $relativePath = str_replace($appPath . '/', '', $file);
                $relativePath = str_replace('.php', '', $relativePath);
                $className = 'App\\' . str_replace('/', '\\', $relativePath);

                if (class_exists($className)) {
                    $reflection = new ReflectionClass($className);
                    if (! $reflection->isAbstract() && $reflection->isSubclassOf('Redberry\\PageBuilderPlugin\\Abstracts\\BaseBlock')) {
                        $globalBlocks[] = $className;
                    }
                }
            }
        }

        return $globalBlocks;
    }

    /**
     * Get display name for a block class
     */
    protected static function getBlockDisplayName(string $className): string
    {
        $shortName = class_basename($className);

        return str($shortName)->headline()->toString();
    }

    /**
     * Get default configuration for a block class by analyzing its schema
     */
    protected static function getDefaultConfiguration(string $className): array
    {
        if (! class_exists($className)) {
            return [];
        }

        try {
            if (method_exists($className, 'getBaseBlockSchema')) {
                $schema = $className::getBaseBlockSchema();
            } else {
                $schema = $className::getBlockSchema();
            }

            $config = [];

            foreach ($schema as $field) {
                if (method_exists($field, 'getName')) {
                    $fieldName = $field->getName();
                    $config[$fieldName] = null;
                }
            }

            return $config;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Check if the table exists in the database
     */
    public static function tableExists(): bool
    {
        try {
            return \Schema::hasTable('global_block_configs');
        } catch (\Exception $e) {
            return false;
        }
    }
}
