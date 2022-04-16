<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\UserRepository;
use App\Repository\TeamRepository;
use Meridian;
use App\Plan;
use Auth;
use DB;

class NoTeamController extends Controller
{
    public function show()
    {
    	if(!inSAASMode()) {
    		return 'No access to team';
    	}
    	$user = auth()->user();
    	return view('app.no_team.show', [
    		'user' => $user
    	]);
    }

    public function subscription(Request $request, UserRepository $userRepository, TeamRepository $team)
    {
    	abort_in_demo();
    	if(!inSAASMode()) {
    		return 'No access to team';
    	}
    	$rules = [
            'company_name' => ['required', 'string', 'max:255'],
            'terms' => ['required', 'accepted'],
            'plan' => ['required', 'integer', 'exists:plans,id,active,1']
        ];

        if(Meridian::isCardUpFrontRequired()) {
            $rules['stripeToken'] = ['required'];
        }

        $request->validate($rules);

        $user = Auth::user();

        $plan = Plan::findOrFail($request->plan);

        $userRepository->createSubscriptionOnStripe(
            $request->only('plan', 'stripeToken'),
            $user,
            $plan
        );

        // create team
        $team = $team->create($user, [
            'name' => request('company_name')
        ]);

        // set redirect
        return redirect()->route('app.index');
    }

    public function teamCreate(Request $request, TeamRepository $team)
    {
    	abort_in_demo();
    	if(!inSAASMode()) {
    		return 'No access to team';
    	}
    	$rules = [
            'company_name' => ['required', 'string', 'max:255']
        ];

        $request->validate($rules);

        $user = Auth::user();

        if($sub = $user->currentSubscription()) {
        	$teamCount = DB::table('teams')
        		->where('owner_id', Auth::id())
        		->count();

        	if(intval($sub['plan']->findLimits['teams']) <= $teamCount) {
		    	return abort(404, __('app.team_limit'));
        	}
        } else {
		    return abort(404, __('app.need_subscription'));
        }

        // create team
        $team = $team->create($user, [
            'name' => request('company_name')
        ]);

        // set redirect
        return redirect()->route('app.index');
    }
}
