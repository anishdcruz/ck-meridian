<?php

namespace App\Tenant\Expense;
use App\Support\Filterable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Filterable;

	protected $connection = 'team';

    protected $table = 'expense_categories';

    protected $fillable = ['name'];
}
