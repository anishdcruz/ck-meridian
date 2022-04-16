<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('item')}}</div>
		<x-row line>
			<x-form-group col="8" v-model="form.code" :label="$t('code')" disabled></x-form-group>
			<x-form-group col="8" :label="$t('category')" :errors="errors.category_id">
				<x-quick-category slot="quick" :source="form.category"
					:title="$t('category')" url="team-settings/item_categories"
					@new="value => { form.category_id = value.id; form.category = value }"
					></x-quick-category>
				<x-tag-input :value="form.category" resource="item_categories" column="name" name="name"
				    @input="value => { form.category_id = value.id; form.category = value }">
				</x-tag-input>
			</x-form-group>
			<x-form-group col="8" :label="$t('uom')" :errors="errors.uom_id">
				<x-quick-category slot="quick" :source="form.uom"
					:title="$t('uom')" url="team-settings/item_uoms"
					@new="value => { form.uom_id = value.id; form.uom = value }"
					></x-quick-category>
				<x-tag-input :value="form.uom" resource="item_uoms" column="name" name="name"
				    @input="value => { form.uom_id = value.id; form.uom = value }">
				</x-tag-input>
			</x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" source="textarea" v-model="form.description" :label="$t('description')" :errors="errors.description"></x-form-group>
			<x-form-group col="8" v-model="form.unit_price" :label="$t('unit_price')" :errors="errors.unit_price"></x-form-group>
		</x-row>
	</x-form>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	export default {
		mixins: [ formable ],
		data() {
			return {
				redirect: 'items'
			}
		}
	}
</script>