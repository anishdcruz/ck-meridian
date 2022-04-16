<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Support\PDF;
use Mail;
use App\Mail\SendDocument;

class MeridianWithItemController extends Controller {

	protected $hasCounter = true;
	protected $withIndex = [];
	protected $withShow = [];
	protected $withEdit = [];

	protected $createForm = [];

	protected $typeaheadColumns = [];
	protected $typeaheadRequired = [];

	protected function response($arr)
	{
		return response()
			->json($arr);
	}

	public function search()
	{
        $collection = $this->model::search();

		return $this->response([
    		'collection' => $collection
    	]);
	}

	public function typeahead()
    {

        $collection = $this->model::typeahead(
        	$this->typeaheadColumns,
        	$this->typeaheadRequired
        );

        return [
            'results' => $collection
        ];
    }

	public function getIndex()
	{
		return $this->model::with($this->withIndex)->filter();
	}

	public function index()
    {
    	$this->authorize('module', $this->title.'.view');
    	$collection = $this->getIndex();

    	return $this->response([
    		'collection' => $collection
    	]);
    }

    public function create()
    {
    	$this->authorize('module', $this->title.'.create');
    	$form = $this->createForm();

    	return $this->response([
    		'form' => $form
    	]);
    }

    public function inShow($model)
    {
    	return $model;
    }

    public function show($id)
    {
    	$this->authorize('module', $this->title.'.view');
    	$model = $this->model::with($this->withShow)
    		->findOrFail($id);

    	$model = $this->inShow($model);

    	return $this->response([
    		'model' => $model
    	]);
    }

    public function createRules()
    {
    	return [];
    }

    public function inCreateTransaction($form)
    {
    	return $form;
    }

    public function afterCreate($form)
    {
    	return $form;
    }

    public function store(Request $request)
    {
    	abort_in_demo();
    	$this->authorize('module', $this->title.'.create');
    	$request->validate($this->createRules());

    	$form = $this->model::getModel();
    	$form->fill($request->all());

    	$form = DB::transaction(function() use ($form) {
    		if($this->hasCounter) {
    			$c = counter($this->counter);
    			$form->number = $c->number;
			    $form = $this->inCreateTransaction($form);
			    $form->storeHasMany([
		            'lines' => request('lines')
		        ]);
    			$c->increment('value');
    		}
    	    $form = $this->afterCreate($form);
    	    return $form;
    	});

    	return $this->response([
    		'saved' => true,
    		'id' => $form->id
    	]);
    }

    public function inEdit($model)
    {
    	return $model;
    }

    public function edit($id)
    {
    	$this->authorize('module', $this->title.'.update');
    	$form = $this->model::with($this->withEdit)
    		->findOrFail($id);

    	$form = $this->inEdit($form);

    	return $this->response([
    		'form' => $form
    	]);
    }

    public function updateRules($id)
    {
    	return [];
    }

    public function afterUpdate($form)
    {
    	return $form;
    }

    public function update($id, Request $request)
    {
    	abort_in_demo();
    	$this->authorize('module', $this->title.'.update');
    	$request->validate($this->updateRules($id));

    	$form = $this->model::findOrFail($id);
    	$form->fill($request->all());
    	$form = DB::transaction(function() use ($form, $request) {
    		$form = $this->inUpdateTransaction($form);
    	    $form->updateHasMany([
    	        'lines' => request('lines')
    	    ]);
    	     $form = $this->afterUpdate($form);
    	    return $form;
    	});

    	return $this->response([
    		'saved' => true,
    		'id' => $form->id
    	]);
    }

    public function markAs($id, Request $request)
    {
    	$this->authorize('module', $this->title.'.change_status');
        $request->validate([
            'type' => 'required|integer|exists:team.'.$this->statusTable.',id,locked,0'
        ]);

        $model = $this->model::findOrFail($id);
        $model->status_id = $request->type;
        $model->save();

        return $this->response([
            'saved' => true,
            'id' => $model->id,
            'status' => $model->status
        ]);
    }

    public function downloadPDF($id, Request $request, PDF $pdf)
    {
        $model = $this->model::with($this->withShow)
        	->findOrFail($id);

        return $pdf
            ->preview('app.pdf.'.$this->printFile, [
                'model' => $model
            ]);

    }

    public function confirmDestroy($model, $db)
    {
    	return true;
    }

    public function destroy($id)
    {
    	abort_in_demo();
    	$this->authorize('module', $this->title.'.delete');
    	$found = $this->model::findOrFail($id);

    	if(!$this->confirmDestroy($found, new DB)) {
	    	return response()->json([
	        	'message' => __('app.cannot_delete'),
		        'errors' => []
		    ], 422);
    	}

    	$found->delete();

    	return $this->response([
    		'deleted' => true,
    		'id' => $id
    	]);
    }

    public function export(Request $request)
    {
    	abort_in_demo();
    	$this->authorize('module', $this->title.'.export');
        return Excel::download($this->setExport($request), __('app.'.$this->title).'.xlsx');
    }

    public function emailVariables($model)
    {
    	return $model;
    }

    public function getEmailForm($model)
    {
    	return [
    	    'email_to' => $model->contact->email
    	];
    }

    public function getEmail($id)
    {
    	$this->authorize('module', $this->title.'.send_email');
    	$model = $this->model::findOrFail($id);

    	$user = auth()->user();

    	$warning = null;

    	$allowedVars = array_merge($this->emailVariables($model), [
    		'company' => team_config('company_name')
    	]);

    	$subject = parseStringTemplate(
    		team_config($this->emailSubject),
    		$allowedVars
    	);

    	$team = $user->currentTeam();

    	$messageTemplate = team_config($this->emailTemplate);

    	$pG = false;
    	if(inSAASMode()) {
    		$pG = $team->hasPaymentGateway();
    	} else {
    		$pG = config('meridian.enable_payment');
    	}

    	if($pG) {
            $messageTemplate = preg_replace('/(@paymentGateway|@endPaymentGateway)/im', '', $messageTemplate);
        } else {
        	$messageTemplate = preg_replace("/@paymentGateway[\s\S]+?@endPaymentGateway/", '', $messageTemplate);
        }

    	$message = parseStringTemplate(
    		$messageTemplate,
    		$allowedVars
    	);

    	$form = array_merge($this->getEmailForm($model), [
    		'bcc' => $user->email,
    		'subject' =>  $subject,
    		'message' => $message
    	]);


    	return [
    	    'form' => $form,
    	    'warning' => $warning
    	];
    }

    public function postEmail($id, Request $request)
    {
    	abort_in_demo();
    	$this->authorize('module', $this->title.'.send_email');
    	$info = $request->validate([
    	    'email_to' => 'required|email',
    	    'bcc' => 'nullable|email',
    	    'subject' => 'required',
    	    'message' => 'required'
    	]);

    	$model = $this->model::with($this->withShow)
            ->findOrFail($id);

        $team = get_team();

        Mail::send(new SendDocument($model, $info, $this->printFile, $team));

        if($id = team_config($this->onEmailSent)) {
        	$model->status_id = $id;
        	$model->save();
        }

        return [
            'saved' => true
        ];
    }
}