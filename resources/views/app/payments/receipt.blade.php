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
				<div class="s">
					<h1>{{__('app.payment_receipt')}}</h1>
				</div>
				<p>{{__('app.number_p')}} {{$model->number}}</p>
			</div>
			<div class="invoice-body">
				<div class="invoice-body-item">
					<table style="width: 100%;">
					    <tbody>
					        <tr>
					            <td>{{__('app.payment_date_p')}}</td>
					            <td>{{formatDate($model->payment_date)}}</td>
					        </tr>
					        <tr>
					            <td>{{__('app.invoice_p')}}</td>
					            <td>{{$model->invoice->number}}</td>
					        </tr>
					        <tr>
					            <td>{{__('app.method')}}</td>
					            <td>{{__('app.'.$model->method)}}</td>
					        </tr>
					        @if($model->charge_id)
					        <tr>
					            <td>{{__('app.payment_id_p')}}</td>
					            <td>{{$model->charge_id}}</td>
					        </tr>
					        @endif
					    </tbody>
					</table>
				</div>
			</div>
			<div class="invoice-footer">
				<div class="invoice-footer-item">
					<strong>{{__('app.amount_received')}}</strong>
					<p>{{formatMoney($model->amount_received)}}</p>
				</div>
			</div>
		</div>
	</div>
</body>
</html>