<?php

namespace Redberry\PageBuilderPlugin\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PageBuilderBlock extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'data' => 'array',
    ];

    public function pageBuilderBlockable(): MorphTo
    {
        return $this->morphTo();
    }
}
