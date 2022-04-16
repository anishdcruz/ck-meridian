<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('payment_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('invoice')}}</x-td>
			    <x-td type="th" size="9">{{$t('contact')}}</x-td>
			    <x-td type="th" size="3">{{$t('amount_received')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/payments/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.payment_date | toDate}}</x-td>
			    <x-td>{{item.invoice.number}}</x-td>
			    <x-td>
			    	<span>{{item.contact.name}}</span>
			    	<span v-if="item.contact.organization"> - {{item.contact.organization}}</span>
			    </x-td>
			    <x-td>{{item.amount_received | formatMoney}}</x-td>
			    <x-td>
			    	<div v-if="item.status">
			    		<span :class="`status status-${item.status}`">
			    			<span class="status-text">{{item.status}}</span>
			    		</span>
			    	</div>
			    	<div v-else>-</div>
			    </x-td>
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
				url: 'payments',
				title: 'payments'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'number', 'payment_date', 'amount_received', 'transaction_fees', 'net_amount', 'amount_refunded', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('payment'),
			        filters: [
			            {name: 'number', type: 'lookup', resource: 'payments'},
			            {name: 'payment_date', type: 'datetime'},
			            {name: 'charge_id', type: 'numeric'},
			            {name: 'status', type: 'numeric'},
			            {name: 'method', type: 'static_lookup', options: ['stripe', 'cheque', 'cash', 'bank_transfer']},
			            {name: 'amount_received', type: 'numeric'},
			            {name: 'transaction_fees', type: 'numeric'},
			            {name: 'net_amount', type: 'numeric'},
			            {name: 'amount_refunded', type: 'numeric'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    },{
			        title: this.$t('contact'),
			        filters: [
			            {name: 'contact.number', type: 'lookup', resource: 'contacts', column: 'number'},
			            {name: 'contact.name', type: 'lookup', resource: 'contacts', column: 'name'},
			            {name: 'contact.organization', type: 'lookup', resource: 'contacts'},
			            {name: 'contact.total_revenue', type: 'numeric'},
			            {name: 'contact.created_at', type: 'datetime'},
			        ]
			    },{
			        title: this.$t('invoice'),
			        filters: [
			            {name: 'invoice.number', type: 'lookup', resource: 'invoices', column: 'number'},
			            {name: 'invoice.reference', type: 'string'}
			        ]
			    }]

			    return groups
			}
		}
	}
</script>