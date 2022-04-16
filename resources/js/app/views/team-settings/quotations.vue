<template>
	<div class="content-inner">
		<x-panel padding margin>
			<div slot="title">{{$t('quotations')}}</div>
			<x-row line>
				<x-form-group col="8" :label="$t('quotation_status_on_create_id')" :errors="errors.quotation_status_on_create_id">
					<x-tag-input :value="form.quotation_status_on_create" resource="quotation_statuses" column="name" name="name"
					    @input="value => { form.quotation_status_on_create_id = value.id; form.quotation_status_on_create = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" :label="$t('quotation_status_on_sent_id')" :errors="errors.quotation_status_on_sent_id">
					<x-tag-input :value="form.quotation_status_on_sent" resource="quotation_statuses" column="name" name="name"
					    @input="value => { form.quotation_status_on_sent_id = value.id; form.quotation_status_on_sent = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" :label="$t('quotation_status_on_invoiced_id')" :errors="errors.quotation_status_on_invoiced_id">
					<x-tag-input :value="form.quotation_status_on_invoiced" resource="quotation_statuses" column="name" name="name"
					    @input="value => { form.quotation_status_on_invoiced_id = value.id; form.quotation_status_on_invoiced = value }">
					</x-tag-input>
				</x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="24" :label="$t('quotation_email_subject')"
					:errors="errors.quotation_email_subject"
					v-model="form.quotation_email_subject">
					</x-form-group>
				<x-form-group col="24" :errors="errors.quotation_email_template" :label="$t('quotation_email_template')">
				  	<quill-editor v-model="form.quotation_email_template"></quill-editor>
				</x-form-group>
			</x-row>
			<div slot="footer" class="flex flex-end">
				<div>
					<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
				</div>
			</div>
		</x-panel>
		<mini-status-crud url="team-settings/quotation_statuses"
			title="quotation_statuses">
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="8">{{$t('name')}}</x-td>
			    <x-td type="th" size="4">{{$t('color')}}</x-td>
			    <x-td type="th" size="4">{{$t('locked')}}</x-td>
			    <x-td type="th" size="6" colspan="2">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item, edit, remove }">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.color}`">
			    		<span class="status-text">{{item.color}}</span>
			    	</span>
			    </x-td>
			    <x-td>{{item.locked ? $t('yes') : $t('no')}}</x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			    <x-td>
			    	<div>
			    		<x-button @click="edit(item)" size="sm" icon="edit"></x-button>
			    		<x-button @click="remove(item)" type="error" size="sm" icon="trash-b"></x-button>
			    	</div>
			    </x-td>
			</x-tr>
		</mini-status-crud>
	</div>
</template>
<script>
	import { settings } from '@js/lib/mixins'

	import MiniCrud from '@js/partials/MiniCrud.vue'
	import MiniStatusCrud from '@js/partials/MiniStatusCrud.vue'

	export default {
		mixins: [ settings ],
		components: { MiniCrud, MiniStatusCrud },
		data() {
			return {
				redirect: 'team-settings/quotations',
				form: {
					fields: []
				},
			}
		},
	}
</script>