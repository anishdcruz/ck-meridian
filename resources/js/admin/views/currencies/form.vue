<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('currency')}}</div>
		<x-row line>
			<x-form-group col="12" v-model="form.name" :label="$t('name')" :errors="errors.name"></x-form-group>
			<x-form-group col="12" v-model="form.code" :label="$t('code')" :errors="errors.code"></x-form-group>
			<x-form-group col="12" v-model="form.precision" :label="$t('precision')" :errors="errors.precision"></x-form-group>
			<x-form-group col="12" v-model="form.subunit" :label="$t('subunit')" :errors="errors.subunit"></x-form-group>
			<x-form-group col="12" v-model="form.symbol" :label="$t('symbol')" :errors="errors.symbol"></x-form-group>
			<x-form-group col="12" v-model="form.symbol_first" :label="$t('symbol_first')" :errors="errors.symbol_first"></x-form-group>
			<x-form-group col="12" v-model="form.decimal_mark" :label="$t('decimal_mark')" :errors="errors.decimal_mark"></x-form-group>
			<x-form-group col="12" v-model="form.thousands_separator" :label="$t('thousands_separator')" :errors="errors.thousands_separator"></x-form-group>
		</x-row>
	</x-form>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	export default {
		mixins: [ formable ],
		data() {
			return {
				redirect: 'currencies'
			}
		}
	}
</script>