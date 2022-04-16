<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;

class MemberController extends Controller
{
    public function index()
    {
    	$this->authorize('settings', 'members');
    	$team = Auth::user()->currentTeam();

    	// $users = User::whereHas('teams', function($q) {
    	// 	$q->where('team_id', 1);
    	// })->get();


    	return [
    		'collection' => $team->users()->filter()
    		// 'collection' => $team->users()->withPivot('type')->filter()
    	];
    }


    public function show($id)
    {
    	$this->authorize('settings', 'members');
    	$team = Auth::user()->currentTeam();
        $item = $team->users()->findOrFail($id);
        return [
        	'model' => $item
        ];
    }

    public function destroy($id)
    {
    	abort_in_demo();
    	$this->authorize('settings', 'members');
    	$user = Auth::user();
    	$team = $user->currentTeam();
        $member = $team->users()->findOrFail($id);

		// cannot remove owner
		// cannot remove self
        if($team->owner_id == $id || $user->id == $id) {
            return delete_first(__('lang.cannot_delete'));
        }

        $member->teams()->detach($team->id);
       	// $team->users()->detach($member->id);
       	$member->refreshCurrentTeam();

        return [
            'deleted' => true
        ];
    }
}
