<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/recurring-invoices/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('invoice')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="8">{{$t('title')}}</x-td>
			    <x-td type="th" size="3">{{$t('frequency')}}</x-td>
			    <x-td type="th" size="3">{{$t('next_date')}}</x-td>
			    <x-td type="th" size="7">{{$t('contact')}}</x-td>
			    <x-td type="th" size="4">{{$t('grand_total')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/recurring-invoices/${item.id}`)">
			    <x-td>{{item.title}}</x-td>
			    <x-td>{{$t(item.frequency)}}</x-td>
			    <x-td>
			    	<span v-if="item.next_date">{{item.next_date | toDate}}</span>
			    	<span v-else>-</span>
			    </x-td>
			    <x-td>{{item.contact.name}}</x-td>
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
				url: 'recurring-invoices',
				title: 'recurring_invoices'
			}
		},
		computed: {
			sortable() {
				let columns = [
					'title', 'start_date', 'end_date',
					'sub_total', 'discount',
					'grand_total', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('invoice'),
			        filters: [
			            {name: 'title', type: 'string'},
			            {name: 'frequency', type: 'static_lookup', options: ['monthly', 'weekly']},
			            {name: 'start_date', type: 'datetime'},
			            {name: 'end_date', type: 'datetime'},
			            {name: 'due_after', type: 'numeric'},
			            {name: 'never_expiry', type: 'toggle'},
			            {name: 'last_generated_date', type: 'datetime'},
			            {name: 'reference', type: 'string'},
			            {name: 'sub_total', type: 'numeric'},
			            {name: 'discount', type: 'numeric'},
			            {name: 'tax_total', type: 'numeric'},
			            {name: 'tax_2_total', type: 'numeric'},
			            {name: 'grand_total', type: 'numeric'},
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