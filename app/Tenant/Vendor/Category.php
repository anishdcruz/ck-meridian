<?php

namespace App\Tenant\Vendor;
use App\Support\Filterable;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Filterable;

	protected $connection = 'team';

    protected $table = 'vendor_categories';

    protected $fillable = ['name'];
}
