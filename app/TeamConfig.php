<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeamConfig extends Model
{
	protected $primaryKey = null;

    public $incrementing = false;

    protected $table = 'team_configs';

    protected $connection = 'team';

    protected $fillable = ['key', 'value'];
}
