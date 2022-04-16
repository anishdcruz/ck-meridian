<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('admin')}}</div>
		<x-row line>
			<x-form-group col="8" v-model="form.name" :label="$t('name')" :errors="errors.name"></x-form-group>
			<x-form-group col="16" v-model="form.email" :label="$t('email')" :errors="errors.email"></x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" type="password" v-model="form.password" :label="$t('password')" :errors="errors.password"></x-form-group>
			<x-form-group col="8" type="password" v-model="form.password_confirmation" :label="$t('password_confirmation')" :errors="errors.password_confirmation"></x-form-group>
		</x-row>
	</x-form>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	export default {
		mixins: [ formable ],
		data() {
			return {
				redirect: 'admins'
			}
		}
	}
</script>