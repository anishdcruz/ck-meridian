<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title" :exportable="false">
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="5">{{$t('name')}}</x-td>
			    <x-td type="th" size="5">{{$t('email')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/users/${item.id}`)">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{item.email}}</x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
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
				url: 'users',
				title: 'users'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'name', 'price', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('user'),
			        filters: [
			        	{name: 'id', type: 'numeric'},
			            {name: 'name', type: 'lookup', resource: 'users'},
			            {name: 'email', type: 'lookup', resource: 'users'},
			            {name: 'stripe_id', type: 'string'},
			            {name: 'card_brand', type: 'string'},
			            {name: 'card_last_four', type: 'string'},
			            {name: 'trial_ends_at', type: 'datetime'},
			            {name: 'extra_billing_info', type: 'string'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    },{
			        title: this.$t('subscription'),
			        filters: [
			        	{name: 'fsubscription.id', type: 'numeric'},
			            {name: 'fsubscription.name', type: 'string'},
			            {name: 'fsubscription.stripe_id', type: 'string'},
			            {name: 'fsubscription.stripe_plan', type: 'string'},
			            {name: 'fsubscription.quantity', type: 'numeric'},
			            {name: 'fsubscription.trial_ends_at', type: 'datetime'},
			            {name: 'fsubscription.ends_at', type: 'datetime'},
			            {name: 'fsubscription.created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		}
	}
</script>