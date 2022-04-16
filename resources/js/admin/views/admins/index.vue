<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title" :exportable="false">
			<div slot="extra">
				<router-link to="/admins/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('admin')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="9">{{$t('name')}}</x-td>
			    <x-td type="th" size="10">{{$t('email')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/admins/${item.id}`)">
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
				url: 'admins',
				title: 'admins'
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
			        title: this.$t('admin'),
			        filters: [
			        	{name: 'id', type: 'numeric'},
			            {name: 'name', type: 'string'},
			            {name: 'email', type: 'lookup', resource: 'admins'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		}
	}
</script>