<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="{{asset('css/frontend.css')}}">
</head>
<body>
	<div class="invoice-body">
		<div class="invoice">
			<div class="invoice-header">
				<div class="invoice-logo_holder">
					<img src="{{asset($config['company_logo'])}}" class="invoice-logo">
				</div>
				<h1>Invoice from {{$config['company_name']}}</h1>
				<p>Invoice Number: {{$invoice->number}}</p>
			</div>
			<div class="invoice-body">
				<div class="invoice-body-item">
					<strong>Bill To</strong><br>
					<p>{{$invoice->contact->name}}</p>
					@if($invoice->contact->organization)
					    <p>{{$invoice->contact->organization}}</p>
					@endif
					<pre>{{$invoice->contact->primary_address}}</pre>
					@if($config['tax_enable'])
					    <p>{{$config['registration_label']}}: {{$invoice->contact->tax_id}}</p>
					@endif
				</div>
				<div class="invoice-body-item">
					<table class="info">
					    <tbody>
					        <tr>
					            <td><strong>Issue Date:</strong></td>
					            <td>{{formatDate($invoice->issue_date)}}</td>
					        </tr>
					        <tr>
					            <td><strong>Expiry Date:</strong></td>
					            <td>{{formatDate($invoice->due_date)}}</td>
					        </tr>
					    </tbody>
					</table>
				</div>
			</div>
			<div class="invoice-footer">
				<div class="invoice-footer-item">
					<strong>Amount Payable</strong>
					<p>{{formatMoney($invoice->grand_total)}}</p>
				</div>
				<div class="invoice-footer-item">
					<form action="{{route('app.invoice.store', ['team' => $team->uuid, 'invoice' => $invoice->payment_code])}}" method="post">
						{{csrf_field()}}

					  	<script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
					        data-key="{{config('services.stripe.key')}}"
					        data-name="{{$config['company_name']}}"
					      	data-description="{{__('app.payment_for')}} {{$invoice->number}}"
					      	data-email="{{$invoice->contact->email}}"
					      	data-image="{{asset($config['company_logo'])}}"
					      	data-amount="{{formatMoneyStripe($invoice->grand_total)}}"
					      	data-currency="{{$team->currency_id}}"
					      	data-locale="auto"></script>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>