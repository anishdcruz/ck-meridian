<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('taxes')}}</div>
			<x-row line>
				<x-col span="12" offset="8">
					<x-form-group source="switch" :errors="errors.tax_enable" v-model="form.tax_enable" :label="$t('tax_enable')"></x-form-group>

					<x-form-group :disabled="!tax1" :errors="errors.tax_label" v-model="form.tax_label" :label="$t('tax_label')"></x-form-group>

					<x-form-group :disabled="!tax1" :errors="errors.tax_rate" v-model="form.tax_rate" :label="$t('tax_rate')"></x-form-group>

					<hr>
					<x-form-group :disabled="!tax1" source="switch" :errors="errors.tax_2_enable" v-model="form.tax_2_enable" :label="$t('tax_2_enable')"></x-form-group>

					<x-form-group :disabled="!tax1 || !tax2" :errors="errors.tax_2_label" v-model="form.tax_2_label" :label="$t('tax_2_label')"></x-form-group>

					<x-form-group :disabled="!tax1 || !tax2" :errors="errors.tax_2_rate" v-model="form.tax_2_rate" :label="$t('tax_2_rate')"></x-form-group>

					<hr>
					<x-form-group :disabled="!tax1" :errors="errors.registration_label" v-model="form.registration_label" :label="$t('registration_label')"></x-form-group>
					<x-form-group :disabled="!tax1"
						:errors="errors.company_tax_id" v-model="form.company_tax_id"
						:label="`${$t('company')} ${form.registration_label}`"></x-form-group>
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
	export default {
		mixins: [ settings ],
		data() {
			return {
				form: {
				},
				options: {
					timezones: []
				}
			}
		},
		computed: {
			tax1() {
				return Number(this.form.tax_enable)
			},
			tax2() {
				return Number(this.form.tax_2_enable)
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
						window.location.replace(`/team-settings/taxes`);
					})
					.catch(this.catch)
					.finally(() => {
						this.isSaving = false
					})
			},
		}
	}
</script>