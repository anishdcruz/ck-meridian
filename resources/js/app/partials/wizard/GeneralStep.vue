<template>
	<div>
		<x-form-group :label="$t('wizard_timezone')" :errors="errors.timezone">
			<select class="form-input" v-model="form.timezone">
				<option v-for="(value, key) in options.timezones"
					:value="key">
					{{value}}
				</option>
			</select>
		</x-form-group>
		<x-form-group :errors="errors.date_format" :label="$t('wizard_date_format')">
			<select class="form-input" v-model="form.date_format">
				<option v-for="option in options.date_formats" :value="option">
					{{option}}
				</option>
			</select>
		</x-form-group>
		<x-form-group :label="$t('wizard_currency')" :errors="errors.currency_id">
			<select class="form-input" v-model="form.currency_id">
				<option v-for="c in options.currencies"
					:value="c.code">
					{{c.name}}
				</option>
			</select>
		</x-form-group>
		<x-form-group :label="$t('wizard_lang')" :errors="errors.lang_id">
			<select class="form-input" v-model="form.lang_id">
				<option v-for="(value, key) in options.langs"
					:value="key">
					{{value}}
				</option>
			</select>
		</x-form-group>
		<x-form-group source="switch" :errors="errors.enable_discount" v-model="form.enable_discount" :label="$t('wizard_discount')"></x-form-group>
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
				url: 'general',
			}
		}
	}
</script>