<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('payment_methods')}}</div>
			<div v-if="form.hasCardOnFile || form.subscription.isSubscribed">
				<x-row line>
					<x-col span="12" offset="8">
						<x-form-group disabled :errors="errors.card_brand" v-model="form.card.card_brand" :label="$t('card_brand')"></x-form-group>
						<x-form-group disabled :errors="errors.card_last_four" v-model="form.card.card_last_four" :label="$t('card_last_four')"></x-form-group>
						<a  v-if="!card" @click.stop="showCard" :disabled="isSaving">{{$t('update_card_details')}}</a>
						<br>
						<div class="mri-stripe-card" v-if="card">
							<hr>
							<p>{{$t('enter_card')}}</p>
							<br><br>
							<div id="card-element" class="form-control"></div>
							<div id="card-errors" role="alert"></div>
						</div>
						<x-button v-if="card" @click="updateCard" :loading="isSaving" type="success">{{$t('update_card')}}</x-button>
						<x-button v-if="card" @click="cancel" :loading="isSaving">{{$t('cancel')}}</x-button>
					</x-col>
				</x-row>
			</div>
			<div v-else>
				<x-row line>
					<x-col span="24" class="text-center">
						<p>{{$t('subs_plan')}}</p>
						<router-link to="/my-account/subscription">{{$t('subs_now')}}</router-link>
					</x-col>
				</x-row>
			</div>
			<br>
		</x-panel>
	</div>
</template>
<script>
	import { settings } from '@js/lib/mixins'
	export default {
		mixins: [ settings ],
		data() {
			return {
				card: false,
				stripe: null,
				scard: null,
				form: {
				},
				options: {
					timezones: []
				}
			}
		},
		methods: {
			cancel() {
				this.cleanUp()
				this.card = false
			},
			stripeTokenHandler(token) {
				this.isSaving = true
				this.errors = {}

				const { url, method } = this.getForm()

				this.$http[method](url, {
					stripe_token: token.id
				})
					.then((res) => {
						this.cleanUp()
						this.$message.success(this.$t('saved_success'))
					})
					.catch(this.catch)
					.finally(() => {
						this.isSaving = false
					})
			},
			cleanUp() {
				this.scard.removeEventListener('change')
			},
			updateCard() {
				this.stripe.createToken(this.scard).then((result) => {
				  if (result.error) {
				    // Inform the customer that there was an error.
				    var errorElement = document.getElementById('card-errors');
				    errorElement.textContent = result.error.message;
				  } else {
				    // Send the token to your server.
				    this.stripeTokenHandler(result.token);
				  }
				})
			},
			showCard() {
				this.card = true
				var head = document.getElementsByTagName('head')[0];
				var script = document.createElement('script');
				script.type = 'text/javascript';
				script.onload = () => {
				    this.initStripe()
				}
				script.src = 'https://js.stripe.com/v3/'
				head.appendChild(script)
			},
			initStripe() {

				this.stripe = Stripe(this.form.api_key)
				var style = {
				  base: {
				    // Add your base input styles here. For example:
				    fontSize: '16px',
				    color: "#32325d",
				  }
				};

				let elements = this.stripe.elements()
				this.scard = elements.create('card', {
					style: style,
					hidePostalCode: true
				})
				this.scard.mount('#card-element')
				this.scard.addEventListener('change', (event) => {
				  var displayError = document.getElementById('card-errors');
				  if (event.error) {
				    displayError.textContent = event.error.message;
				  } else {
				    displayError.textContent = '';
				  }
				});
			},
			setData(res) {
				this.$set(this.$data, 'form', res.data.form)
				this.$set(this.$data, 'options', res.data.options)
				this.$bar.finish()
				this.show = true
			}
		}
	}
</script>