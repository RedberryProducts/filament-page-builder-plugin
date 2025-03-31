<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Infolist;

use Closure;
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

        $this->relationship();
    }

    public function relationship(
        string $relationship = 'pageBuilderBlocks',
        ?Closure $modifyRelationshipQueryUsing = null,
    ): self {
        $this->relationship = $relationship;
        $this->modifyRelationshipQueryUsing = $modifyRelationshipQueryUsing;

        $this->getStateUsing(function () {
            return $this->getConstrainAppliedQuery($this->getRecord())->get()->toArray();
        });

        return $this;
    }
}
