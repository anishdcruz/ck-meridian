<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Admin extends Authenticatable
{
	use Filterable;

	protected $connection = 'mysql';

	protected $guard = 'admin';

	protected $fillable = ['name', 'email', 'password'];

    protected $hidden = [
    	'password',
        'remember_token'
    ];

    protected $nonFilterable = [
    	'password',
        'remember_token'
    ];

    protected $nonSearchable = [
    	'password',
        'remember_token'
    ];
}
