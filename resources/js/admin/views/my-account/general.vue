<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('contact_information')}}</div>
			<x-row line>
				<x-col span="12" offset="8">
					<x-form-group :errors="errors.name" v-model="form.name" :label="$t('name')"></x-form-group>
					<x-form-group :errors="errors.email" v-model="form.email" :label="$t('email')" disabled></x-form-group>
				</x-col>
			</x-row>
			<div slot="footer" class="flex flex-end">
			<div>
				<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
			</div>
		</div>
		</x-panel>
	</div>
</template>
<script>
	import { settings } from '@js/lib/mixins'
	export default {
		mixins: [ settings ],
		data() {
			return {
				form: {
				},
				options: {
					timezones: []
				}
			}
		},
		methods: {
			setData(res) {
				this.$set(this.$data, 'form', res.data.form)
				this.$set(this.$data, 'options', res.data.options)
				this.$bar.finish()
				this.show = true
			},
			save() {
				this.isSaving = true
				this.errors = {}

				const { url, method } = this.getForm()

				this.$http[method](url, this.form)
					.then((res) => {
						this.$message.success(this.$t('saved_success'))
						window.location.replace(`/my-account`);
					})
					.catch(this.catch)
					.finally(() => {
						this.isSaving = false
					})
			},
		}
	}
</script>