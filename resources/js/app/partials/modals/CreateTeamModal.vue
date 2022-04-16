<template>
	<x-modal width="350" :loading="isProcessing" @cancel="$emit('cancel')">
		<div slot="title">
			<div>
				<span>{{$t('create_team')}}</span>
			</div>
		</div>
		<div>
			<x-form-group v-model="form.team_name" :label="$t('team_name')" :errors="errors.team_name"></x-form-group>
		</div>
		<template slot="footer">
			<div></div>
			<div>
				<x-button :disabled="isProcessing" @click="$emit('cancel')">{{$t('cancel')}}</x-button>
				<x-button :disabled="isProcessing" type="primary" @click="save">
					{{$t('create')}}
				</x-button>
			</div>
		</template>
	</x-modal>
</template>
<script>
	export default {
		props: {
			type: String,
			id: Number
		},
		data() {
			return {
				warning: null,
				isProcessing: false,
				show: false,
				form: {},
				errors: {}
			}
		},
		mounted() {
			this.isProcessing = true
	    this.receiveData()
		},
		methods: {
			receiveData() {
				this.$set(this.$data, 'form', {
					name: null
				})
        this.show = true
        this.isProcessing = false
			},
			save(fd) {
			  this.isProcessing = true
			  this.$http.post(`/api/my-account/teams`, this.form)
			    .then((res) => {
			      if(res.data.saved) {
			      	this.$message.success(this.$t('email_sent'))
			        this.$emit('ok', res.data.team)
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