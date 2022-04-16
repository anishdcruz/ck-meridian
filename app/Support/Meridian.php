<?php

namespace App\Support;

use App\Plan;

class Meridian {

	private $cardUpFront = true;

	public function __construct()
	{
	}

	public function plans()
	{
		return Plan::where('active', 1)
			->orderBy('price', 'asc')
			->get();
	}

	public function noCardUpFront()
	{
		return $this->cardUpFront = false;
	}

	public function isCardUpFrontRequired()
	{
		return $this->cardUpFront;
	}
}