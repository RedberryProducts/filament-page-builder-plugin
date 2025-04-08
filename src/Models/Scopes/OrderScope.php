<?php

namespace Redberry\PageBuilderPlugin\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrderScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->orderBy('order')->orderBy('created_at', 'desc');
    }
}
