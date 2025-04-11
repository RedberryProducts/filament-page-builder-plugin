<?php

namespace Redberry\PageBuilderPlugin\Tests\Fixtures;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\HasFormComponentActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\BasePage;
use Filament\Pages\Concerns\InteractsWithFormActions;

class FormComponent extends BasePage implements HasActions, HasForms
{
    use HasFormComponentActions;
    use InteractsWithActions;
    use InteractsWithFormActions;
    use InteractsWithForms;

    public $data;

    public static function make(): static
    {
        return new static;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function data($data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }
}
