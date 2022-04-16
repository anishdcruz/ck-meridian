<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Hash;
use Auth;

class MyAccountController extends Controller
{
    public function generalGet()
    {
    	$user = Auth::guard('admin')->user();

    	return [
			'form' => [
				'name' => $user->name,
                'email' => $user->email
			]
		];
    }

    public function generalPost(Request $request)
    {
    	abort_in_demo();
    	$request->validate([
    		'name' => 'required|string'
    	]);

    	$user = Auth::guard('admin')->user();

    	$user->fill(
    		$request->only('name')
    	);

    	$user->save();

    	return [
            'saved' => true
        ];
    }

    public function securityGet()
    {
    	$user = Auth::guard('admin')->user();

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

    	$user = Auth::guard('admin')->user();

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
}
