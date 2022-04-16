@extends('app.pdf.master', $team)

@section('content')
    <div class="content">
        <div class="content-title">
            {{__('app.payment_receipt')}}
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
                    <td class="summary-info">
                        <table class="info">
                            <tbody>
                                <tr>
                                    <td>{{__('app.number_p')}}</td>
                                    <td>{{$model->number}}</td>
                                </tr>
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
                                <tr>
                                    <td>{{__('app.amount_received')}}</td>
                                    <td>{{formatMoney($model->amount_received)}}</td>
                                </tr>
                                @if($model->amount_refunded > 0)
                                    <tr>
                                        <td>{{__('app.amount_refunded_p')}}</td>
                                        <td>{{formatMoney($model->amount_refunded)}}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection