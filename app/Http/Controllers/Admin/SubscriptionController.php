<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Subscription;
use Auth;

class SubscriptionController extends AdminController
{
    protected $model = Subscription::class;

    protected $title = 'subscription';

    protected $withIndex = ['user'];

    protected $withShow = ['user'];

    public function store(Request $request)
    {

    }

    public function update($id, Request $request)
    {
    	// restore
    	$sub = Subscription::findOrFail($id);

    	$sub->user->subscription('main')->resume();

    	// restore email

    	return [
    	    'saved' => true
    	];
    }

    public function destroy($id)
    {
    	// unsubscribe
    	abort_in_demo();
    	$sub = Subscription::findOrFail($id);

    	$sub->user->subscription('main')->cancel();
    }
}
