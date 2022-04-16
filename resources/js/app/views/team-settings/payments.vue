<template>
	<div class="content-inner">
		<x-panel padding margin>
			<div slot="title">{{$t('payments')}}</div>
			<x-row line>
				<x-col span="12">
					<x-form-group :errors="errors.payment_notification_email" v-model="form.payment_notification_email" :label="$t('payment_notification_email')"></x-form-group>
				</x-col>
			</x-row>
			<x-row line>
				<x-form-group col="24" :label="$t('payment_email_subject')"
					:errors="errors.payment_email_subject"
					v-model="form.payment_email_subject">
					</x-form-group>
				<x-form-group col="24" :errors="errors.payment_email_template" :label="$t('payment_email_template')">
				  	<quill-editor v-model="form.payment_email_template"></quill-editor>
				</x-form-group>
			</x-row>
			<div slot="footer" class="flex flex-end">
				<div>
					<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
				</div>
			</div>
		</x-panel>
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
				redirect: 'team-settings/payments',
				form: {
					fields: []
				},
			}
		},
	}
</script>