<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title" :exportable="false">
			<x-tr slot="heading">
			    <x-td type="th" size="8">{{$t('name')}}</x-td>
			    <x-td type="th" size="3">{{$t('frequency')}}</x-td>
			    <x-td type="th" size="7">{{$t('email_to')}}</x-td>
			    <x-td type="th" size="4">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/recurring-exports/${item.id}`)">
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{$t(item.frequency)}}</x-td>
			    <x-td>{{item.email_to}}</x-td>
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
				url: 'recurring-exports',
				title: 'recurring_exports'
			}
		},
		computed: {
			sortable() {
				let columns = [
					'title', 'start_date', 'end_date',
					'sub_total', 'discount', 'tax_total', 'tax_2_total',
				  'grand_total', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('recurring_exports'),
			        filters: [
			            {name: 'name', type: 'string'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		}
	}
</script>