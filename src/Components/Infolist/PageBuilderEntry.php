<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Infolist;


use Filament\Infolists\Components\Entry;
use RedberryProducts\PageBuilderPlugin\Traits\ComponentLoadsPageBuilderBlocks;

class PageBuilderEntry extends Entry
{
    use ComponentLoadsPageBuilderBlocks;

    public string $view = 'page-builder-plugin::infolist.page-builder-entry';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpanFull();

        $this->relationship('pageBuilderBlocks');
    }

    public function relationship(
        string $relationship,
    ) {
        $this->relationship = $relationship;

        $this->getStateUsing(function () {
            return $this->getConstrainAppliedQuery($this->getRecord())->get()->toArray();
        });
    }
}
