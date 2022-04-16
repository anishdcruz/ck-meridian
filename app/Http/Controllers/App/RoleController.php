<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Role;
use DB;


class RoleController extends Controller
{
	// public function __construct()
	// {
	// 	$this->authorize('settings', 'roles_and_permissions');
	// }

    public function search()
    {
    	$this->authorize('settings', 'roles_and_permissions');
        return [
            'collection' => Role::team()->search()
        ];
    }

    public function index()
    {
    	$this->authorize('settings', 'roles_and_permissions');
    	return [
    		'collection' => Role::team()->filter()
    	];
    }

    public function create()
    {
    	$this->authorize('settings', 'roles_and_permissions');
        $item = [
            'name' => '',
            'description' => '',
            'permissions' => config('permission')
        ];

        return [
            'form' => $item
        ];
    }

    public function store(Request $request)
    {
    	abort_in_demo();
    	$this->authorize('settings', 'roles_and_permissions');
        $request->validate([
            'name' => 'required|unique:roles,name',
            'description' => 'required|string',
            'permissions' => 'required'
        ]);

        $item = new Role;
        $item->team_id = auth()->user()->current_team_id;
        $item->name = $request->name;
        $item->description = $request->description;
        $item->permissions = json_encode($request->permissions);
        $item->save();

        return [
            'saved' => true,
            'id' => $item->id
        ];
    }

    public function show($id)
    {
    	$this->authorize('settings', 'roles_and_permissions');
        $item = Role::team()->findOrFail($id);

        return [
        	'model' => $item
        ];
    }

    public function edit($id)
    {
    	$this->authorize('settings', 'roles_and_permissions');
        $item = Role::team()->findOrFail($id);

        return [
        	'form' => $item
        ];
    }

    public function update($id, Request $request)
    {
    	abort_in_demo();
    	$this->authorize('settings', 'roles_and_permissions');
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'permissions' => 'required|array'
        ]);

        $item = Role::team()->findOrFail($id);
        $item->name = $request->name;
        $item->description = $request->description;
        $item->permissions = json_encode($request->permissions);
        $item->save();

        return [
            'saved' => true,
            'id' => $item->id
        ];
    }

    public function destroy($id)
    {
    	abort_in_demo();
    	$this->authorize('settings', 'roles_and_permissions');
        $model = Role::team()->findOrFail($id);

        if(DB::table('user_teams')->where(['role_id' => $model->id, 'team_id' => get_team()->id])->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('user_invites')->where('role_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return [
            'deleted' => true
        ];
    }
}
