<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController as Controller;
use App\Admin;
use App\Admin\Filter;
use App\Admin\UserMetric;
use File;

class AdminController extends Controller
{
    protected $title = 'admins';
	protected $model = Admin::class;

	public function createForm()
    {
        return [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation'
        ];
    }

    public function createRules()
    {
    	return [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }

    public function inCreateTransaction($form)
    {

    	$form->password = bcrypt(request('password'));
    	return $form;
    }

    public function afterCreate($form)
    {
    	$this->addDashbaord($form);
    	return $form;
    }

    public function addDashbaord($u)
    {
    	// filters
  		// add default dashboard
		$file = File::get(base_path('database/seeds/admin_default_metrics.json'));
		$json = json_decode($file, 1);

		foreach($json as $key => $d) {
			$found = Filter::where('name', $d['filter']['name'])
				->first();

			if($found) {
				$re = new UserMetric;
				$re->fill($d);
				$re->filter_id = $found->id;
				$re->user_id = $u->id;
				$re->save();
			}
		}
    }

    public function inUpdateTransaction($form)
    {
    	$form->password = bcrypt(request('password'));
    	return $form;
    }

    public function confirmDestroy($model, $db)
	{
		if(auth()->id() == $model->id) {
			return false;
		}

		return true;
	}


    public function updateRules($id)
    {
    	return [
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,'.$id.',id',
            'password' => 'required|confirmed|min:6',
        ];
    }
}
