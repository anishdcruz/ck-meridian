<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe\OAuth;
use Stripe\Error\OAuth\OAuthBase;
use Stripe\Stripe;
use Stripe\Account;
use App\StripeConnectAccount;

class StripeConnectController extends Controller
{
    public function show()
    {
    	$this->authorize('settings', 'payment_gateway');
    	$team = auth()->user()
    		->currentTeam();

    	if($team->stripe_user_id) {
            $stripe =StripeConnectAccount::where('team_id', $team->id)
                ->first();

            $form = [
                'charges_enabled' => $stripe->charges_enabled,
                'country' => $stripe->country,
                'default_currency' => $stripe->default_currency,
                'email' => $stripe->email,
                'payouts_enabled' => $stripe->payouts_enabled,
                'stripe_user_id' => $team->stripe_user_id,
                'livemode' => $team->livemode
            ];
    		return [
    			'form' => $form
    		];
    	}
    	return [
    		'form' => [
    			'url' => $this->getURL()
    		]
    	];
    }

    protected function getURL()
    {
    	$clientId = config('services.stripe.connect_client_id');
    	$redirectTo = route('app.stripe.connect');

    	$url = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id=$clientId&scope=read_write&redirect_uri=$redirectTo";
    	return $url;
    }

    public function connect(Request $request)
    {
    	abort_in_demo();
    	$this->authorize('settings', 'payment_gateway');
    	Stripe::setApiKey(config('services.stripe.secret'));
    	Stripe::setClientId(config('services.stripe.connect_client_id'));

    	if($request->has('scope') && $request->has('code')) {
    		// accepted
    		try {
		        $resp = OAuth::token([
		            'grant_type' => 'authorization_code',
		            'code' => $request->code,
		        ]);
		    } catch (OAuthBase $e) {
		    	abort(404, "Error: " . $e->getMessage());
		    }

		    $team = auth()->user()
		    	->currentTeam();

		    $team->stripe_user_id = $resp->stripe_user_id;
		    $team->livemode = $resp->livemode;
		    $team->stripe_connected_at = now();
		    $team->save();

		    $sa = Account::retrieve($team->stripe_user_id);

		    $acc = new StripeConnectAccount;
		    $acc->team_id = $team->id;
		    $acc->charges_enabled = $sa->charges_enabled;
		    $acc->country = $sa->country;
		    $acc->default_currency = $sa->default_currency;
		    $acc->details_submitted = $sa->details_submitted;
		    $acc->email = $sa->email;
		    $acc->payouts_enabled = $sa->payouts_enabled;
		    $acc->save();

		    return redirect(route('app.index'). '/team-settings/payment-gateway');
    	} else {
    		abort(404, 'Could not connect to stripe!');
    	}
    }

    public function disconnect()
    {
    	abort_in_demo();
    	$this->authorize('settings', 'payment_gateway');
    	Stripe::setApiKey(config('services.stripe.secret'));
    	Stripe::setClientId(config('services.stripe.connect_client_id'));

    	$team = auth()->user()
		    	->currentTeam();
    	try {
	        OAuth::deauthorize([
	            'stripe_user_id' => $team->stripe_user_id
	        ]);
	    } catch (OAuthBase $e) {
	        abort(404, "Error: " . $e->getMessage());
    	}

        $team->stripe_user_id = null;
        $team->stripe_connected_at = null;
        $team->livemode = 0;
        $team->save();

        StripeConnectAccount::where('team_id', $team->id)
            ->delete();

    	return [
    		'disconnect' => true,
    		'url' => $this->getURL()
    	];
    }
}
