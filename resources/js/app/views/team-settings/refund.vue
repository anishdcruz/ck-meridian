<template>
	<div class="content-inner">
		<x-panel padding margin>
			<div slot="title">{{$t('refunds')}}</div>
			<x-row line>
				<x-form-group col="24" :label="$t('refund_email_subject')"
					:errors="errors.refund_email_subject"
					v-model="form.refund_email_subject">
					</x-form-group>
				<x-form-group col="24" :errors="errors.refund_email_template" :label="$t('refund_email_template')">
				  	<quill-editor v-model="form.refund_email_template"></quill-editor>
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
				redirect: 'team-settings/refunds',
				form: {
					fields: []
				},
			}
		},
	}
</script>