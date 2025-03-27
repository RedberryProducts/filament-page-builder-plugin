<?php

namespace RedberryProducts\PageBuilderPlugin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PageBuilderBlock extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'data' => 'array',
    ];

    public function pageBuilderBlockable(): MorphTo
    {
        return $this->morphTo();
    }
}
