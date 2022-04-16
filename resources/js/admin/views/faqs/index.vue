<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title" :exportable="false">
			<div slot="extra">
				<router-link to="/faqs/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('faq')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="16">{{$t('question')}}</x-td>
			    <x-td type="th" size="3">{{$t('show_on_pricing')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/faqs/${item.id}`)">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.question | trim(70)}}</x-td>
			    <x-td>{{item.show_on_pricing ? $t('yes') : $t('no')}}</x-td>
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
				url: 'faqs',
				title: 'faqs'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'show_on_pricing', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('faq'),
			        filters: [
			        	{name: 'id', type: 'numeric'},
			            {name: 'question', type: 'string'},
			            {name: 'answer', type: 'string'},
			            {name: 'show_on_pricing', type: 'toggle'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		}
	}
</script>