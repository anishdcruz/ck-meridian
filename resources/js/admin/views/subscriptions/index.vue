<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title" :exportable="false">
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="5">{{$t('user')}}</x-td>
			    <x-td type="th" size="5">{{$t('stripe_id')}}</x-td>
			    <x-td type="th" size="5">{{$t('stripe_plan')}}</x-td>
			    <x-td type="th" size="4">{{$t('ends_at')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/subscriptions/${item.id}`)">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.user.name}}</x-td>
			    <x-td>{{item.stripe_id}}</x-td>
			    <x-td>{{item.stripe_plan}}</x-td>
			    <x-td>{{item.ends_at || '-'}}</x-td>
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
				url: 'subscriptions',
				title: 'subscriptions'
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
			        title: this.$t('subscription'),
			        filters: [
			        	{name: 'id', type: 'numeric'},
			            {name: 'name', type: 'string'},
			            {name: 'stripe_id', type: 'string'},
			            {name: 'stripe_plan', type: 'string'},
			            {name: 'quantity', type: 'numeric'},
			            {name: 'trial_ends_at', type: 'datetime'},
			            {name: 'ends_at', type: 'datetime'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		}
	}
</script>