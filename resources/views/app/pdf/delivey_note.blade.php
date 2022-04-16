@extends('app.pdf.master', $team)

@section('content')
    <div class="content">
        <div class="content-title">
            {{__('app.delivery_note')}}
        </div>
        <table class="summary">
            <tbody>
                <tr>
                    <td class="summary-address">
                        <strong>{{__('app.to')}}</strong>
                        <p>{{$model->contact->name}}</p>
                        @if($model->contact->organization)
                            <p>{{$model->contact->organization}}</p>
                        @endif
                        <pre>{{$model->contact->primary_address}}</pre>
                        @if($team['tax_enable'])
                            <p>{{$team['registration_label']}}: {{$model->contact->tax_id}}</p>
                        @endif
                    </td>
                    <td class="summary-empty"></td>
                    <td class="summary-info">
                        <table class="info">
                            <tbody>
                                <tr>
                                    <td>{{__('app.invoice_number')}}</td>
                                    <td>{{$model->number}}</td>
                                </tr>
                                <tr>
                                    <td>{{__('app.invoice_date')}}</td>
                                    <td>{{formatDate($model->issue_date)}}</td>
                                </tr>
                                <tr>
                                    <td>{{__('app.delivery_date')}}</td>
                                    <td>{{formatDate(now())}}</td>
                                </tr>
                                @if($model->reference)
                                	<tr>
                                	    <td>{{__('app.reference_p')}}</td>
                                	    <td>{{$model->reference}}</td>
                                	</tr>
                                @endif
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="items">
            <thead>
                <tr>
                    <th class="w-6">{{__('app.code')}}</th>
                    <th class="w-10">{{__('app.description')}}</th>
                    <th class="w-4">{{__('app.uom')}}</th>
                    <th class="w-4">{{__('app.qty')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($model->lines as $line)
                    <tr>
                        <td class="align-left">{{$line->item->code}}</td>
                        <td class="align-left"><pre>{{$line->item->description}}</pre></td>
                        <td class="align-left">{{$line->item->uom->name}}</td>
                        <td class="align-right">{{$line->qty}}</td>
                    </tr>
                @endforeach
                @include('app.pdf.blank_items', ['td' => 4, 'lines' => $model->lines])
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td align="right" colspan="1">{{__('app.total_qty')}}</td>
                    <td class="align-center">{{$model->lines->sum('qty')}}</td>
                </tr>
            </tfoot>
        </table>
        <table class="sign" border="1">
            <tbody>
                <tr>
                    <td style="width: 30%;">
                        <div>
                        	<strong>{{__('app.prepared_by')}}</strong>
                        </div>
                    </td>
                    <td style="width: 20%;"></td>
                    <td style="width: 50%;">
                    	<div>
                    		{{__('app.received_in')}}
                    		<br><br>
                    		<table class="sign-table">
                    			<tbody>
                    				<tr>
                    					<td><strong>{{__('app.receiver')}}</strong></td>
                    					<td width="50%">
                    						<br><br>
                    						<br>
                    					</td>
                    				</tr>
                    				<tr>
                    					<td><strong>{{__('app.sign')}}</strong></td>
                    					<td><br><br>
                    						<br></td>
                    				</tr>
                    				<tr>
                    					<td><strong>{{__('app.date')}}</strong></td>
                    					<td><br><br>
                    						<br></td>
                    				</tr>
                    			</tbody>
                    		</table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection