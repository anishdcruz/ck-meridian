<?php

namespace App\Tenant\Contact;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Category extends Model
{
	use Filterable;

	protected $connection = 'team';

    protected $table = 'contact_categories';

    protected $fillable = ['name'];
}
