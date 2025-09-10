<?php

use Redberry\PageBuilderPlugin\Models\GlobalBlockConfig;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Blocks\GlobalViewBlock;

beforeEach(function () {
    copy(
        __DIR__ . '/../../database/migrations/create_global_block_configs_table.php.stub',
        database_path('migrations/create_global_block_configs_table.php')
    );

    $this->artisan('migrate', ['--database' => 'testing'])->run();
});

afterEach(function () {
    unlink(database_path('migrations/create_global_block_configs_table.php'));
});

it('can create a global block config', function () {
    $config = GlobalBlockConfig::create([
        'name' => 'Test Block',
        'class_name' => GlobalViewBlock::class,
        'configuration' => [
            'title' => 'Test Title',
            'content' => 'Test Content',
        ],
    ]);

    expect($config)->toBeInstanceOf(GlobalBlockConfig::class)
        ->and($config->name)->toBe('Test Block')
        ->and($config->class_name)->toBe(GlobalViewBlock::class)
        ->and($config->configuration)->toBe([
            'title' => 'Test Title',
            'content' => 'Test Content',
        ]);
});

it('can get config value', function () {
    $config = GlobalBlockConfig::create([
        'name' => 'Test Block',
        'class_name' => GlobalViewBlock::class,
        'configuration' => [
            'title' => 'Test Title',
            'content' => 'Test Content',
        ],
    ]);

    expect($config->getConfigValue('title'))->toBe('Test Title')
        ->and($config->getConfigValue('content'))->toBe('Test Content')
        ->and($config->getConfigValue('non_existent'))->toBeNull();
});

it('handles missing configuration gracefully', function () {
    $config = GlobalBlockConfig::create([
        'name' => 'Test Block',
        'class_name' => GlobalViewBlock::class,
        'configuration' => null,
    ]);

    expect($config->getConfigValue('title'))->toBeNull();
});
