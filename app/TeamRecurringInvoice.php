<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Team;

class TeamRecurringInvoice extends Model
{
    protected $fillable = [
        'frequency',
        // 'send_on',
        // 'send_at',
        // 'start_date',
        // 'end_date',
        'never_expiry'
    ];

    public function team()
    {
    	return $this->belongsTo(Team::class);
    }
}
