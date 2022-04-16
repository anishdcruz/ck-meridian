<template>
	<div>
		<x-form-group source="switch" :errors="errors.tax_enable" v-model="form.tax_enable" :label="$t('wizard_tax_enable')"></x-form-group>

		<x-form-group :disabled="!tax1" :errors="errors.tax_label" v-model="form.tax_label" :label="$t('wizard_tax_label')"></x-form-group>

		<x-form-group :disabled="!tax1" :errors="errors.tax_rate" v-model="form.tax_rate" :label="$t('wizard_tax_rate')"></x-form-group>
		<hr>
		<x-form-group :disabled="!tax1" source="switch" :errors="errors.tax_2_enable" v-model="form.tax_2_enable" :label="$t('wizard_tax_2_enable')"></x-form-group>

		<x-form-group :disabled="!tax1 || !tax2" :errors="errors.tax_2_label" v-model="form.tax_2_label" :label="$t('wizard_tax_2_label')"></x-form-group>

		<x-form-group :disabled="!tax1 || !tax2" :errors="errors.tax_2_rate" v-model="form.tax_2_rate" :label="$t('wizard_tax_2_rate')"></x-form-group>
		<hr>
		<x-form-group :disabled="!tax1" :errors="errors.registration_label" v-model="form.registration_label" :label="$t('wizard_registration_label')"></x-form-group>
		<x-form-group :disabled="!tax1"
			:errors="errors.company_tax_id" v-model="form.company_tax_id"
			:label="`${$t('wizard_company')} ${form.registration_label} ?`"></x-form-group>
		<hr>
		<div class="wizard-footer">
			<x-button type="success" :loading="isSaving" @click="save">{{$t('save_and_continue')}}</x-button>
		</div>
	</div>
</template>
<script>
	import { wizard } from '@js/lib/mixins'
	import http from '@js/lib/Http'
	export default {
		mixins: [ wizard ],
		data() {
			return {
				url: 'taxes?set=completed',
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
	}
</script>