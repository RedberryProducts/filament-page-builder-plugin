<?php

namespace Redberry\PageBuilderPlugin\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Redberry\PageBuilderPlugin\Traits\HasPageBuilder;

class Page extends Model
{
    use HasFactory;
    use HasPageBuilder;

    protected $guarded = [];
}
