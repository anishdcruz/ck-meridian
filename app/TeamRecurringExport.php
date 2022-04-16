<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Team;

class TeamRecurringExport extends Model
{
    protected $fillable = [
        'frequency',
        // 'send_on',
        // 'send_at',
        // 'start_date',
        // 'end_date',
    ];

    public function team()
    {
    	return $this->belongsTo(Team::class);
    }
}
