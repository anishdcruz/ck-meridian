<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('upload_logo')}}</div>
			<x-row line>
				<x-col span="12" offset="8">
					<br><br>
					<x-image-upload v-model="form.company_logo" url="/api/team-settings/logo"></x-image-upload>
					<br><br>
				</x-col>
			</x-row>
		</x-panel>
		<x-panel padding margin>
			<div slot="title">{{$t('company_information')}}</div>
			<x-row line>
				<x-col span="12" offset="8">
					<x-form-group :errors="errors.company_name" v-model="form.company_name" :label="$t('company_name')"></x-form-group>
					<x-form-group source="textarea" :errors="errors.company_address" v-model="form.company_address" :label="$t('company_address')"></x-form-group>
					<x-form-group :errors="errors.company_email" v-model="form.company_email" :label="$t('company_email')"></x-form-group>
					<x-form-group optional :errors="errors.company_telephone" v-model="form.company_telephone" :label="$t('company_telephone')"></x-form-group>
					<x-form-group optional :errors="errors.company_fax" v-model="form.company_fax" :label="$t('company_fax')"></x-form-group>
				</x-col>
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
	import { state } from '@js/shared/store'
	export default {
		mixins: [ settings ],
		data() {
			return {
				form_logo: null,
				form: {
				},
				options: {
					timezones: []
				},
				config: state.team_settings
			}
		},
		methods: {
			setData(res) {
				this.$set(this.$data, 'form', res.data.form)
				this.$set(this.$data, 'options', res.data.options)
				this.$bar.finish()
				this.show = true
			},
			save() {
				this.isSaving = true
				this.errors = {}

				const { url, method } = this.getForm()

				this.$http[method](url, this.form)
					.then((res) => {
						this.$message.success(this.$t('saved_success'))
					})
					.catch(this.catch)
					.finally(() => {
						this.isSaving = false
					})
			},
		}
	}
</script>