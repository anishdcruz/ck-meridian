<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\CategoryController;
use App\Tenant\Vendor\Category;

class VendorCategoryController extends CategoryController
{
    protected $model = Category::class;

	protected $parent = 'vendors';

	protected $title = 'vendors';

	protected $table = 'vendor_categories';
}
