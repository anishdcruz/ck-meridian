<?php

namespace App\Tenant\Expense;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Tenant\Vendor\Vendor;

class Expense extends Model
{
    use Filterable;

	protected $connection = 'team';

	protected $table = 'expenses';

	protected $fillable = [
		'vendor_id',
		'category_id',
		'payment_date',
		'method',
		'sub_total',
		'reference',
		'tax',
		'tax_total',
		'tax_2',
		'tax_2_total',
		'note'
	];

	public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

	public function category()
	{
		return $this->belongsTo(Category::class, 'category_id', 'id');
	}
}
