<?php

// webhooks
Route::post(
    'meri/stripe/webhook',
    '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook'
)->name('stripe.webhook');

modeSwitcher(config('meridian.admin_prefix'), false,  function() {
	Route::get('login', 'Admin\AppController@showLogin')
		->middleware('guest:admin')
		->name('admin.login');

	Route::post('login', 'Admin\AppController@attemptLogin')
		->middleware('guest:admin')
		->name('admin.login');

	Route::group(['as' => 'admin.', 'middleware' => ['auth:admin']], function() {
		Route::group(['prefix' => 'api'], function() {
			Route::resource('filters', 'Admin\FilterController');

			Route::get('overview/{filter}', 'App\Admin\OverviewController@show');
			Route::get('overview', 'App\Admin\OverviewController@index');
			Route::post('user_metrics', 'App\Admin\UserMetricController@store');
			Route::put('user_metrics/{id}', 'App\Admin\UserMetricController@update');
			Route::delete('user_metrics/{user_metric}', 'App\Admin\UserMetricController@destroy');
			Route::put('user_metrics', 'App\Admin\UserMetricController@updateAll');

			Route::get('search/filters', 'Admin\FilterController@search');
			Route::module('teams', 'Admin\TeamController');
			Route::module('subscriptions', 'Admin\SubscriptionController');
			Route::module('users', 'Admin\UserController');
			Route::module('faqs', 'Admin\FaqController');
			Route::module('admins', 'Admin\AdminController');
			Route::module('plans', 'Admin\PlanController');
			Route::module('currencies', 'Admin\CurrencyController');

			Route::group(['as' => 'my_account.', 'prefix' => 'my-account'], function() {

				Route::settings('general', 'Admin\MyAccountController');
				Route::settings('security', 'Admin\MyAccountController');
			});
		});

		Route::get('/logout' , 'Admin\AppController@logout');
		Route::get('{vue?}', 'Admin\AppController@index')
			->where('vue', '[\/\w\.-]*')
			->name('index');
	});
});

modeSwitcher(config('meridian.app_prefix'), true,  function() {
	Auth::routes();
	Route::get('invitation/{id}/join', 'App\InvitationController@joinTeam')
		->name('app.invitation.join');

	// guest
	Route::get('invitation/{id}', 'App\InvitationController@registerGet')
		->name('app.invitation.register');

	// guest
	Route::post('invitation/{id}', 'App\InvitationController@registerPost')
		->name('app.invitation.save');

	Route::get('out/invoices/{team}/{invoice}', 'App\InvoicePaymentController@show')
		->name('app.invoice.show');
	Route::post('out/invoices/{team}/{invoice}', 'App\InvoicePaymentController@store')
		->name('app.invoice.store');

	Route::group(['middleware' => ['auth']], function() {
		Route::get('no-teams', 'App\Tenant\NoTeamController@show')
			->name('app.no_team');
		Route::post('no-teams/subscription', 'App\Tenant\NoTeamController@subscription')
			->name('app.no_team_subscription');

		Route::post('no-teams/team-create', 'App\Tenant\NoTeamController@teamCreate')
			->name('app.no_team_create_team');

	});

	Route::group(['as' => 'app.', 'middleware' => ['auth', 'team']], function() {
		Route::get('/logout' , 'Auth\LoginController@logout');

		Route::group(['prefix' => 'api'], function() {
			// team resources
			Route::module('contacts', 'App\Tenant\ContactController');
			Route::module('items', 'App\Tenant\ItemController');
			Route::module('quotations', 'App\Tenant\QuotationController');

			Route::module('invoices', 'App\Tenant\InvoiceController');
			Route::get('downloads/delivery-notes/{invoice}', 'App\Tenant\InvoiceController@deliveyNote');
			Route::post('payments/{payment}/refund', 'App\Tenant\PaymentController@refund');
			Route::post('payments/create', 'App\Tenant\PaymentController@store');
			Route::resource('payments', 'App\Tenant\PaymentController');
			Route::resource('refunds', 'App\Tenant\RefundController');
			Route::module('recurring-invoices', 'App\Tenant\RecurringInvoiceController');
			Route::resource('filters', 'App\Tenant\FilterController');

			Route::get('overview/{filter}', 'App\Tenant\OverviewController@show');
			Route::get('overview', 'App\Tenant\OverviewController@index');

			Route::post('user_metrics', 'App\Tenant\UserMetricController@store');
			Route::put('user_metrics/{id}', 'App\Tenant\UserMetricController@update');
			Route::delete('user_metrics/{user_metric}', 'App\Tenant\UserMetricController@destroy');
			Route::put('user_metrics', 'App\Tenant\UserMetricController@updateAll');

			Route::module('vendors', 'App\Tenant\VendorController');
			Route::module('purchase-orders', 'App\Tenant\PurchaseOrderController');
			Route::module('payments-made', 'App\Tenant\PaymentMadeController');
			Route::module('expenses', 'App\Tenant\ExpenseController');

			Route::resource('recurring-exports', 'App\Tenant\RecurringExportController');

			Route::group(['prefix' => 'downloads'], function() {
				Route::get('quotations/{quotation}', 'App\Tenant\QuotationController@downloadPDF');
				Route::get('invoices/{invoice}', 'App\Tenant\InvoiceController@downloadPDF');
				Route::get('payments/{payment}', 'App\Tenant\PaymentController@downloadPDF');
				Route::get('purchase-orders/{purchase_order}', 'App\Tenant\PurchaseOrderController@downloadPDF');
			});

			Route::group(['prefix' => 'exports'], function() {
				// Route::get('recurring-invoices', 'App\Tenant\RecurringInvoiceController@export');
				Route::get('payments', 'App\Tenant\PaymentController@export');
				Route::get('refunds', 'App\Tenant\RefundController@export');
			});

			Route::group(['prefix' => 'emails'], function() {
				Route::get('quotations/{quotation}', 'App\Tenant\QuotationController@getEmail');
				Route::post('quotations/{quotation}', 'App\Tenant\QuotationController@postEmail');

				Route::get('invoices/{invoice}', 'App\Tenant\InvoiceController@getEmail');
				Route::post('invoices/{invoice}', 'App\Tenant\InvoiceController@postEmail');

				Route::get('payments/{payment}', 'App\Tenant\EmailController@paymentsGet');
				Route::post('payments/{payment}', 'App\Tenant\EmailController@paymentsPost');

				Route::get('refunds/{refund}', 'App\Tenant\EmailController@refundsGet');
				Route::post('refunds/{refund}', 'App\Tenant\EmailController@refundsPost');

				Route::get('purchase-orders/{purchase_order}', 'App\Tenant\PurchaseOrderController@getEmail');
				Route::post('purchase-orders/{purchase_order}', 'App\Tenant\PurchaseOrderController@postEmail');
			});

			Route::group(['prefix' => 'mark-as'], function() {
				Route::post('quotations/{quotation}', 'App\Tenant\QuotationController@markAs');
				Route::post('invoices/{invoice}', 'App\Tenant\InvoiceController@markAs');
				Route::post('purchase-orders/{purchase_order}', 'App\Tenant\PurchaseOrderController@markAs');
			});

			Route::group(['prefix' => 'search'], function() {
				Route::get('contact_categories', 'App\Tenant\ContactCategoryController@search');
				Route::get('item_categories', 'App\Tenant\ItemCategoryController@search');

				Route::get('item_uoms', 'App\Tenant\ItemUomController@search');

				Route::get('quotation_statuses', 'App\Tenant\QuotationStatusController@search');
				Route::get('invoice_statuses', 'App\Tenant\InvoiceStatusController@search');

				Route::get('filters', 'App\Tenant\FilterController@search');
				Route::get('payments', 'App\Tenant\PaymentController@search');
				Route::get('refunds', 'App\Tenant\RefundController@search');

				Route::get('vendor_categories', 'App\Tenant\VendorCategoryController@search');
				Route::get('purchase_order_statuses', 'App\Tenant\PurchaseOrderStatusController@search');
				Route::get('expense_categories', 'App\Tenant\ExpenseCategoryController@search');
				Route::get('currencies', 'App\Tenant\CurrencyController@search');
			});

			Route::group(['prefix' => 'typeahead'], function() {
				Route::get('contacts', 'App\Tenant\ContactController@typeahead');
				Route::get('items', 'App\Tenant\ItemController@typeahead');
				Route::get('terms', 'App\Tenant\TermController@typeahead');
				Route::get('vendors', 'App\Tenant\VendorController@typeahead');
			});

			// my-account
			Route::group(['as' => 'my_account.', 'prefix' => 'my-account'], function() {

				Route::settings('general', 'App\MyAccountController');
				Route::settings('security', 'App\MyAccountController');
				if(inSAASMode()) {
					Route::get('subscription', 'App\MyAccountController@subscriptionGet')
						->name('subscription.get');
					Route::post('create-subscription', 'App\MyAccountController@subscriptionCreate')
						->name('subscription.create');
					Route::post('update-subscription', 'App\MyAccountController@subscriptionUpdate')
						->name('subscription.update');
					Route::post('cancel-subscription', 'App\MyAccountController@subscriptionCancel')
						->name('subscription.cancel');
					Route::post('restore-subscription', 'App\MyAccountController@subscriptionRestore')
						->name('subscription.restore');
					Route::get('payment-methods', 'App\MyAccountController@paymentMethodsGet')
						->name('payment_methods.get');
					Route::post('payment-methods', 'App\MyAccountController@paymentMethodsPost')
						->name('payment_methods.post');
				}
				Route::get('teams', 'App\MyAccountController@teamsGet')
					->name('teams.get');
				Route::post('teams', 'App\MyAccountController@teamsPost')
					->name('teams.post');
				Route::delete('teams/{id}', 'App\MyAccountController@teamDelete')
					->name('teams.delete');
				Route::get('invoices', 'App\MyAccountController@invoicesGet')
					->name('invoices.get');
				Route::get('download/invoices/{id}', 'App\MyAccountController@invoiceDownload')
					->name('invoices.download');
			});

			// team-settings
			Route::group(['as' => 'team_settings.', 'prefix' => 'team-settings'], function() {
				Route::settings('documents', 'App\TeamSettingsController');
				Route::post('logo', 'App\TeamSettingsController@logoPost');
				Route::settings('taxes', 'App\TeamSettingsController');
				Route::settings('general', 'App\TeamSettingsController');
				Route::resource('roles', 'App\RoleController');
				Route::resource('invitations', 'App\InvitationController');
				Route::get('search/roles', 'App\RoleController@search');


				if(inSAASMode()) {
					Route::post('payment-gateway/disconnect', 'App\StripeConnectController@disconnect');
					Route::get('payment-gateway', 'App\StripeConnectController@show');
				}

				Route::resource('members', 'App\MemberController');

				Route::settings('quotations', 'App\TeamSettingsController');
				Route::settings('invoices', 'App\TeamSettingsController');
				Route::settings('payments', 'App\TeamSettingsController');
				Route::settings('refunds', 'App\TeamSettingsController');

				Route::resource('expense_categories', 'App\Tenant\ExpenseCategoryController');
				Route::resource('contact_categories', 'App\Tenant\ContactCategoryController');
				Route::resource('item_categories', 'App\Tenant\ItemCategoryController');
				Route::resource('item_uoms', 'App\Tenant\ItemUomController');
				Route::resource('terms', 'App\Tenant\TermController');

				Route::resource('quotation_statuses', 'App\Tenant\QuotationStatusController');
				Route::resource('invoice_statuses', 'App\Tenant\InvoiceStatusController');

				Route::resource('vendor_categories', 'App\Tenant\VendorCategoryController');
				Route::resource('purchase_order_statuses', 'App\Tenant\PurchaseOrderStatusController');
				Route::get('purchase-orders', 'App\TeamSettingsController@purchaseOrderGet');
				Route::post('purchase-orders', 'App\TeamSettingsController@purchaseOrderPost');
			});
		});

		Route::get('stripe/connect', 'App\StripeConnectController@connect')
			->name('stripe.connect');
		Route::get('teams/switch/{id}', 'App\AppController@switchTeam');

		Route::get('{vue?}', 'App\AppController@index')
			->where('vue', '[\/\w\.-]*')
			->name('index');
	});
});

if(inSAASMode()) {
	Route::group(['as' => 'frontend.'], function() {

		Route::get('/', 'Frontend\FrontendController@home')
			->name('home');

		Route::get('/features', 'Frontend\FrontendController@features')
			->name('features');

		Route::get('/pricing', 'Frontend\FrontendController@pricing')
			->name('pricing');

		Route::get('/faqs', 'Frontend\FrontendController@faqs')
			->name('faqs');

		Route::get('/privacy', 'Frontend\FrontendController@privacy')
			->name('privacy');

		Route::get('/terms', 'Frontend\FrontendController@terms')
			->name('terms');

		Route::get('/about', 'Frontend\FrontendController@about')
			->name('about');
	});
}




