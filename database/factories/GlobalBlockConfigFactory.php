<?php

namespace Redberry\PageBuilderPlugin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Redberry\PageBuilderPlugin\Models\GlobalBlockConfig;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Blocks\GlobalViewBlock;

class GlobalBlockConfigFactory extends Factory
{
    protected $model = GlobalBlockConfig::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'class_name' => GlobalViewBlock::class,
            'configuration' => [
                'title' => $this->faker->sentence,
                'content' => $this->faker->paragraph,
                'button_text' => $this->faker->word,
            ],
        ];
    }
}
