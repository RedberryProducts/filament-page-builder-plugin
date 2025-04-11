<?php

use Filament\Infolists\Infolist;
use Redberry\PageBuilderPlugin\Components\Infolist\PageBuilderEntry;
use Redberry\PageBuilderPlugin\Components\Infolist\PageBuilderPreviewEntry;
use Redberry\PageBuilderPlugin\Models\PageBuilderBlock;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Blocks\ViewBlock;
use Redberry\PageBuilderPlugin\Tests\Fixtures\InfolistComponent;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Models\Page;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->page = Page::factory()->create();
});

it('only valid blocks will be rendered in list', function () {
    PageBuilderBlock::factory(3)->sequence(
        [
            'block_type' => ViewBlock::class,
            'data' => [
                'image' => 'https://example.com/image.jpg',
                'hero_button' => [
                    'text' => 'hero button text',
                    'url' => 'https://example.com',
                ],
            ],
            'order' => 0,
        ],
        [
            'block_type' => 'InvalidBlock',
            'data' => [
                'image' => 'https://example.com/image.jpg',
                'hero_button' => [
                    'text' => 'invalid button text',
                    'url' => 'https://example.com',
                ],
            ],
        ],
        [
            'block_type' => ViewBlock::class,
            'data' => [
                'image' => 'https://example.com/image.jpg',
                'hero_button' => [
                    'text' => 'second hero button text',
                    'url' => 'https://example.com',
                ],
            ],
            'order' => 3,
        ]
    )->create([
        'page_builder_blockable_id' => $this->page->id,
        'page_builder_blockable_type' => Page::class,
    ]);

    livewire(TestInfolistComponentWithPageBuilderAndPreview::class)
        ->assertSeeHtml('ViewBlock')
        ->assertDontSeeHtml('InvalidBlock')
        ->assertSeeHtml('hero button text')
        ->assertDontSeeHtml('invalid button text')
        ->assertSeeHtmlInOrder([
            'hero button text',
            'second hero button text',
        ]);
});

class TestInfolistComponentWithPageBuilderAndPreview extends InfolistComponent
{
    public function infolist(Infolist $infolist)
    {
        return $infolist
            ->record(Page::first())
            ->schema([
                PageBuilderEntry::make('website_content')
                    ->blocks([
                        ViewBlock::class,
                    ]),
                PageBuilderPreviewEntry::make('website_content_preview')
                    ->blocks([
                        ViewBlock::class,
                    ]),
            ]);
    }
}
