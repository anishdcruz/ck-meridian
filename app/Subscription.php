<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Support\Filterable;

class Subscription extends Model
{
	use Filterable;

    protected $table = 'subscriptions';

    public function user()
    {
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
