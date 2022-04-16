<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Invitation extends Model
{
	use Filterable;

	protected $table = 'invitations';

	protected $nonSortable = ['token', 'team_id'];
	protected $nonFilterable = ['token', 'team_id', 'user_id'];

	public function role()
	{
		return $this->belongsTo(Role::class);
	}

	public function team()
	{
		return $this->belongsTo(Team::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function scopeInTeam($query)
    {
    	return $query->where('team_id', auth()->user()->current_team_id);
    }
}
