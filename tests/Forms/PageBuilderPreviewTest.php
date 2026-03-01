<?php

use Filament\Schemas\Schema;
use Illuminate\View\View;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilder;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilderPreview;
use Redberry\PageBuilderPlugin\Models\PageBuilderBlock;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Blocks\ViewBlock;
use Redberry\PageBuilderPlugin\Tests\Fixtures\FormComponent;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Models\Page;

use function Pest\Laravel\startSession;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->page = Page::factory()->create();
    startSession();
});

it('data will change and all valid blocks will be previewed', function () {
    $blocks = PageBuilderBlock::factory(2)->sequence(
        [
            'block_type' => ViewBlock::class,
        ],
        [
            'block_type' => 'text',
        ]
    )->create([
        'page_builder_blockable_id' => $this->page->id,
        'page_builder_blockable_type' => Page::class,
        'data' => [
            'image' => 'https://example.com/image.jpg',
            'hero_button' => [
                'text' => 'hero button text',
            ],
        ],
    ]);

    livewire(TestComponentWithPageBuilderAndPreview::class)
        ->assertSet(
            'data.website_content.0.block_type',
            ViewBlock::class
        )
        ->assertSet(
            'data.website_content.0.data.image',
            'https://example.com/image.jpg'
        )
        ->assertSet(
            'data.website_content.0.data.hero_button.text',
            'hero button text'
        )
        ->assertSeeHtml('hero button text')
        ->mountFormComponentAction('website_content', 'edit', [
            'index' => 0,
            'item' => $blocks->first()->id,
        ])
        ->assertFormComponentActionMounted('website_content', ['edit'])
        ->fillForm([
            'data' => [
                'hero_button' => [
                    'text' => 'Test 123',
                    'url' => 'https://example.com',
                ],
            ],
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors()
        ->assertDontSeeHtml('hero button text')
        ->assertSeeHtml('Test 123');
});

class TestComponentWithPageBuilderAndPreview extends FormComponent
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                PageBuilder::make('website_content')
                    ->reorderable()
                    ->blocks([
                        ViewBlock::class,
                    ]),
                PageBuilderPreview::make('website_content_preview')
                    ->pageBuilderField('website_content'),
            ])
            ->model(Page::first() ?? app(Page::class))
            ->statePath('data');
    }

    public function mount(): void
    {
        $this->form->fill();
        $this->form->loadStateFromRelationships();
    }

    public function render(): View
    {
        return view('form');
    }
}
