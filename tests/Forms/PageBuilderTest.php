<?php

use Filament\Schemas\Schema;
use Illuminate\View\View;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilder;
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

it('can create new block', function () {
    $livewire = livewire(TestcomponentWithPageBuilderRenderedUsingViews::class)
        ->mountFormComponentAction('website_content', 'create', arguments: [
            'block_type' => ViewBlock::class,
        ])
        ->assertFormComponentActionMounted('website_content', 'create')
        ->setFormComponentActionData([
            'data' => [
                'hero_button' => [
                    'text' => 'Test 123',
                    'url'  => 'https://example.com',
                ],
            ],
        ])
        ->callMountedFormComponentAction()
        ->assertHasNoFormComponentActionErrors()
        ->assertFormComponentActionNotMounted('website_content', 'create');


    $state = $livewire->get('data.website_content');

    expect($state)->toHaveCount(1)
        ->and($state[0]['block_type'])->toBe(ViewBlock::class)
        ->and($state[0]['data']['hero_button']['text'])->toBe('Test 123')
        ->and($state[0]['data']['hero_button']['url'])->toBe('https://example.com');
});


it('can edit existing block', function () {
    $block = PageBuilderBlock::factory()->create([
        'block_type' => ViewBlock::class,
        'page_builder_blockable_id' => $this->page->id,
        'page_builder_blockable_type' => Page::class,
        'data' => [
            'image' => 'https://example.com/image.jpg',
            'hero_button' => [
                'text' => 'Test',
                'url' => 'https://example.com',
            ],
        ],
    ]);
     livewire(TestComponentWithPageBuilderRenderedUsingViews::class)
        ->mountFormComponentAction('website_content', 'edit', ['index' => 0, 'item' => $block->id])
        ->setFormComponentActionData([
            'data' => [
                'hero_button' => [
                    'text' => 'Test 123',
                    'url'  => 'https://example.com',
                ],
            ],
        ])
        ->callMountedFormComponentAction()
        ->assertSet('data.website_content.0.id', $block->id)
        ->assertSet('data.website_content.0.block_type', ViewBlock::class)
        ->assertSet('data.website_content.0.data.image', 'https://example.com/image.jpg')
        ->assertSet('data.website_content.0.data.hero_button.text', 'Test 123')
        ->assertSet('data.website_content.0.data.hero_button.url', 'https://example.com');
});

it('can delete existing block', function () {
    $block = PageBuilderBlock::factory()->create([
        'block_type' => ViewBlock::class,
        'page_builder_blockable_id' => $this->page->id,
        'page_builder_blockable_type' => Page::class,
        'data' => [
            'image' => 'https://example.com/image.jpg',
            'hero_button' => [
                'text' => 'Test',
                'url' => 'https://example.com',
            ],
        ],
    ]);
    livewire(TestComponentWithPageBuilderRenderedUsingViews::class)
        ->mountFormComponentAction('website_content', 'delete', ['index' => 0, 'item' => $block->id])
        ->callMountedFormComponentAction()
        ->assertFormSet([
            'website_content' => [
            ],
        ]);
});

it('can reorder existing blocks', function () {
    $blocks = PageBuilderBlock::factory()->count(3)->sequence(
        ['order' => 0],
        ['order' => 1],
        ['order' => 2],
    )->create([
        'block_type' => ViewBlock::class,
        'page_builder_blockable_id' => $this->page->id,
        'page_builder_blockable_type' => Page::class,
        'data' => [
            'image' => 'https://example.com/image.jpg',
            'hero_button' => [
                'text' => 'Test',
                'url' => 'https://example.com',
            ],
        ],
    ]);

    $livewire = livewire(TestComponentWithPageBuilderRenderedUsingViews::class)
        ->mountFormComponentAction('website_content', 'reorder', [
            'items' => [
                $blocks->get(2)->id,
                $blocks->get(0)->id,
                $blocks->get(1)->id,
            ],
        ])
        ->callMountedFormComponentAction();

    $livewire
        ->assertSet('data.website_content.0.id', $blocks->get(2)->id)
        ->assertSet('data.website_content.1.id', $blocks->get(0)->id)
        ->assertSet('data.website_content.2.id', $blocks->get(1)->id);
});

class TestComponentWithPageBuilderRenderedUsingViews extends FormComponent
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
