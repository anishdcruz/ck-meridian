<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserTeam extends Pivot
{
	public function role()
	{
		return $this->belongsTo(Role::class, 'role_id', 'id');
	}
}