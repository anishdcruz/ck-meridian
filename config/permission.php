<?php

$settings = [
	'general' => 1,
	'documents' => 1,
	'taxes' => 1,
	'contacts' => 1,
	'items' => 1,
	'quotations' => 1,
	'invoices' => 1,
	'payments' => 1,
	'refunds' => 1,
	'vendors' => 1,
	'purchase_orders' => 1,
	'expenses' => 1,
	'terms' => 1
];

if(inSAASMode()) {
	$settings += [
		'payment_gateway' => 1
	];
}

$settings += [
	'members' => 1,
	'invitations' => 1,
	'roles_and_permissions' => 1
];

return [
	'modules' => [
		'contacts' => ['view' => 1, 'create' => 1, 'update' => 1, 'delete' => 1, 'export' => 1],
		'items' => ['view' => 1, 'create' => 1, 'update' => 1, 'delete' => 1, 'export' => 1],
		'quotations' => ['view' => 1, 'create' => 1, 'update' => 1, 'delete' => 1, 'export' => 1, 'change_status' => 1, 'send_email' => 1],
		'invoices' => ['view' => 1, 'create' => 1, 'update' => 1, 'delete' => 1, 'export' => 1, 'change_status' => 1, 'send_email' => 1],
		'recurring_invoices' => ['view' => 1, 'create' => 1, 'update' => 1, 'delete' => 1, 'export' => 1],
		'payments' => ['view' => 1, 'create' => 1, 'delete' => 1, 'refund' => 1, 'export' => 1, 'send_email' => 1],
		'refunds' => ['view' => 1, 'create' => 1, 'delete' => 1, 'export' => 1, 'send_email' => 1],
		'vendors' => ['view' => 1, 'create' => 1, 'update' => 1, 'delete' => 1, 'export' => 1],
		'purchase_orders' => ['view' => 1, 'create' => 1, 'update' => 1, 'delete' => 1, 'export' => 1, 'change_status' => 1, 'send_email' => 1],
		'payments_made' => ['view' => 1, 'create' => 1, 'delete' => 1, 'export' => 1],
		'expenses' => ['view' => 1, 'create' => 1, 'delete' => 1, 'export' => 1],
		'recurring_exports' => ['view' => 1, 'create' => 1, 'delete' => 1],
	],
	'settings' => $settings
];