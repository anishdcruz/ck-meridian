<template>
	<div class="content-inner">
		<x-panel padding margin>
			<div slot="title">{{$t('contacts')}}</div>
		</x-panel>
  	<mini-crud url="team-settings/contact_categories"
  		title="contact_categories">
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="16">{{$t('name')}}</x-td>
			    <x-td type="th" size="6" colspan="2">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item, edit, remove }">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			    <x-td>
			    	<div>
			    		<x-button @click="edit(item)" size="sm" icon="edit"></x-button>
			    		<x-button @click="remove(item)" type="error" size="sm" icon="trash-b"></x-button>
			    	</div>
			    </x-td>
			</x-tr>
		</mini-crud>
	</div>
</template>
<script>
	import { bar } from '@js/lib/mixins'
	import MiniCrud from '@js/partials/MiniCrud.vue'

	export default {
		mixins: [ bar ],
		components: { MiniCrud },
		data() {
			return {
				redirect: 'team-settings/contact-categories',
				form: {
					fields: []
				},
			}
		},
	}
</script>