<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/purchase-orders/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('purchase_order')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('issue_date')}}</x-td>
			    <x-td type="th" size="12">{{$t('vendor')}}</x-td>
			    <x-td type="th" size="3">{{$t('grand_total')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/purchase-orders/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.issue_date | toDate}}</x-td>
			    <x-td>
			    	<span>{{item.vendor.name}}</span>
			    	<span v-if="item.vendor.organization"> - {{item.vendor.organization}}</span>
			    </x-td>
			    <x-td>{{item.grand_total | formatMoney}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.status.color}`">
			    		<span class="status-text">{{item.status.name}}</span>
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
				url: 'purchase-orders',
				title: 'purchase_orders'
			}
		},
		computed: {
			sortable() {
				let columns = [
					'number', 'issue_date', 'due_date',
				  'sub_total',
				  'grand_total', 'created_at',
				  'amount_paid', 'paid_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('purchase_order'),
			        filters: [
			            {name: 'number', type: 'lookup', resource: 'purchase-orders'},
			            {name: 'issue_date', type: 'datetime'},
			            {name: 'reference', type: 'string'},
			            {name: 'sub_total', type: 'numeric'},
			            {name: 'grand_total', type: 'numeric'},
			            {name: 'created_at', type: 'datetime'},
			            {name: 'paid_at', type: 'datetime'},
			            {name: 'amount_paid', type: 'numeric'},
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
			    	title: this.$t('status'),
              filters: [
                {name: 'status.name', type: 'lookup', resource: 'purchase_order_statuses', column: 'name'}
              ]
			    }]

			     if(Number(this.config.tax_enable)) {
	    	    	let taxes = [
	        			{name: 'tax', title: `${this.config.tax_label} %`, type: 'numeric'},
	        			{name: 'tax_total', title: `${this.config.tax_label} ${this.$t('total')}`, type: 'numeric'},
	        		]

	        		if(Number(this.config.tax_2_enable)) {
	        			taxes.push({name: 'tax_2', title: `${this.config.tax_2_label} %`, type: 'numeric'})
	    	    		taxes.push({name: 'tax_2_total', title: `${this.config.tax_2_label} ${this.$t('total')}`, type: 'numeric'})
	        		}

	    	    	groups.push({
	    	    		title: this.$t('tax'),
	    	    		filters: taxes
	    	    	})
	    	    }

			    return groups
			}
		}
	}
</script>