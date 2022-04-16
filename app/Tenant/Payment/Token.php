<?php

namespace App\Tenant\Payment;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'tokens';

    protected $connection = 'team';

    protected $fillable = ['token', 'email'];
}
