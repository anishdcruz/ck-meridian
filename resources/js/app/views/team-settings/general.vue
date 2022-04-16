<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('general')}}</div>
			<x-row line>
				<x-col span="12" offset="8">
					<x-form-group :errors="errors.name" v-model="form.name" :label="$t('name')"></x-form-group>
					<hr>
					<x-form-group :label="$t('timezone')" :errors="errors.timezone">
						<select class="form-input" v-model="form.timezone">
							<option v-for="(value, key) in options.timezones"
								:value="key">
								{{value}}
							</option>
						</select>
					</x-form-group>
					<x-form-group :errors="errors.date_format" :label="$t('date_format')">
						<select class="form-input" v-model="form.date_format">
							<option v-for="option in options.date_formats" :value="option">
								{{option}}
							</option>
						</select>
					</x-form-group>
					<hr>
					<x-form-group :label="$t('currency_id')" :errors="errors.currency_id">
						<select class="form-input" v-model="form.currency_id">
							<option v-for="c in options.currencies"
								:value="c.code">
								{{c.name}}
							</option>
						</select>
					</x-form-group>
					<x-form-group :label="$t('lang_id')" :errors="errors.lang_id">
						<select class="form-input" v-model="form.lang_id">
							<option v-for="(value, key) in options.langs"
								:value="key">
								{{value}}
							</option>
						</select>
					</x-form-group>
					<x-form-group source="switch" :errors="errors.enable_discount" v-model="form.enable_discount" :label="$t('enable_discount')"></x-form-group>
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
						window.location.replace(`/team-settings`);
					})
					.catch(this.catch)
					.finally(() => {
						this.isSaving = false
					})
			},
		}
	}
</script>