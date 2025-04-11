<?php

namespace Redberry\PageBuilderPlugin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Models\Page;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
