<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Currency;
use App\Support\Filterable;

class Team extends Model
{
	use Filterable;

    protected $fillable = [
        'name', 'timezone', 'date_format', 'currency_id', 'lang_id'
    ];

	protected $hidden = [
		'database', 'uuid',
		'initialized_at',
        'stripe_connected_at',
        'livemode',
        'stripe_user_id'
	];

	public function members()
	{
		return $this->belongsToMany(
            User::class, 'user_teams', 'team_id', 'user_id'
        )->using(UserTeam::class)->orderBy('name', 'asc');
	}

    public function users()
    {
        return $this->belongsToMany(
            User::class, 'user_teams', 'team_id', 'user_id'
        )->using(UserTeam::class)
        ->withPivot('type', 'role_id')
        ->orderBy('name', 'asc');
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class, 'user_teams', 'team_id', 'role_id'
        )->using(UserTeam::class);
    }

    public function role()
    {
        return $this->roles()->find($this->pivot->role_id);
    }

    public function owner()
    {
    	return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'code');
    }

    public function hasPaymentGateway()
    {
        return !is_null($this->attributes['stripe_connected_at']);
    }
}
