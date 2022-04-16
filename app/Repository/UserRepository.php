<?php

namespace App\Repository;

use App\User;
use App\Team;
use DB;
use File;
use App\Tenant\UserMetric;
use App\Tenant\Filter;
use App\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use TeamManager;

class UserRepository {

	public function createUserFromRegistration($request, $withSubscription = false, $plan = null)
	{
		return DB::transaction(function() use ($request, $withSubscription, $plan) {
			$user = $this->createNewUser($request);

			if($withSubscription) {
				// charge
				$this->createSubscriptionOnStripe($request, $user, $plan);
			}

			return $user;
		});
	}

	public function joinTeam($invitation, $data)
	{
		$user = $this->createNewUser(array_merge($data, ['email' => $invitation->email]));

		$team = $invitation->team;
		$team->users()->attach(
			$user, [
				'type' => 'member',
				'role_id' => $invitation->role_id
			]
		);

		$user->current_team_id = $invitation->team_id;
		$user->save();

		$this->setupDefaultDashboard($team, $user, $invitation->role_id);

		return $user;
	}

	public function setupDefaultDashboard($team, $user, $roleId)
	{
		// add default dashboard
		$file = File::get(base_path('database/seeds/default_metrics.json'));
		$json = json_decode($file, 1);

		// get role
		$role = Role::where('team_id', $team->id)
			->where('id', $roleId)
			->first();
		DB::purge();
		$modules = $role->permissions['modules'];
		TeamManager::setTeam($team);

		if($role) {
			foreach($json as $key => $values) {
				$k = Str::slug($key, '-');
				$has = Arr::get($modules, $k.'.view');
				if($has) {
					foreach($values as $d) {
							// check if filter exist

							$found = Filter::where('name', $d['filter']['name'])
								->first();

							if($found) {
								$re = new UserMetric;
								$re->fill($d);
								$re->filter_id = $found->id;
								$re->user_id = $user->id;
								$re->save();
							}
					}
				}

			}
		}
	}

	public function createNewUser($request)
	{
		return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password'])
        ]);
	}

	public function createSubscriptionOnStripe($request, $user, $plan)
	{
		$subscription = $user->newSubscription('main', $plan->gateway_id)
			->create($request['stripeToken']);
	}
}