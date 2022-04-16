<?php

namespace App\Support;
use Cache;
use DB;

class TeamConfig {

	protected $team;

	protected $minutes = 1440; // 24hrs

	public function __construct($team)
	{
		$this->team = $team;
	}

    protected function getCacheKey($key)
    {
        return 'm.'.$this->team->id.'.'.$key;
    }

    function get($key, $default = null)
	{
		$found = Cache::remember($this->getCacheKey($key), $this->minutes, function() use ($key) {
		    return DB::connection('team')
        		->table('team_configs')->where('key', $key)->first();
		});

		if($found) {
			return $found->value;
		}

		return $default;
	}

	function getMany($keys)
	{
		$collection = [];
		foreach($keys as $key) {
			$collection[$key] = $this->get($key);
		}

		return $collection;
	}

	public function set($key, $value)
    {

        Cache::forget($this->getCacheKey($key));
        $data = DB::connection('team')
        	->table('team_configs')
        	->where('key', $key)
        	->update(['value' => $value]);
    }

    function setMany($data)
	{
		foreach($data as $key => $value) {
			$this->set($key, $value);
		}
	}
}