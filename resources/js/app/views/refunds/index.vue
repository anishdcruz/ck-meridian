<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('refund_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('payment')}}</x-td>
			    <x-td type="th" size="9">{{$t('contact')}}</x-td>
			    <x-td type="th" size="3">{{$t('amount')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/refunds/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.refund_date | toDate}}</x-td>
			    <x-td>{{item.payment.number}}</x-td>
			    <x-td>
			    	<span>{{item.contact.name}}</span>
			    	<span v-if="item.contact.organization"> - {{item.contact.organization}}</span>
			    </x-td>
			    <x-td>{{item.amount | formatMoney}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.status}`">
			    		<span class="status-text">{{item.status}}</span>
			    	</span>
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
				url: 'refunds',
				title: 'refunds'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'number', 'refund_date', 'refund_id', 'status', 'amount', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('refund'),
			        filters: [
			            {name: 'number', type: 'lookup', resource: 'refunds'},
			            {name: 'refund_date', type: 'datetime'},
			            {name: 'refund_id', type: 'string'},
			            {name: 'amount', type: 'numeric'},
			            {name: 'status', type: 'string'},
			            {name: 'description', type: 'string'},
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
			        title: this.$t('payment'),
			        filters: [
			            {name: 'payment.number', type: 'lookup', resource: 'payments', column: 'number'},
			            {name: 'payment.charge_id', type: 'numeric'},
			        ]
			    }]

			    return groups
			}
		}
	}
</script>