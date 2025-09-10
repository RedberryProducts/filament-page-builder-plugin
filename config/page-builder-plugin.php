<?php

return [
    'block_model_class' => Redberry\PageBuilderPlugin\Models\PageBuilderBlock::class,

    'polymorphic_relationship_name' => 'page_builder_blockable',

    'global_blocks' => [
        'enabled' => true,
        'resource' => Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource::class,
    ],
];
