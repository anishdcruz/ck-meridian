<template>
	<div class="content-inner">
		<x-filterable :exportable="false" :saved-filters="false"
			:url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/team-settings/terms/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('term')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="4">{{$t('label')}}</x-td>
			    <x-td type="th" size="16">{{$t('description')}}</x-td>
			    <x-td type="th" size="4">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/team-settings/terms/${item.id}`)">
			    <x-td>{{item.label}}</x-td>
			    <x-td>{{item.description}}</x-td>
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
				url: 'team-settings/terms',
				title: 'terms'
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
			        title: this.$t('item'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'label', type: 'string'},
			            {name: 'description', type: 'string'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		}
	}
</script>