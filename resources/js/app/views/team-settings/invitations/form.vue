<template>
	<div class="content-inner" v-if="show">
		<x-panel margin padding>
			<div slot="title">{{$t(mode)}} {{$t('role')}}</div>
			<x-row line>
				<x-form-group col="8" v-model="form.email" :label="$t('email')" :errors="errors.email"></x-form-group>
				<x-form-group col="8" :label="$t('role')" :errors="errors.role_id">
					<x-tag-input :value="form.role" base="/api/team-settings/search" resource="roles" column="name" name="name"
					    @input="value => { form.role_id = value.id; form.role = value }">
					</x-tag-input>
				</x-form-group>
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
				redirect: 'team-settings/invitations'
			}
		}
	}
</script>