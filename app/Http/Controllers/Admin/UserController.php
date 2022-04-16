<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\User;
use App\Team;
use DB;
use App\Jobs\DeleteTeam;

class UserController extends AdminController
{
    protected $model = User::class;

    protected $title = 'users';

    protected $withShow = ['subscriptions'];

    public function store(Request $request)
    {

    }

    public function update($id, Request $request)
    {

    }

    public function destroy($id)
    {
    	abort_in_demo();
    	$user = User::findOrFail($id);

    	if(inSAASMode()) {
    		// unsubscripe all
    		if($user->subscribed('main')) {
    			$user->subscription('main')
    				->cancel();
    		}
    	}

    	// delete all owned teams
    	$teams = Team::where('owner_id', $user->id)
    		->get();

		foreach ($teams as $team) {
			dispatch(new DeleteTeam($team));
		}

		// detach from other teams

		DB::table('user_teams')->where('user_id', $user->id)->delete();

    	// delete subscription
    	DB::table('subscriptions')->where('user_id', $user->id)->delete();

    	// delete user
    	$user->delete();

    	return response()
			->json(['deleted' => true]);
    }
}
