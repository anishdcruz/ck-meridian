<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title" v-if="!form.subscription.isSubscribed">{{$t('create_subscription')}}</div>
			<div slot="title" v-else>{{$t('update_subscription')}}</div>
			<x-row line>
				<x-col span="4">
					<strong>{{$t('plan')}}</strong>
				</x-col>
				<x-col span="11">
					<strong>{{$t('features')}}</strong>
				</x-col>
				<x-col span="4">
					<strong>{{$t('price')}}</strong>
				</x-col>
				<x-col span="5">
					&nbsp;
				</x-col>
			</x-row>
			<x-row v-for="plan in form.plans" :key="plan.id" line>
				<x-col span="4">
					<i class="dropdown-team-icon icon icon-checkmark icon-green"
						v-if="form.subscription.isSubscribed && plan.gateway_id === form.subscription.plan_id"></i>
					<span>{{plan.name}}</span>
				</x-col>
				<x-col span="11">
					<span v-for="(item, i) in plan.list">
						<span v-if="i !== 0"> &middot; </span>
						{{item}}
					</span>
				</x-col>
				<x-col span="4">
					${{plan.price}}
				</x-col>
				<x-col span="5">
					<div v-if="!form.subscription.isSubscribed">
						<x-button @click="choosePlan(plan)"
							:type="planSelected && planSelected.id === plan.id ? 'primary' : 'default'"
							:loading="isSaving">{{$t('choose_plan')}}</x-button>
					</div>
					<div v-else>
						<x-button v-if="showChangeButton(plan)" @click="updateSubscription(plan)" :loading="isSaving">{{$t('change_plan')}}</x-button>
						<span v-if="form.subscription.onGracePeriod && plan.gateway_id === form.subscription.plan_id">{{$t('subs_end_on')}} {{form.subscription.ends_at}}</span>
					</div>
				</x-col>
			</x-row>
		</x-panel>
		<x-panel v-if="form.subscription.isSubscribed" margin>
			<br>
			<x-row>
				<x-col span="20">
					<div v-if="form.subscription.isSubscribed && form.subscription.onGracePeriod">
						<x-alert type="info" :message="`${$t('subs_cancelled', {date: form.subscription.ends_at})}`"></x-alert>
						<br>
						<x-button @click="restoreSubscription" :loading="isRestoring" type="primary">Restore Subscription</x-button>
					</div>
					<div v-else>
						<x-alert type="warning" :message="$t('team_del_warn')"></x-alert>
						<br>
						<x-button type="error" @click="cancelSubscription" :loading="isCancelling">Cancel Subscription</x-button>
					</div>
				</x-col>
			</x-row>
			<br>
		</x-panel>
		<x-panel v-if="planSelected" margin>
			<div slot="title">{{$t('enter_card')}}</div>
			<x-row>
				<x-col span="12" offset="8">
					<br><br>
					<div class="mri-stripe-card">
						<div id="card-element" class="form-control"></div>
						<div id="card-errors" role="alert"></div>
					</div>
					<x-button @click="createSubscription" :loading="isSaving" type="success">
						{{$t('subscribe_plan', {name: planSelected.name, price: planSelected.price})}}
					</x-button>
					<x-button @click="cancel" :loading="isSaving">{{$t('cancel')}}</x-button>
				</x-col>
			</x-row>
			<br><br>
		</x-panel>
	</div>
</template>
<script>
	import { settings } from '@js/lib/mixins'
	export default {
		mixins: [ settings ],
		data() {
			return {
				planSelected: null,
				isCancelling: false,
				isRestoring: false,
				stripe: null,
				scard: null,
				form: {
				},
				options: {
					timezones: []
				}
			}
		},
		computed: {
			showChangeButton() {
				return (plan) => {
					if(this.form.subscription.onGracePeriod) {
						return false
					} else if(this.form.subscription.isSubscribed && plan.gateway_id !== this.form.subscription.plan_id) {
						return true
					}
				}
			}
		},
		methods: {
			choosePlan(plan) {
				this.planSelected = plan
				if(!this.stripe) {
					this.showCard()
				}
			},
			showCard() {
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
				this.scard = elements.create('card', {style: style})
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
			cancel() {
				this.cleanUp()
				this.card = false
				this.planSelected = null
			},
			stripeTokenHandler(token) {
				this.isSaving = true
				this.errors = {}

				const { url, method } = this.getForm()

				this.$http.post('/api/my-account/create-subscription', {
					plan: this.planSelected.id,
					stripeToken: token.id
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
			saveCard() {
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
			setData(res) {
				this.$set(this.$data, 'form', res.data.form)
				this.$set(this.$data, 'options', res.data.options)
				this.$bar.finish()
				this.show = true
			},
			createSubscription() {
				this.isSaving = true
				this.saveCard()
			},
			updateSubscription(plan) {
				const r = confirm(this.$t('are_you_sure'))
				if(r != true) { return }

				this.isSaving = true
				this.errors = {}

				this.$http.post('/api/my-account/update-subscription', {
					plan: plan.id
				})
					.then((res) => {
						this.$message.success(this.$t('saved_success'))
						window.location.replace(`/my-account/subscription`)
					})
					.catch(this.catch)
					.finally(() => {
						this.isSaving = false
					})
			},
			cancelSubscription() {
				const r = confirm(this.$t('are_you_sure'))
				if(r != true) { return }

				this.isCancelling = true
				this.errors = {}

				this.$http.post('/api/my-account/cancel-subscription')
					.then((res) => {
						this.$message.success(this.$t('cancel_success'))
					})
					.catch(this.catch)
					.finally(() => {
						this.isCancelling = false
					})
			},
			restoreSubscription() {
				const r = confirm(this.$t('are_you_sure'))
				if(r != true) { return }

				this.isRestoring = true
				this.errors = {}

				this.$http.post('/api/my-account/restore-subscription')
					.then((res) => {
						this.$message.success(this.$t('saved_success'))
					})
					.catch(this.catch)
					.finally(() => {
						this.isRestoring = false
					})
			},
		}
	}
</script>