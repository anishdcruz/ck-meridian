<template>
	<div>
		<x-form-group :errors="errors.company_name" v-model="form.company_name" :label="$t('wizard_company_name')"></x-form-group>
		<x-form-group source="textarea" :errors="errors.company_address" v-model="form.company_address" :label="$t('wizard_company_address')"></x-form-group>
		<x-form-group :errors="errors.company_email" v-model="form.company_email" :label="$t('wizard_company_email')"></x-form-group>
		<div class="form-group">
			<label class="form-label">
			<span v-if="!form.company_logo">{{$t('wizard_logo_no')}}</span>
			<span v-else>{{$t('wizard_logo')}}</span>
			</label>
			<x-image-upload v-model="form.company_logo"
				url="/api/team-settings/logo"></x-image-upload>
		</div>
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
				url: 'documents',
			}
		}
	}
</script>