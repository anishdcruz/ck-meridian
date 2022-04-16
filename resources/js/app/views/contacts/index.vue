<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/contacts/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('contact')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="10">{{$t('name')}}</x-td>
			    <x-td type="th" size="8">{{$t('organization')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/contacts/${item.id}`)">
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
				url: 'contacts',
				title: 'contacts',
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
			        title: this.$t('contact'),
			        filters: [
			            {name: 'number', type: 'lookup', resource: 'contacts'},
			            {name: 'name', type: 'lookup', resource: 'contacts'},
			            {name: 'organization', type: 'lookup', resource: 'contacts'},
			            {name: 'title', type: 'string'},
			            {name: 'department', type: 'string'},
			            {name: 'email', type: 'lookup', resource: 'contacts'},
			            {name: 'website', type: 'lookup', resource: 'contacts'},
			            {name: 'fax', type: 'lookup', resource: 'contacts'},
			            {name: 'phone', type: 'lookup', resource: 'contacts'},
			            {name: 'mobile', type: 'lookup', resource: 'contacts'},
			            {name: 'primary_address', type: 'string'},
			            {name: 'other_address', type: 'string'},
			            {name: 'total_revenue', type: 'numeric'},
			            {name: 'created_at', type: 'datetime'}
			        ]
			    }, {
			    	title: this.$t('category'),
              filters: [
                {name: 'category.name', type: 'lookup', resource: 'contact_categories', column: 'name'},
              ]
			    },]

			     if(Number(this.config.tax_enable)) {
              groups.push({
                  title: this.$t('tax'),
                  filters: [
                      {name: 'tax_id', title: this.config.registration_label, type: 'lookup', resource: 'contacts'}
                  ]
              })
            }

			    return groups
			}
		},
		methods: {
			onSaved(e) {
				this.onUploadClose()
				this.$router.push(`/contacts?q=${e.str_rand}`)
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