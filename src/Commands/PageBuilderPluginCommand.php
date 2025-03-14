<?php

namespace RedberryProducts\PageBuilderPlugin\Commands;

use Illuminate\Console\Command;

class PageBuilderPluginCommand extends Command
{
    // TODO: Implement scaffolding block class
    public $signature = 'page-builder-plugin';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
