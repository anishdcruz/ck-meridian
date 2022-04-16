<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\UserRepository;
use App\Repository\TeamRepository;
use Auth;
use Hash;
use Meridian;
use App\Plan;
use Illuminate\View\Expression as ViewExpression;
use DB;
use App\Team;
use App\Jobs\DeleteTeam;

class MyAccountController extends Controller
{
    public function generalGet()
    {
    	$user = Auth::user();

    	return [
			'form' => [
				// 'profile_photo' => $user->profile_photo,
				'name' => $user->name,
                'email' => $user->email,
				'extra_billing_info' => $user->extra_billing_info
			]
		];
    }

    public function generalPost(Request $request)
    {
    	abort_in_demo();
    	$request->validate([
    		'name' => 'required|string',
    		'extra_billing_info' => 'nullable|max:2000'
    	]);

    	$user = Auth::user();

    	$user->fill(
    		$request->only('name', 'extra_billing_info')
    	);

    	$user->save();

    	return [
            'saved' => true
        ];
    }

    public function securityGet()
    {
    	$user = Auth::user();

    	return [
			'form' => [
				'current_password' => null,
				'new_password' => null,
				'new_password_confirmation' => null
			]
		];
    }

    public function securityPost(Request $request)
    {
    	abort_in_demo();
    	$request->validate([
    		'current_password' => 'required|min:6',
    		'new_password' => 'required|confirmed|min:6'
    	]);

    	$user = Auth::user();

    	// match current password
    	if (!Hash::check($request->current_password, $user->password)) {
    		return response()->json([
    		    'message' => 'The given data was invalid!',
    		    'errors' => [
    		        'current_password' => [__('lang.invalid_current_password')]
    		    ]
    		], 422);
    	}

    	$user->password = bcrypt($request->new_password);

    	$user->save();

        // email change

    	return [
            'saved' => true
        ];
    }

    public function subscriptionGet()
    {
        $user = Auth::user();

        $subscription = [
            'plan_id' => null,
            'ends_at' => null,
            'isSubscribed' => $user->subscribed('main'),
            'hasCardOnFile' => $user->hasCardOnFile(),
            'onTrial' => false,
            'isCancelled' => false,
            'onGracePeriod' => false
        ];

        $current = $user->subscription('main');

        if($current) {
            $subscription['plan_id'] = $current->stripe_plan;
            $subscription['ends_at'] = $current->ends_at
                ? $current->ends_at->format('d-M-Y')
                : null;

            $subscription['onTrial'] = $current->onTrial();
            $subscription['isCancelled'] = $current->cancelled();
            $subscription['onGracePeriod'] = $current->onGracePeriod();
        }

        $plans = Meridian::plans();

        return [
            'form' => [
                'plans' => $plans,
                'subscription' => $subscription,
                'api_key' => config('services.stripe.key')
            ]
        ];
    }

    public function subscriptionCreate(Request $request, UserRepository $userRepository)
    {
    	abort_in_demo();
        $request->validate([
            'plan' => ['required', 'integer', 'exists:plans,id,active,1'],
            'stripeToken' => 'required',
        ]);

        $user = Auth::user();

        $plan = Plan::findOrFail($request->plan);

        $user = $userRepository->createSubscriptionOnStripe(
            $request->only('plan', 'stripeToken'),
            $user,
            $plan
        );

        // sent email

        return [
            'saved' => true
        ];
    }

    public function subscriptionUpdate(Request $request)
    {
    	abort_in_demo();
        $request->validate([
            'plan' => ['required', 'integer', 'exists:plans,id,active,1']
        ]);

        $newPlan = Plan::findOrFail($request->plan);

        // check to see if data is under limit
        if ($newPlan->price === 0) {
            Auth::user()->subscription('main')->cancel();
        } else {
            Auth::user()->subscription('main')->swap($newPlan->gateway_id);
        }

        // email change

        return [
            'saved' => true
        ];
    }

    public function subscriptionCancel()
    {
    	abort_in_demo();
        Auth::user()->subscription('main')->cancel();

        // cancel email

        return [
            'saved' => true
        ];
    }

    public function subscriptionRestore()
    {
    	abort_in_demo();
        Auth::user()->subscription('main')->resume();

        // restore email

        return [
            'saved' => true
        ];
    }

    public function paymentMethodsGet()
    {
        $user = Auth::user();
        $card = [
            'card_brand' => $user->card_brand,
            'card_last_four' => $user->card_last_four,
            'hasCardOnFile' => $user->hasCardOnFile()
        ];

        $subscription = [
            'isSubscribed' => $user->subscribed('main'),
            'onTrial' => optional($user->subscription('main'))->onTrial(),
            'isCancelled' => optional($user->subscription('main'))->cancelled(),
            'onGracePeriod' => optional($user->subscription('main'))->onGracePeriod()
        ];

        return [
            'form' => [
                'card' => $card,
                'subscription' => $subscription,
                'api_key' => config('services.stripe.key')
            ]
        ];
    }

    public function paymentMethodsPost(Request $request)
    {
    	abort_in_demo();
        $request->validate([
            'stripe_token' => 'required',
        ]);

        Auth::user()->updateCard($request->stripe_token);

        return [
            'saved' => true
        ];
    }

    public function teamsGet()
    {
        $user = Auth::user();

        return [
            'form' => [
                'teams' => $user->ownedTeamsWithMembers,
                'subscription' => $user->currentSubscription()
            ]
        ];
    }

    public function teamsPost(Request $request, TeamRepository $teamRepository)
    {
    	abort_in_demo();
        $request->validate([
            'team_name' => ['required', 'string', 'max:255'],
        ]);

        // validate subscription here
        $user = Auth::user();

        if($sub = $user->currentSubscription()) {
        	$teamCount = DB::table('teams')
        		->where('owner_id', Auth::id())
        		->count();

        	if(intval($sub['plan']->findLimits['teams']) <= $teamCount) {
		    	return response()->json([
		        	'message' => __('app.team_limit'),
			        'errors' => []
			    ], 422);
        	}
        } else {
	    	return response()->json([
	        	'message' => __('app.need_subscription'),
		        'errors' => []
		    ], 422);
        }

        $team = $teamRepository->create($user, [
            'name' => $request->team_name
        ]);

        return [
            'saved' => true,
            'team' => $team
        ];
    }

    public function invoicesGet()
    {
    	abort_in_demo();
        $user = Auth::user();

        $invoices = [];

        if($user->subscribed('main')) {
            $invoices = $user->invoices()->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'date' => $invoice->date()->toFormattedDateString(),
                    'total' => $invoice->total(),
                    'download' => route('app.my_account.invoices.download', ['id' => $invoice->id]),
                ];
            });
        }


        return [
            'form' => [
                'invoices' => $invoices
            ]
        ];
    }

    public function invoiceDownload($id)
    {
    	abort_in_demo();
        $user = Auth::user();

        $data = [
            'vendor' => $user->name,
            'product' => 'Product',
            'vat' => nl2br(e($user->extra_billing_info)),
        ];

        return $user->downloadInvoice($id, $data);
    }

    public function teamDelete($id)
    {
    	abort_in_demo();
    	// check if its the team owner
    	$team = Team::where('owner_id', Auth::id())
    		->where('id', $id)
    		->firstOrFail();

    	$currentTeam = Auth::user()->currentTeam();

    	if($currentTeam->id == $id) {
	    	return response()->json([
		        	'message' => __('app.cannot_delete'),
			        'errors' => []
			    ], 422);
    	}

    	dispatch(new DeleteTeam($team));
    }
}
