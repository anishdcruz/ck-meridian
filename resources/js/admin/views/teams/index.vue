<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title" :exportable="false">
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="9">{{$t('name')}}</x-td>
			    <x-td type="th" size="10">{{$t('owner')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/teams/${item.id}`)">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{item.owner.name}}</x-td>
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
				url: 'teams',
				title: 'teams'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'name', 'email', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('team'),
			        filters: [
			        	{name: 'id', type: 'numeric'},
			            {name: 'name', type: 'string'},
			            {name: 'created_at', type: 'datetime'},
			           	{name: 'timezone', type: 'string'},
			           	{name: 'date_format', type: 'string'},
			           	{name: 'currency_id', type: 'string'},
			           	{name: 'lang_id', type: 'string'}
			        ]
			    },{
			        title: this.$t('owner'),
			        filters: [
			        	{name: 'owner.id', type: 'numeric'},
			            {name: 'owner.name', type: 'lookup', resource: 'users', column: 'name'},
			            {name: 'owner.email', type: 'lookup', resource: 'users', column: 'email'},
			            {name: 'owner.trial_ends_at', type: 'datetime'},
			            {name: 'owner.created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		}
	}
</script>