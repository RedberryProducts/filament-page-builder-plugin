<?php

use Filament\Infolists\Infolist;
use Redberry\PageBuilderPlugin\Components\Infolist\PageBuilderEntry;
use Redberry\PageBuilderPlugin\Models\PageBuilderBlock;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Blocks\ViewBlock;
use Redberry\PageBuilderPlugin\Tests\Fixtures\InfolistComponent;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Models\Page;

use function Pest\Laravel\startSession;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->page = Page::factory()->create();
    startSession();
});

it('only valid blocks will be rendered in list', function () {
    PageBuilderBlock::factory(2)->sequence(
        [
            'block_type' => ViewBlock::class,
        ],
        [
            'block_type' => 'InvalidBlock',
        ]
    )->create([
        'page_builder_blockable_id' => $this->page->id,
        'page_builder_blockable_type' => Page::class,
        'data' => [
            'image' => 'https://example.com/image.jpg',
            'hero_button' => [
                'text' => 'hero button text',
                'url' => 'https://example.com',
            ],
        ],
    ]);

    livewire(TestInfolistComponentWithPageBuilder::class)
        ->assertSeeHtml('ViewBlock')
        ->assertDontSeeHtml('InvalidBlock');
});

class TestInfolistComponentWithPageBuilder extends InfolistComponent
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
            ]);
    }
}
