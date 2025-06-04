<?php

namespace Redberry\PageBuilderPlugin\Traits;

trait PreviewRendersWithBlade
{
    public function getViewForBlock(string $class)
    {
        $view = $class::getView();

        if ($view) {
            return $view;
        }

        throw new \Exception('View not found for block ' . $class . ' if you want to use view method of rendering you need to declare view for a block.');
    }
}
