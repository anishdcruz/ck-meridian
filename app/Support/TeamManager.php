<?php

namespace App\Support;

use App\Team;

class TeamManager {

	protected $team;

	public function setTeam($team)
	{
		$this->team = $team;
		config('app.timezone', $team->timezone);
		return $this;
	}

	public function getTeam()
	{
		return $this->team;
	}

	public function findByUUID($uuid)
	{
		$this->team = Team::where('uuid', $uuid)
			->first();

		return $this->team;
	}
}