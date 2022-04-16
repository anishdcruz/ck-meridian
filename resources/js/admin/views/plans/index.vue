<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title" :exportable="false">
			<div slot="extra">
				<router-link to="/plans/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('plan')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="5">{{$t('name')}}</x-td>
			    <x-td type="th" size="5">{{$t('slug')}}</x-td>
			    <x-td type="th" size="5">{{$t('gateway_id')}}</x-td>
			    <x-td type="th" size="4">{{$t('price')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/plans/${item.id}`)">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{item.slug}}</x-td>
			    <x-td>{{item.gateway_id}}</x-td>
			    <x-td>{{item.price}}</x-td>
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
				url: 'plans',
				title: 'plans'
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
			        title: this.$t('plan'),
			        filters: [
			        	{name: 'id', type: 'numeric'},
			            {name: 'name', type: 'lookup', resource: 'plans'},
			            {name: 'gateway_id', type: 'lookup', resource: 'plans'},
			            {name: 'price', type: 'numeric'},
			            {name: 'active', type: 'toggle'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		}
	}
</script>