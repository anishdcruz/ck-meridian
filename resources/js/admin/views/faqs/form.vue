<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('faq')}}</div>
		<x-row line>
			<x-form-group col="12" source="textarea" v-model="form.question" :label="$t('question')" :errors="errors.question"></x-form-group>
			<x-form-group col="12" source="textarea" v-model="form.answer" :label="$t('answer')" :errors="errors.answer"></x-form-group>
			<x-form-group source="switch" col="8" v-model="form.show_on_pricing" :label="$t('show_on_pricing')" :errors="errors.show_on_pricing"></x-form-group>
		</x-row>
	</x-form>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	export default {
		mixins: [ formable ],
		data() {
			return {
				redirect: 'faqs'
			}
		}
	}
</script>