<template>
	<x-modal width="400" :loading="isProcessing" @cancel="$emit('cancel')">
		<div slot="title">
			<div>
				<span>{{$t('refund_payment')}}</span>
			</div>
		</div>
		<x-row>
			<x-form-group col="10" v-model="form.amount" :label="$t('amount')" :errors="errors.amount"></x-form-group>
			<x-form-group col="14" :label="$t('reason')" :errors="errors.reason">
						<select class="form-input" v-model="form.reason">
							<option v-for="reason in options.reasons"
								:value="reason">
								{{$t(reason)}}
							</option>
						</select>
					</x-form-group>
			<x-form-group col="24" source="textarea" v-model="form.description" :label="$t('description')" :errors="errors.description"></x-form-group>
		</x-row>
		<p>Refunds take 5-10 days to appear on a customer's statement.</p>
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
				warning: null,
				isProcessing: false,
				show: false,
				form: {
					amount: 0,
					reason: 'requested_by_customer',
					description: null
				},
				errors: {},
				options: {
					reasons: [
						'duplicate',
						'fraudulent',
						'requested_by_customer'
					]
				}
			}
		},
		mounted() {
			this.form.amount = this.amount
		},
		methods: {
			save(fd) {
			  this.isProcessing = true
			  this.$http.post(`/api/payments/${this.id}/refund`, this.form)
			    .then((res) => {
			      if(res.data.saved) {
			      	this.$message.success(this.$t('refund_processed'))
			        this.$router.push(`/refunds/${res.data.id}`)
			      }
			    })
			    .catch((error) => {
			      if(error.response.status === 422) {
			          this.errors = error.response.data.errors
			      }
			      this.$message.error(error.response.data.message)
			    })
			    .finally(() => {
			    	this.isProcessing = false
			    })
			},
		}
	}
</script>