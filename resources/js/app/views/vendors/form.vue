<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('vendor')}}</div>
		<x-row line>
			<x-form-group col="8" v-model="form.number" :label="$t('number')" disabled></x-form-group>
			<x-form-group col="8" :label="$t('category')" :errors="errors.category_id">
				<x-quick-category slot="quick" :source="form.category"
					:title="$t('category')" url="team-settings/vendor_categories"
					@new="value => { form.category_id = value.id; form.category = value }"
					></x-quick-category>
				<x-tag-input :value="form.category" resource="vendor_categories" column="name" name="name"
				    @input="value => { form.category_id = value.id; form.category = value }">
				</x-tag-input>
			</x-form-group>
			<x-form-group col="8" v-model="form.organization" :label="$t('organization')" :errors="errors.organization" optional></x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" v-model="form.name" :label="$t('name')" :errors="errors.name"></x-form-group>
			<x-form-group col="8" v-model="form.email" :label="$t('email')" :errors="errors.email"></x-form-group>
			<x-form-group col="8" v-model="form.title" :label="$t('title')" :errors="errors.title" optional></x-form-group>
			<x-form-group col="8" v-model="form.department" :label="$t('department')" :errors="errors.department" optional></x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" v-model="form.phone" :label="$t('phone')" :errors="errors.phone" optional></x-form-group>
			<x-form-group col="8" v-model="form.mobile" :label="$t('mobile')" :errors="errors.mobile" optional></x-form-group>
			<x-form-group col="8" v-model="form.fax" :label="$t('fax')" :errors="errors.fax" optional></x-form-group>
			<x-form-group col="8" v-model="form.website" :label="$t('website')" :errors="errors.website" optional></x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" source="textarea" v-model="form.primary_address" :label="$t('primary_address')" :errors="errors.primary_address"></x-form-group>
			<x-form-group col="8" source="textarea" v-model="form.other_address" :label="$t('other_address')" :errors="errors.other_address" optional></x-form-group>
			<x-form-group col="8" :label="$t('currency')" :errors="errors.currency_id" optional>
				<x-tag-input removable :value="form.currency" resource="currencies" column="name" name="name"
				    @input="value => { form.currency_id = value.id; form.currency = value }">
				</x-tag-input>
			</x-form-group>
		</x-row>
	</x-form>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	import { state } from '@js/shared/store'
	export default {
		mixins: [ formable ],
		data() {
			return {
				redirect: 'vendors',
				config: state.team_settings
			}
		}
	}
</script>