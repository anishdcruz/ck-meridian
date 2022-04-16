<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('payment_date')}}</x-td>
			    <x-td type="th" size="6">{{$t('purchase_order')}}</x-td>
			    <x-td type="th" size="9">{{$t('vendor')}}</x-td>
			    <x-td type="th" size="3">{{$t('net_amount')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/payments-made/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.payment_date | toDate}}</x-td>
			    <x-td>{{item.purchase_order.number}}</x-td>
			    <x-td>
			    	<span>{{item.vendor.name}}</span>
			    	<span v-if="item.vendor.organization"> - {{item.vendor.organization}}</span>
			    </x-td>
			    <x-td>{{item.net_amount | formatMoney}}</x-td>
			</x-tr>
		</x-filterable>
	</div>
</template>
<script>
	import { indexable } from '@js/lib/mixins'
	export default {
		mixins: [indexable],
		data() {
			return {
				url: 'payments-made',
				title: 'payments_made'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'number', 'payment_date', 'amount_paid', 'transaction_fees', 'net_amount', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('payment'),
			        filters: [
			            {name: 'number', type: 'lookup', resource: 'payments-made'},
			            {name: 'payment_date', type: 'datetime'},
			            {name: 'status', type: 'numeric'},
			            {name: 'amount_paid', type: 'numeric'},
			            {name: 'transaction_fees', type: 'numeric'},
			            {name: 'net_amount', type: 'numeric'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    },{
			        title: this.$t('vendor'),
			        filters: [
			            {name: 'vendor.number', type: 'lookup', resource: 'vendors', column: 'number'},
			            {name: 'vendor.name', type: 'lookup', resource: 'vendors', column: 'name'},
			            {name: 'vendor.organization', type: 'lookup', resource: 'vendors'},
			            {name: 'vendor.total_paid', type: 'numeric'},
			            {name: 'vendor.created_at', type: 'datetime'},
			        ]
			    },{
			        title: this.$t('purchase_order'),
			        filters: [
			            {name: 'purchase_order.number', type: 'lookup', resource: 'purchase_orders'},
			            {name: 'purchase_order.reference', type: 'string'}
			        ]
			    }]

			    return groups
			}
		}
	}
</script>