<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Team;
use App\Jobs\DeleteTeam;

class TeamController extends AdminController
{
    protected $model = Team::class;

    protected $title = 'teams';

    protected $withIndex = ['owner'];

    protected $withShow = ['owner', 'members'];

    public function store(Request $request)
    {

    }

    public function update($id, Request $request)
    {

    }

    public function destroy($id)
    {
    	abort_in_demo();
    	$team = Team::findOrFail($id);
		dispatch(new DeleteTeam($team));

		return response()
			->json(['deleted' => true]);
    }
}
