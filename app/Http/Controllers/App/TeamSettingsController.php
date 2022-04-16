<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facades\TeamConfig;
use Auth;
use DB;
use Storage;
use App\Tenant\Quotation\Status as QuotationStatus;
use App\Tenant\Invoice\Status as InvoiceStatus;
use App\Tenant\PurchaseOrder\Status as PurchaseOrderStatus;

class TeamSettingsController extends Controller
{
	public function generalGet()
	{
		$this->authorize('settings', 'general');

		$team = Auth::user()->currentTeam();

		return [
			'form' => [
				'name' => $team->name,
				'date_format' => $team->date_format,
				'timezone' => $team->timezone,
				'currency_id' => $team->currency_id,
				'lang_id' => $team->lang_id,
				// 'logo_file' => $team->logo_file,
				'enable_discount' => TeamConfig::get('enable_discount')
			],
			'options' => [
				'date_formats' => config('meridian.date_formats'),
				'timezones' => config('timezone'),
				'currencies' => DB::table('currencies')->orderBy('name')->get(),
				'langs' => config('meridian.langs')
			]
		];
	}

	public function generalPost(Request $request)
	{
		abort_in_demo();
		$this->authorize('settings', 'general');

		$request->validate([
			'name' => 'required',
			'timezone' => 'required',
			'date_format' => 'required',
			'currency_id' => 'required',
			'lang_id' => 'required',
			'enable_discount' => 'required|boolean',
		]);

		$team = Auth::user()->currentTeam();

    	$team->fill(
    		$request->only(
    			'name', 'timezone', 'date_format',
    			'currency_id', 'lang_id'
    		)
    	);

    	$team->save();

    	TeamConfig::set('enable_discount', $request->enable_discount);

    	return [
            'saved' => true
        ];
	}

	public function taxesGet()
	{
		$this->authorize('settings', 'taxes');
		$config = TeamConfig::getMany([
			'registration_label',
			'tax_enable',
			'tax_label',
			'tax_rate',
			'tax_2_enable',
			'tax_2_label',
			'tax_2_rate',
			'company_tax_id'
		]);

		return [
			'form' => $config
		];
	}

	public function taxesPost(Request $request)
	{
		abort_in_demo();
		$this->authorize('settings', 'taxes');
		$data = $request->validate([
			'tax_enable' => 'required|boolean',
			'tax_label' => 'required_if:tax_enable,true',
			'tax_rate' => 'required_if:tax_enable,true|numeric',
			'tax_2_enable' => 'required|boolean',
			'tax_2_label' => 'required_if:tax_2_enable,true',
			'tax_2_rate' => 'required_if:tax_2_enable,true|numeric',
			'registration_label' => 'required_if:tax_enable,true',
			'company_tax_id' => 'nullable'
		]);

		TeamConfig::setMany($data);

		if(request()->has('set') && request('set') === 'completed') {
			$team = Auth::user()->currentTeam();
			$team->is_setup = true;
			$team->save();
		}

		return [
			'saved' => true
		];
	}

	public function documentsGet()
	{
		$this->authorize('settings', 'documents');
		$config = TeamConfig::getMany([
			'company_name',
			'company_address',
			'company_email',
			'company_telephone',
			'company_fax',
			'company_logo',
		]);

		return [
			'form' => $config
		];
	}

	public function documentsPost(Request $request)
	{
		abort_in_demo();
		$this->authorize('settings', 'documents');
		$rules = [
			'company_name' => 'required',
			'company_address' => 'required',
			'company_email' => 'email',
			'company_telephone' => 'nullable',
			'company_fax' => 'nullable',
			// 'company_logo' => 'nullable'
		];

        $data = $request->validate($rules);

		TeamConfig::setMany($data);

		return [
			'saved' => true
		];
	}

	public function logoPost(Request $request)
	{
		abort_in_demo();
		$this->authorize('settings', 'documents');
		$request->validate([
			'image_upload' => 'required|image|max:'.config('meridian.max_logo_size')
		]);

		if($request->hasfile('image_upload')) {
			$filename = str_random(16);

			$path = $request->file('image_upload')
				->store(
				    'logos', 'public'
				);

			if($file = TeamConfig::get('company_logo')) {
				Storage::disk('public')
				->delete($file);
			}

			TeamConfig::set('company_logo', $path);

			return [
                'saved' => true,
                'image' => $path
            ];
		}

		return abort(404);
	}

	public function quotationsGet()
	{
		$this->authorize('settings', 'quotations');
		$config = TeamConfig::getMany([
			'quotation_status_on_create_id',
			'quotation_status_on_create',
			'quotation_status_on_sent_id',
			'quotation_status_on_sent',
			'quotation_status_on_invoiced_id',
			'quotation_status_on_invoiced',
			'quotation_email_subject',
			'quotation_email_template'
		]);

    	if($id = $config['quotation_status_on_sent_id']) {
    		$c = QuotationStatus::findOrFail($id);
    		$config['quotation_status_on_sent'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];
    	}

    	if($id = $config['quotation_status_on_create_id']) {
    		$c = QuotationStatus::findOrFail($id);
    		$config['quotation_status_on_create'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];
    	}

    	if($id = $config['quotation_status_on_invoiced_id']) {
    		$c = QuotationStatus::findOrFail($id);
    		$config['quotation_status_on_invoiced'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];
    	}

		return [
			'form' => $config
		];
	}

	public function quotationsPost(Request $request)
	{
		abort_in_demo();
		$this->authorize('settings', 'quotations');
		$rules = [
			'quotation_status_on_create_id' => 'required|integer',
			'quotation_status_on_sent_id' => 'required|integer',
			'quotation_status_on_invoiced_id' => 'required|integer',
			'quotation_email_subject' => 'required',
			'quotation_email_template' => 'required'
		];

        $data = $request->validate($rules);

		TeamConfig::setMany($data);

		return [
			'saved' => true
		];
	}

	public function invoicesGet()
	{
		$this->authorize('settings', 'invoices');
		$config = TeamConfig::getMany([
			'invoice_status_on_create_id',
			'invoice_status_on_create',
			'invoice_status_on_sent_id',
			'invoice_status_on_sent',
			'invoice_status_on_partially_paid_id',
			'invoice_status_on_partially_paid',
			'invoice_status_on_paid_id',
			'invoice_status_on_paid',
			'invoice_email_subject',
			'invoice_email_template'
		]);

    	if($id = $config['invoice_status_on_sent_id']) {
    		$c = InvoiceStatus::findOrFail($id);
    		$config['invoice_status_on_sent'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];
    	}

    	if($id = $config['invoice_status_on_create_id']) {
    		$c = InvoiceStatus::findOrFail($id);
    		$config['invoice_status_on_create'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];
    	}

    	if($id = $config['invoice_status_on_partially_paid_id']) {
    		$c = InvoiceStatus::findOrFail($id);
    		$config['invoice_status_on_partially_paid'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];
    	}

    	if($id = $config['invoice_status_on_paid_id']) {
    		$c = InvoiceStatus::findOrFail($id);
    		$config['invoice_status_on_paid'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];
    	}

		return [
			'form' => $config
		];
	}

	public function invoicesPost(Request $request)
	{
		abort_in_demo();
		$this->authorize('settings', 'invoices');
		$rules = [
			'invoice_status_on_create_id' => 'required|integer',
			'invoice_status_on_sent_id' => 'required|integer',
			'invoice_status_on_partially_paid_id' => 'required|integer',
			'invoice_status_on_paid_id' => 'required|integer',
			'invoice_email_subject' => 'required',
			'invoice_email_template' => 'required'
		];

        $data = $request->validate($rules);

		TeamConfig::setMany($data);

		return [
			'saved' => true
		];
	}

	public function paymentsGet()
	{
		$this->authorize('settings', 'payments');
		$config = TeamConfig::getMany([
			'payment_email_subject',
			'payment_email_template',
			'payment_notification_email'
		]);

		return [
			'form' => $config
		];
	}

	public function paymentsPost(Request $request)
	{
		abort_in_demo();
		$this->authorize('settings', 'payments');
		$rules = [
			'payment_email_subject' => 'required',
			'payment_email_template' => 'required',
			'payment_notification_email' => 'required|email'
		];

        $data = $request->validate($rules);

		TeamConfig::setMany($data);

		return [
			'saved' => true
		];
	}

	public function refundsGet()
	{
		$this->authorize('settings', 'refunds');
		$config = TeamConfig::getMany([
			'refund_email_subject',
			'refund_email_template'
		]);

		return [
			'form' => $config
		];
	}

	public function refundsPost(Request $request)
	{
		abort_in_demo();
		$this->authorize('settings', 'refunds');
		$rules = [
			'refund_email_subject' => 'required',
			'refund_email_template' => 'required'
		];

        $data = $request->validate($rules);

		TeamConfig::setMany($data);

		return [
			'saved' => true
		];
	}

	public function purchaseOrderGet()
	{
		$this->authorize('settings', 'purchase_orders');
		$config = TeamConfig::getMany([
			'purchase_order_status_on_create_id',
			'purchase_order_status_on_create',
			'purchase_order_status_on_sent_id',
			'purchase_order_status_on_sent',
			'purchase_order_status_on_partially_paid_id',
			'purchase_order_status_on_partially_paid',
			'purchase_order_status_on_paid_id',
			'purchase_order_status_on_paid',
			'purchase_order_email_subject',
			'purchase_order_email_template'
		]);

    	if($id = $config['purchase_order_status_on_sent_id']) {
    		$c = PurchaseOrderStatus::findOrFail($id);
    		$config['purchase_order_status_on_sent'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];
    	}

    	if($id = $config['purchase_order_status_on_create_id']) {
    		$c = PurchaseOrderStatus::findOrFail($id);
    		$config['purchase_order_status_on_create'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];
    	}

    	if($id = $config['purchase_order_status_on_partially_paid_id']) {
    		$c = PurchaseOrderStatus::findOrFail($id);
    		$config['purchase_order_status_on_partially_paid'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];
    	}

    	if($id = $config['purchase_order_status_on_paid_id']) {
    		$c = PurchaseOrderStatus::findOrFail($id);
    		$config['purchase_order_status_on_paid'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];
    	}

		return [
			'form' => $config
		];
	}

	public function purchaseOrderPost(Request $request)
	{
		abort_in_demo();
		$this->authorize('settings', 'purchase_orders');
		$rules = [
			'purchase_order_status_on_create_id' => 'required|integer',
			'purchase_order_status_on_sent_id' => 'required|integer',
			'purchase_order_status_on_partially_paid_id' => 'required|integer',
			'purchase_order_status_on_paid_id' => 'required|integer',
			'purchase_order_email_subject' => 'required',
			'purchase_order_email_template' => 'required'
		];

        $data = $request->validate($rules);

		TeamConfig::setMany($data);

		return [
			'saved' => true
		];
	}
}
