<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\CategoryController;
use App\Tenant\Item\Uom;
use DB;

class ItemUomController extends CategoryController
{
	protected $model = Uom::class;

	protected $parent = 'items';

	protected $title = 'items';

	protected $table = 'item_uoms';

	public function confirmDestroy($model, $db)
    {
    	if(DB::connection('team')->table('items')->where('uom_id', $model->id)->count()) {
    	    return false;
    	}

    	return true;
    }
}
