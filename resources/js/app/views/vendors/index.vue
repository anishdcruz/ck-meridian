<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/vendors/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('vendor')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="10">{{$t('name')}}</x-td>
			    <x-td type="th" size="8">{{$t('organization')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/vendors/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{item.organization}}</x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			</x-tr>
		</x-filterable>
	</div>
</template>
<script>
	import { indexable } from '@js/lib/mixins'
	import { state } from '@js/shared/store'
	export default {
		mixins: [indexable],
		data() {
			return {
				url: 'vendors',
				title: 'vendors',
				showUploadModal: false,
				config: state.team_settings
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'number', 'name', 'total_revenue',
				  'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('vendor'),
			        filters: [
			            {name: 'number', type: 'lookup', resource: 'vendors'},
			            {name: 'name', type: 'lookup', resource: 'vendors'},
			            {name: 'organization', type: 'lookup', resource: 'vendors'},
			            {name: 'title', type: 'string'},
			            {name: 'department', type: 'string'},
			            {name: 'email', type: 'lookup', resource: 'vendors'},
			            {name: 'website', type: 'lookup', resource: 'vendors'},
			            {name: 'fax', type: 'lookup', resource: 'vendors'},
			            {name: 'phone', type: 'lookup', resource: 'vendors'},
			            {name: 'mobile', type: 'lookup', resource: 'vendors'},
			            {name: 'primary_address', type: 'string'},
			            {name: 'other_address', type: 'string'},
			            {name: 'total_revenue', type: 'numeric'},
			            {name: 'created_at', type: 'datetime'}
			        ]
			    }, {
			    	title: this.$t('category'),
              filters: [
                {name: 'category.name', type: 'lookup', resource: 'vendor_categories', column: 'name'},
              ]
			    },]

			    return groups
			}
		},
		methods: {
			onSaved(e) {
				this.onUploadClose()
				this.$router.push(`/vendors?q=${e.str_rand}`)
			},
			onClose() {
				this.showModal = false
			},
			toggleUploadModal() {
				this.showUploadModal = true
			},
			onUploadClose() {
				this.showUploadModal = false
			}
		}
	}
</script>