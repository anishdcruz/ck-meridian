<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\CategoryController;
use App\Tenant\Expense\Category;

class ExpenseCategoryController extends CategoryController
{
    protected $model = Category::class;

    protected $parent = 'expenses';

    protected $title = 'expenses';

    protected $table = 'expense_categories';
}
