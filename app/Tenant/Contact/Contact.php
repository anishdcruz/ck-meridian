<?php

namespace App\Tenant\Contact;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Contact extends Model
{
	use Filterable;

	protected $connection = 'team';

	protected $table = 'contacts';

	protected $fillable = [
		'organization', 'name', 'category_id',
		'email', 'title', 'department',
		'mobile', 'phone', 'fax', 'website',
		'primary_address', 'other_address',
		'tax_id'
	];

	public function category()
	{
		return $this->belongsTo(Category::class, 'category_id', 'id');
	}
}
