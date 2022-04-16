@extends('app.pdf.master', $team)

@section('content')
    <div class="content">
        <div class="content-title">
            {{__('app.quotation')}}
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
                                    <td>{{__('app.number_p')}}</td>
                                    <td>{{$model->number}}</td>
                                </tr>
                                <tr>
                                    <td>{{__('app.issue_date_p')}}</td>
                                    <td>{{formatDate($model->issue_date)}}</td>
                                </tr>
                                <tr>
                                    <td>{{__('app.expiry_date_p')}}</td>
                                    <td>{{formatDate($model->expiry_date)}}</td>
                                </tr>
                                @if($model->reference)
                                	<tr>
                                	    <td>{{__('app.reference_p')}}</td>
                                	    <td>{{$model->reference}}</td>
                                	</tr>
                                @endif
                                <tr>
                                    <td>{{__('app.grand_total_p')}}</td>
                                    <td>{{formatMoney($model->grand_total)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="items">
            <thead>
                <tr>
                    <th class="w-4">{{__('app.code')}}</th>
                    <th class="w-8">{{__('app.description')}}</th>
                    <th class="w-2">{{__('app.uom')}}</th>
                    <th class="w-4">{{__('app.unit_price')}}</th>
                    <th class="w-2">{{__('app.qty')}}</th>
                    <th class="w-4">{{__('app.total')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($model->lines as $line)
                    <tr>
                        <td class="align-left">{{$line->item->code}}</td>
                        <td class="align-left"><pre>{{$line->item->description}}</pre></td>
                        <td class="align-left">{{$line->item->uom->name}}</td>
                        <td class="align-right">{{formatMoney($line->unit_price, false)}}</td>
                        <td class="align-center">{{$line->qty}}</td>
                        <td class="align-right">{{formatMoney($line->total, false)}}</td>
                    </tr>
                @endforeach
                @include('app.pdf.blank_items', ['td' => 6, 'lines' => $model->lines])
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td align="right" colspan="2">{{__('app.sub_total')}}</td>
                    <td align="right">{{formatMoney($model->sub_total, false)}}</td>
                </tr>
                @if($team['enable_discount'] && $model->discount > 0)
                    <tr>
                        <td colspan="3"></td>
                        <td align="right" colspan="2">{{__('app.discount')}}</td>
                        <td align="right">-{{formatMoney($model->discount, false)}}</td>
                    </tr>
                @endif
                @if($team['tax_enable'])
                    <tr>
                        <td colspan="3"></td>
                        <td align="right" colspan="2">{{$team['tax_label']}} @ {{$model->tax}} %</td>
                        <td align="right">{{formatMoney($model->tax_total, false)}}</td>
                    </tr>
                    @if($team['tax_2_enable'])
                        <tr>
                            <td colspan="3"></td>
                            <td align="right" colspan="2">{{$team['tax_2_label']}} @ {{$model->tax_2}} %</td>
                            <td align="right">{{formatMoney($model->tax_2_total, false)}}</td>
                        </tr>
                    @endif
                @endif
                <tr>
                    <td colspan="3"></td>
                    <td align="right" colspan="2">
                        <strong>{{__('app.grand_total')}}</strong>
                    </td>
                    <td align="right">
                        <strong>{{formatMoney($model->grand_total)}}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
        <table class="terms">
            <tbody>
                <tr>
                    <td class="terms-description">
                        <div>
                            <strong>{{__('app.terms_and_conditions')}}</strong>
                            <pre>{{$model->term->description}}</pre>
                        </div>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection