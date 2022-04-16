<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\CategoryController;
use App\Tenant\Item\Category;

class ItemCategoryController extends CategoryController
{
    protected $model = Category::class;

	protected $parent = 'items';

	protected $title = 'items';

	protected $table = 'item_categories';
}
