<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\CategoryController;
use App\Tenant\Contact\Category;

class ContactCategoryController extends CategoryController
{
	protected $model = Category::class;

	protected $parent = 'contacts';

	protected $title = 'contacts';

	protected $table = 'contact_categories';
}
