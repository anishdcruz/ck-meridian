<template>
	<div class="content-inner" v-if="show">
		<x-panel>
			<div slot="title">{{$t('payment_gateway')}}</div>
			<div>
				<div style="width: 150px;margin-left: -15px;">
					<img src="../../img/Stripe-logo-blue.png" style="width: 100%;">
				</div>
				<br>
				<h1>{{$t('stripe_head')}}</h1>
				<br>
				<p style="width: 45%;">{{$t('stripe_line_1')}}</p>
				<br>
			</div>
		</x-panel>
		<x-panel v-if="form.stripe_user_id" padding>
				<x-row line>
					<x-group col="8" label="payouts">
						<p>{{form.payouts_enabled ? $t('enabled'): $t('disabled')}}</p>
					</x-group>
					<x-group col="8" label="charges">
						<p>{{form.charges_enabled ? $t('enabled'): $t('disabled')}}</p>
					</x-group>
					<x-group col="8" label="mode">
						<pre>{{form.livemode ? $t('live') : $t('test')}}</pre>
					</x-group>
					<x-group col="8" label="country">
						<p>{{form.country}}</p>
					</x-group>
					<x-group col="8" label="default_currency">
						<p>{{form.default_currency}}</p>
					</x-group>
					<x-group col="8" label="stripe_user_id">
						<p>{{form.stripe_user_id}}</p>
					</x-group>
					<x-group col="8" label="email">
						<pre>{{form.email || '-'}}</pre>
					</x-group>
			</x-row>
			<x-row line>
				<x-button @click="onDisconnect" :disabled="processing" type="error">{{$t('stripe_dis')}}</x-button>
			</x-row>
		</x-panel>
		<x-panel v-else>
			<div>
				<small  style="width: 50%;">{{$t('stripe_sm')}}</small>
				<br><br>
				<a v-if="form.url" :href="form.url" class="stripe-connect"><span>{{$t('stripe_conn')}}</span></a>
				<br><br>
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
				processing: false,
				form: {
					url: null,
					stripe_user_id: null,
					livemode: false
				},
				options: {
				}
			}
		},

		methods: {
			onDisconnect() {
				this.processing = true
				this.$http.post('/api/team-settings/payment-gateway/disconnect')
					.then((res) => {
						if(res.data.disconnect) {
							this.form.url = res.data.url
							this.form.stripe_user_id = null
						}
					})
					.finally(() => {
						this.processing = false
					})
			},
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
					})
					.catch(this.catch)
					.finally(() => {
						this.isSaving = false
					})
			},
		}
	}
</script>