<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Meridian;
use App\Repository\UserRepository;
use App\Repository\TeamRepository;
use App\Plan;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $user;

    protected $team;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $user, TeamRepository $team)
    {
        $this->user = $user;
        $this->team = $team;

        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'company_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'terms' => ['required', 'accepted'],
            'plan' => ['required', 'integer', 'exists:plans,id,active,1']
        ];

        if(Meridian::isCardUpFrontRequired()) {
            $rules['stripeToken'] = ['required'];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
    	abort_in_demo();

        $plan = Plan::findOrFail($data['plan']);

        $withSubscription = $plan->price > 0;

        // create user
        $user = $this->user->createUserFromRegistration(
            $data,
            $withSubscription,
            $plan
        );

        // create team
        $team = $this->team->create($user, [
            'name' => $data['company_name']
        ]);

        // set redirect
        $this->redirectTo = route('app.index');

        return $user;
    }
}
