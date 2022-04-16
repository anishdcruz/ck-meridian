<template>
	<x-modal width="400" :loading="isProcessing" @cancel="$emit('cancel')">
		<div slot="title">
			<div>
				<span>{{$t('payment_made')}}</span>
			</div>
		</div>
		<x-row>
			<x-form-group type="date" col="12" v-model="form.payment_date" :label="$t('payment_date')" :errors="errors.payment_date"></x-form-group>
			<x-form-group col="12" :label="$t('payment_method')" :errors="errors.method">
				<select class="form-input" v-model="form.method">
					<option v-for="method in options.reasons"
						:value="method">
						{{$t(method)}}
					</option>
				</select>
			</x-form-group>
			<x-form-group col="12" v-model="form.amount_paid"
				:label="`${$t('amount_paid')} (${state.currency.code})`"
				:errors="errors.amount_paid"></x-form-group>
			<x-form-group col="12" v-model="form.transaction_fees"
			:label="`${$t('transaction_fees')} (${state.currency.code})`"
			:errors="errors.transaction_fees"></x-form-group>

			<x-form-group source="textarea" col="24" v-model="form.note" :label="$t('note')" :errors="errors.note" optional></x-form-group>
		</x-row>
		<template slot="footer">
			<div></div>
			<div>
				<x-button :disabled="isProcessing" @click="$emit('cancel')">{{$t('cancel')}}</x-button>
				<x-button :disabled="isProcessing" type="primary" @click="save">
					{{$t('save')}}
				</x-button>
			</div>
		</template>
	</x-modal>
</template>
<script>
	import { state } from '@js/shared/store'
	export default {
		props: {
			amount: {
				required: true,
				type: Number
			},
			id: {
				required: true,
				type: Number
			}
		},
		data() {
			return {
				state: state.current_team,
				warning: null,
				isProcessing: false,
				show: false,
				form: {
					amount_paid: 0,
					transaction_fees: 0,
					method: 'cheque',
					note: null
				},
				errors: {},
				options: {
					reasons: [
						'cheque',
						'cash',
						'bank_transfer'
					]
				}
			}
		},
		mounted() {
			this.form.amount_paid = this.amount
		},
		methods: {
			save(fd) {
			  this.isProcessing = true
			  let form = {
			  	...this.form,
			  	purchase_order_id: this.id
			  }
			  this.$http.post(`/api/payments-made`, form)
			    .then((res) => {
			      if(res.data.saved) {
			      	this.$message.success(this.$t('saved_success'))
			        this.$router.push(`/payments-made/${res.data.id}`)
			      }
			    })
			    .catch((error) => {
			      if(error.response.status === 422) {
			          this.errors = error.response.data.errors
			      }
			    })
			    .finally(() => {
			    	this.isProcessing = false
			    })
			},
		}
	}
</script>