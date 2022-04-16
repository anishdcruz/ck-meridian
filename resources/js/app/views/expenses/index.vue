<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/expenses/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('expense')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('payment_date')}}</x-td>
			    <x-td type="th" size="6">{{$t('category')}}</x-td>
			    <x-td type="th" size="9">{{$t('vendor')}}</x-td>
			    <x-td type="th" size="3">{{$t('grand_total')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/expenses/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.payment_date | toDate}}</x-td>
			    <x-td>{{item.category.name}}</x-td>
			    <x-td>
			    	<span>{{item.vendor.name}}</span>
			    	<span v-if="item.vendor.organization"> - {{item.vendor.organization}}</span>
			    </x-td>
			    <x-td>{{item.grand_total | formatMoney}}</x-td>
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
				url: 'expenses',
				title: 'expenses'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'number', 'payment_date', 'grand_total', 'transaction_fees', 'net_amount', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('payment'),
			        filters: [
			            {name: 'number', type: 'lookup', resource: 'expenses'},
			            {name: 'payment_date', type: 'datetime'},
			            {name: 'method', type: 'static_lookup', options: ['cheque', 'cash', 'bank_transfer']},
			            {name: 'reference', type: 'string'},
			            {name: 'note', type: 'string'},
			            {name: 'sub_total', type: 'numeric'},
			            {name: 'grand_total', type: 'numeric'},
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
			    }, {
			    	title: this.$t('category'),
		            filters: [
		            	{name: 'category.name', type: 'lookup', resource: 'expense_categories', column: 'name'},
		            ]
			    },]

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