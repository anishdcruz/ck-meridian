<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/quotations/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('quotation')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('issue_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('expiry_date')}}</x-td>
			    <x-td type="th" size="9">{{$t('contact')}}</x-td>
			    <x-td type="th" size="3">{{$t('grand_total')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/quotations/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.issue_date | toDate}}</x-td>
			    <x-td>
			    	<span v-if="item.expiry_date">{{item.expiry_date | toDate}}</span>
			    	<span v-else>-</span>
			    </x-td>
			    <x-td>
			    	<span>{{item.contact.name}}</span>
			    	<span v-if="item.contact.organization"> - {{item.contact.organization}}</span>
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
				url: 'quotations',
				title: 'quotations'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'number', 'issue_date', 'expiry_date',
				  'sub_total', 'discount',
				  'grand_total', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('quotation'),
			        filters: [
			            {name: 'number', type: 'lookup', resource: 'quotations'},
			            {name: 'issue_date', type: 'datetime'},
			            {name: 'expiry_date', type: 'datetime'},
			            {name: 'reference', type: 'string'},
			            {name: 'sub_total', type: 'numeric'},
			            {name: 'discount', type: 'numeric'},
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
			    },{
			    	title: this.$t('status'),
		            filters: [
		            	{name: 'status.name', type: 'lookup', resource: 'quotation_statuses', column: 'name'}
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