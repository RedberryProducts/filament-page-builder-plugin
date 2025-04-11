<?php

namespace Redberry\PageBuilderPlugin\Models;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;
use Redberry\PageBuilderPlugin\Models\Scopes\OrderScope;

/**
 * @property string $id
 * @property class-string<BaseBlock> $block_type
 * @property string $page_builder_blockable_type
 * @property string $page_builder_blockable_id
 * @property array $data
 */
#[ScopedBy(OrderScope::class)]
class PageBuilderBlock extends Model
{
    use HasFactory;
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
