<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facades\TeamConfig;

class AppController extends Controller
{
    public function index()
    {
        $teamSettings = TeamConfig::getMany([
            'enable_discount',
            'registration_label',
            'tax_enable',
            'tax_label',
            'tax_rate',
            'tax_2_enable',
            'tax_2_label',
            'tax_2_rate',
        ]);

    	return view('app.app', [
            'teamSettings' => $teamSettings
        ]);
    }

    public function switchTeam(Request $request, $teamId)
    {
    	$user = $request->user();

    	$team = $user->teams()->findOrFail($teamId);

    	$user->switchToTeam($team);

    	return back();
    }
}
