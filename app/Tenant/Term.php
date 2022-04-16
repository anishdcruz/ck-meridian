<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Term extends Model
{
    use Filterable;

    protected $connection = 'team';

    protected $table = 'terms';

    protected $fillable = ['label', 'description'];
}
