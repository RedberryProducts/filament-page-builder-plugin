<?php

use Filament\Forms\Form;
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
    livewire(TestcomponentWithPageBuilderRenderedUsingViews::class)
        ->mountFormComponentAction('website_content', 'select-block')
        ->setFormComponentActionData([
            'block_type' => ViewBlock::class,
        ])
        ->callMountedFormComponentAction()
        ->assertHasNoFormComponentActionErrors()
        ->callMountedFormComponentAction()
        ->assertFormComponentActionMounted('website_content', ['select-block', 'create'])
        ->setFormComponentActionData([
            'data' => [
                'hero_button' => [
                    'text' => 'Test 123',
                    'url' => 'https://example.com',
                ],
            ],
        ])
        ->assertFormComponentActionDataSet([
            'data' => [
                'hero_button' => [
                    'text' => 'Test 123',
                    'url' => 'https://example.com',
                ],
            ],
        ])
        ->callMountedFormComponentAction()
        ->assertFormComponentActionNotMounted('website_content', ['select-block', 'create'])
        ->assertFormSet([
            'website_content' => [
                [
                    'block_type' => ViewBlock::class,
                    'data' => [
                        'hero_button' => [
                            'text' => 'Test 123',
                            'url' => 'https://example.com',
                        ],
                    ],
                ],
            ],
        ]);
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
                    'url' => 'https://example.com',
                ],
            ],
        ])
        ->callMountedFormComponentAction()
        ->assertFormSet([
            'website_content' => [
                [
                    'id' => $block->id,
                    'block_type' => ViewBlock::class,
                    'data' => [
                        'image' => 'https://example.com/image.jpg',
                        'hero_button' => [
                            'text' => 'Test 123',
                            'url' => 'https://example.com',
                        ],
                    ],
                ],
            ],
        ]);
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
        [
            'order' => 0,
        ],
        [
            'order' => 1,
        ],
        [
            'order' => 2,
        ],
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
    livewire(TestComponentWithPageBuilderRenderedUsingViews::class)
        ->assertFormSet([
            'website_content' => $blocks->map(function ($block) {
                return [
                    'id' => $block->id,
                ];
            })->toArray(),
        ])
        ->mountFormComponentAction('website_content', 'reorder', [
            'items' => [
                $blocks->get(2)->id,
                $blocks->get(0)->id,
                $blocks->get(1)->id,
            ],
        ])
        ->callMountedFormComponentAction()
        ->assertFormSet([
            'website_content' => [
                [
                    'id' => $blocks->get(2)->id,
                ],
                [
                    'id' => $blocks->get(0)->id,
                ],
                [
                    'id' => $blocks->get(1)->id,
                ],
            ],
        ]);
});

class TestComponentWithPageBuilderRenderedUsingViews extends FormComponent
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
