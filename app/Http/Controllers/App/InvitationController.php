<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Invitation;
use App\User;
use Illuminate\Support\Str;
use Mail;
use App\Mail\InvitationEmail;
use App\Repository\UserRepository;
use Auth;
use DB;

class InvitationController extends Controller
{
	// check user limit on team

    public function index()
    {
    	$this->authorize('settings', 'invitations');
    	return [
    		'collection' => Invitation::with('role')->inTeam()->filter()
    	];
    }

    public function create()
    {
    	$this->authorize('settings', 'invitations');
        $item = [
            'email' => '',
            'role_id' => null,
            'role' => null
        ];

        return [
            'form' => $item
        ];
    }

    public function store(Request $request)
    {
    	abort_in_demo();
    	$this->authorize('settings', 'invitations');


    	$team = get_team();

    	$id = $team->id;
    	if(!$this->checkUserLimit($team)) {
	        return response()->json([
			        	'message' => __('app.team_limit'),
				        'errors' => []
				    ], 422);
    	}

    	$request->validate([
            'email' => 'required|email|unique:invitations,email,'.$id.',team_id',
            'role_id' => 'required|exists:roles,id,team_id,'.$id
        ]);

        if ($team->users()->where('email', $request->email)->exists()) {
            return response()->json([
            	'message' => 'That user is already in the team.',
            	'errors' => ['email' => ['That user is already in the team.']]
            ], 422);
        }

    	$user = User::where('email', $request->email)->first();

        $invitation = new Invitation;
        $invitation->email = $request->email;
        $invitation->team_id = $id;
        $invitation->user_id = $user ? $user->id : null;
        $invitation->token =  (string) Str::uuid();
        $invitation->role_id = $request->role_id;
        $invitation->save();

        Mail::send(new InvitationEmail($invitation, $user));

        return [
        	'saved' => true,
        	'id' => $invitation->id
        ];
    }

    public function show($id)
    {
    	abort_in_demo();
    	$this->authorize('settings', 'invitations');
        $item = Invitation::with('role')->inTeam()->findOrFail($id);

        return [
            'model' => $item
        ];
    }

    public function destroy($id)
    {
    	abort_in_demo();
    	$this->authorize('settings', 'invitations');
        $model = Invitation::with('role')->inTeam()->findOrFail($id);

        $model->delete();

        return [
            'deleted' => true
        ];
    }

    public function registerGet($token)
    {
    	abort_in_demo();
    	$invitation = Invitation::where('token', $token)->firstOrFail();

    	$team = $invitation->team;

    	if(!$this->checkUserLimit($team)) {
	        return abort(404, __('app.team_limit'));
    	}

    	return view('app.invitations.register', [
    		'invitation' => $invitation
    	]);
    }

    public function registerPost(Request $request, $token, UserRepository $userRepository)
    {
    	abort_in_demo();
    	$invitation = Invitation::where('token', $token)->firstOrFail();

    	$team = $invitation->team;

    	if(!$this->checkUserLimit($team)) {
	        return abort(404, __('app.team_limit'));
    	}

    	$rules = [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ];

        if(inSAASMode()) {
        	$rules['terms'] = ['required', 'accepted'];
        }

        $request->validate($rules);

        $user = $userRepository->joinTeam($invitation, $request->only('name', 'password'));

        $invitation->delete();

        // set redirect
        $redirectTo = route('app.index');

        Auth::loginUsingId($user->id);

        return redirect($redirectTo);
    }

    public function joinTeam($token)
    {
    	abort_in_demo();
        $invitation = Invitation::where('token', $token)
            ->whereNotNull('user_id')->firstOrFail();

        $team = $invitation->team;

    	if(!$this->checkUserLimit($team)) {
	        return abort(404, __('app.team_limit'));
    	}

        $user = $invitation->user;


        $invitation->team->users()->attach($user, [
            'type' => 'member',
            'role_id' => $invitation->role_id
        ]);

        $user->current_team_id = $invitation->team_id;
        $user->save();

        // set redirect
        $redirectTo = route('app.index');

        return redirect($redirectTo);
    }

    public function checkUserLimit($team)
    {
        $owner = $team->owner;
        // find current members
        $teamCount = $team->members->count();

        if($sub = $owner->currentSubscription()) {
        	if(intval($sub['plan']->findLimits['users_per_team']) <= $teamCount) {
		    	return false;
        	}
        } else {
	    	return false;
        }

        return true;
    }
}
