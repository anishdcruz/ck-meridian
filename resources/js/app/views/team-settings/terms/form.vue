<template>
	<div class="content-inner" v-if="show">
		<x-panel margin padding>
			<div slot="title">{{$t(mode)}} {{$t('term')}}</div>
			<x-row line>
				<x-form-group col="8" v-model="form.label" :label="$t('label')" :errors="errors.label"></x-form-group>
				<x-form-group source="textarea" col="16" v-model="form.description" :label="$t('description')" :errors="errors.description"></x-form-group>
			</x-row>
			<div slot="footer" class="flex flex-end">
				<div>
					<x-button @click="cancel" :disabled="isSaving">{{$t('cancel')}}</x-button>
					<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
				</div>
			</div>
		</x-panel>
	</div>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	export default {
		mixins: [ formable ],
		data() {
			return {
				form: {},
				redirect: 'team-settings/terms'
			}
		}
	}
</script>