<?php

use Filament\Forms\Form;
use Illuminate\View\View;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilder;
use Redberry\PageBuilderPlugin\Components\Forms\PageBuilderPreview;
use Redberry\PageBuilderPlugin\Models\PageBuilderBlock;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Blocks\ViewBlock;
use Redberry\PageBuilderPlugin\Tests\Fixtures\FormComponent;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Models\Page;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->page = Page::factory()->create();
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
        ->assertFormSet([
            'website_content' => [
                [
                    'block_type' => ViewBlock::class,
                    'data' => [
                        'image' => 'https://example.com/image.jpg',
                        'hero_button' => [
                            'text' => 'hero button text',
                        ],
                    ],
                ],
            ],
        ])
        ->assertSeeHtml('hero button text')
        ->mountFormComponentAction('website_content', 'edit', [
            'index' => 0,
            'item' => $blocks->get(0)->id,
        ])
        ->assertFormComponentActionMounted('website_content', ['edit'])
        ->setFormComponentActionData([
            'data' => [
                'hero_button' => [
                    'text' => 'Test 123',
                    'url' => 'https://example.com',
                ],
            ],
        ])
        ->callMountedFormComponentAction()
        ->assertHasNoFormComponentActionErrors()
        ->assertDontSeeHtml('hero button text')
        ->assertSeeHtml('Test 123');
});

class TestComponentWithPageBuilderAndPreview extends FormComponent
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
