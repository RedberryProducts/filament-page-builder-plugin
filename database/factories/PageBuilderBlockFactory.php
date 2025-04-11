<?php

namespace Redberry\PageBuilderPlugin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Redberry\PageBuilderPlugin\Models\PageBuilderBlock;

class PageBuilderBlockFactory extends Factory
{
    protected $model = PageBuilderBlock::class;

    public function definition()
    {
        return [
            'block_type' => 'text',
            'order' => $this->faker->numberBetween(0, 300),
            'page_builder_blockable_id' => 1,
            'page_builder_blockable_type' => '',
            'data' => [],
        ];
    }
}
