<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Role extends Model
{
	use Filterable;

	protected $table = 'roles';

	protected $fillable = [
        'name', 'description', 'permissions',
        'team_id'
    ];

    protected $nonSortable = ['permissions'];
    protected $nonFilterable = ['permissions', 'team_id'];
    protected $nonSearchable = ['permissions', 'team_id'];

    public function getPermissionsAttribute($value)
    {
    	return json_decode($value, true);
    }

    public function scopeTeam($query)
    {
    	return $query->where('team_id', auth()->user()->current_team_id);
    }
}
