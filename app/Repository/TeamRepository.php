<?php

namespace App\Repository;

use DB;
use App\Team;
use App\Role;
use Illuminate\Support\Str;
use TeamManager;
use App\Facades\TeamConfig;
use File;
use App\Tenant\Filter;
use App\Tenant\UserMetric;
use App\Tenant\Term;

class TeamRepository {

	public function create($user, $request)
	{
		$team = new Team;

		$team->owner_id = $user->id;
		$team->name = $request['name'];
		$team->uuid = $this->getNewUUID();
		$team->database = $this->getNewDatabaseName();
		$team->currency_id = 'USD';
		$team->date_format = 'd-m-Y';
		$team->lang_id = 'en';
		$team->timezone = 'UTC';
		$team->logo_file = null;

		$team->save();

		// default admin role
		$role = $this->createDefaultRole($team);

		$team->users()->attach(
			$user, [
				'type' => 'owner',
				'role_id' => $role->id
			]
		);

		$this->setupTeam($team);

		return $team;
	}

	public function createDefaultRole($team)
	{
		return Role::create([
			'name' => 'Admin',
			'team_id' => $team->id,
			'description' => 'Full permission',
			'permissions' => json_encode(config('permission'))
		]);
	}


	public function getNewDatabaseName()
	{
		$name = null;
		do {
			$name = str_random(32);
		} while(Team::where('database', $name)->first());

		return 'mdb_'.$name;
	}

	public function getNewUUID()
	{
		$name = null;
		do {
			$name = str_random(16);
		} while(Team::where('uuid', $name)->first());

		return 'me_'.$name;
	}

	protected function setupTeam($team)
	{
		// create new database
		$create = DB::statement('create database ' . $team->database);

		TeamManager::setTeam($team);

		// change current user to team
		if($create) {
			$this->migrate();
			$this->loadConfigs($team);

			DB::purge('team');

			$team->initialized_at = now();
			$team->save();
		}
	}

	protected function loadConfigs($team)
	{
		TeamConfig::setMany([
			'company_name' => $team->name,
			'payment_notification_email' => $team->owner->email
		]);

		// default terms
		$file = File::get(base_path('database/seeds/default_terms.json'));
		$json = json_decode($file, 1);

		foreach ($json as $saved) {
			Term::create($saved);
		}


		// default filters
		$file = File::get(base_path('database/seeds/default_filters.json'));
		$json = json_decode($file, 1);

		foreach ($json as $saved) {
			Filter::create([
				'user_id' => $team->owner_id,
				'name' => $saved['name'],
				'shared_with' => $saved['shared_with'],
				'params' => json_encode($saved['params']),
				'resource' => $saved['resource'],
				'filter_match' => $saved['filter_match']
			]);
		}

		// setup default dashboard
		$file = File::get(base_path('database/seeds/default_metrics.json'));
		$json = json_decode($file, 1);

		foreach($json as $key => $values) {
			foreach($values as $data) {
					// check if filter exist
					$found = Filter::where('name', $data['filter']['name'])
						->first();
					if($found) {
						$re = new UserMetric;
						$re->fill($data);
						$re->filter_id = $found->id;
						$re->user_id = $team->owner_id;
						$re->save();
					}
			}
		}
	}

	protected function migrate()
	{
		$migrator = app('migrator');
		$migrator->setConnection('team');
		if(!$migrator->repositoryExists()) {
		    $migrator->getRepository()->createRepository();
		}

		$migrator->run(database_path('migrations/team'), []);
	}
}