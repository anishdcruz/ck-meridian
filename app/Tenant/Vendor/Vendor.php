<?php

namespace App\Tenant\Vendor;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Currency;

class Vendor extends Model
{
    use Filterable;

	protected $connection = 'team';

	protected $table = 'vendors';

	protected $fillable = [
		'organization', 'name', 'category_id',
		'email', 'title', 'department',
		'mobile', 'phone', 'fax', 'website',
		'primary_address', 'other_address',
		'currency_id'
	];

	public function category()
	{
		return $this->belongsTo(Category::class, 'category_id', 'id');
	}

	public function currency()
	{
		return $this->belongsTo(Currency::class, 'currency_id', 'id');
	}

	public function addPayment($amount)
	{
		$this->total_paid += $amount;
		$this->save();
	}

	public function removePayment($amount)
	{
		$this->total_paid -= $amount;
		$this->save();
	}
}
