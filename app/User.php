<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use App\Support\Filterable;
use App\Plan;
use App\Subscription;

class User extends Authenticatable
{
	protected $connection = 'mysql';

    // use Notifiable;
    use Billable;
    use Filterable;

    protected $fillable = [
        'name', 'email', 'password',
        'extra_billing_info'
    ];

    protected $appends = ['role'];

    protected $hidden = [
        'current_team_id',
        'trial_ends_at',
        'card_brand',
        'card_last_four',
        'extra_billing_info',
        'password',
        'remember_token',
        'stripe_id',
        'stripe_subscription',
        'email_verified_at'
    ];

    protected $nonSortable = [
        'password',
        'remember_token',
        'stripe_subscription'
    ];
    protected $nonFilterable = [
        'password',
        'remember_token'
    ];
    protected $nonSearchable = [
        'card_brand',
        'card_last_four',
        'extra_billing_info',
        'password',
        'remember_token',
        'stripe_id',
        'stripe_subscription',
        'email_verified_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function teams()
    {
        return $this->belongsToMany(
            Team::class, 'user_teams',
            'user_id', 'team_id'
        )->using(UserTeam::class)
        ->withPivot('type', 'role_id')
        ->with('currency')
        ->orderBy('name', 'asc');

        // return $this->belongsToMany(
        //     Team::class, 'user_teams', 'user_id', 'team_id'
        // )->withPivot(['type'])
        //     ->orderBy('name', 'asc');
    }

    public function getRoleAttribute()
    {
        return optional($this->pivot)->role;
    }

    public function hasTeams()
    {
        return count($this->teams) > 0;
    }

    public function ownedTeamsWithMembers()
    {
        return $this->teams()->with(['users'])
            ->where("owner_id", $this->getKey());
    }

    public function ownedTeams()
    {
        return $this->teams()
            ->where("owner_id", $this->getKey());
    }

    public function currentTeam()
    {
        if (is_null($this->current_team_id) && $this->hasTeams()) {
            $this->switchToTeam($this->teams->first());

            return $this->currentTeam();
        } elseif (! is_null($this->current_team_id)) {
            $currentTeam = $this->teams->find($this->current_team_id);
            return $currentTeam ?: $this->refreshCurrentTeam();
        }
    }

    public function switchToTeam($team)
    {
        $this->current_team_id = $team->id;

        $this->save();
    }

    public function refreshCurrentTeam()
    {
        $this->current_team_id = null;

        $this->save();

        return $this->currentTeam();
    }

    public function fsubscription()
    {
    	return $this->hasOne(Subscription::class);
    }

    public function currentSubscription()
    {
        $subscription = [
            'plan_id' => null,
            'ends_at' => null,
            'isSubscribed' => $this->subscribed('main'),
            'hasCardOnFile' => $this->hasCardOnFile(),
            'onTrial' => false,
            'isCancelled' => false,
            'onGracePeriod' => false,
            'hasTeams' => $this->hasTeams()
        ];

        $current = $this->subscription('main');

        if($current) {
        	$plan = Plan::where('gateway_id', $current->stripe_plan)
        		->first(['name', 'gateway_id', 'time_period', 'limits']);

        	$subscription['plan'] = $plan;
            $subscription['plan_id'] = $current->stripe_plan;
            $subscription['ends_at'] = $current->ends_at
                ? $current->ends_at->format('d-M-Y')
                : null;

            $subscription['onTrial'] = $current->onTrial();
            $subscription['isCancelled'] = $current->cancelled();
            $subscription['onGracePeriod'] = $current->onGracePeriod();
        }

        return $subscription;
    }
}
