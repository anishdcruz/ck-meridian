<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title" :exportable="false">
			<div slot="extra">
				<router-link to="/currencies/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('currency')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="16">{{$t('name')}}</x-td>
			    <x-td type="th" size="3">{{$t('code')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/currencies/${item.id}`)">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{item.code}}</x-td>
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
				url: 'currencies',
				title: 'currencies'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'name', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('currency'),
			        filters: [
			        	{name: 'id', type: 'numeric'},
			            {name: 'name', type: 'string'},
			            {name: 'code', type: 'string'},
			            {name: 'precision', type: 'string'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		}
	}
</script>