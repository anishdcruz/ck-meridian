<template>
  <div>
  	<span class="form-side">
  		<small class="form-quick" @click="newForm">{{$t('new')}}</small>
  		<small v-if="source" class="form-quick-sep">&nbsp;|&nbsp;</small>
  		<small v-if="source" class="form-quick" @click="copyForm">{{$t('copy')}}</small>
  	</span>
  	<x-modal width="450" :loading="isSaving" @cancel="onCancel" :okText="$t('save')" @ok="save" v-if="showModal">
  		<div slot="title">
  			<div v-if="isEdit">
  				<span>{{$t('copy')}} {{title}}</span>
  			</div>
  			<div v-else>{{$t('new')}} {{title}}</div>
  		</div>
  		<x-form-group v-model="form.name" :label="$t('name')" :errors="errors.name"></x-form-group>
  	</x-modal>
  </div>
</template>
<script>
  export default {
    name: 'XQuickCategory',
    props: {
    	source: {
    		default: null
    	},
    	url: String,
    	title: String
    },
    data() {
      return {
      	errors: {},
      	form: {
        	name: ''
        },
        isSaving: false,
        loading: true,
        showModal: false,
        isEdit: false
      }
    },
    methods: {
    	newForm() {
    		this.$set(this.$data, 'form', {
	        	name: ''
	        })
	        this.toggleModal()
    	},
    	copyForm() {
    		this.$set(this.$data, 'form', this.source)
    		this.toggleModal()
    	},
    	getForm() {
    		let r = this.url
    		let id = this.id
    		let url = `/api/${r}`
    		let method = 'post'

    		if(this.isEdit) {
    			url = `/api/${r}/${id}`
    			method = 'put'
    		}

    		return {
    			url,
    			method
    		}
    	},
    	save() {
    		this.isSaving = true
    		this.errors = {}

    		const { url, method } = this.getForm()

    		this.$http[method](url, this.form)
    			.then((res) => {
    				if(res.data.saved) {
    					this.$emit('new', res.data.item)
    					this.showModal = false
    					this.$message.success(this.$t('saved_success'))
    				}
    			})
    			.catch((error) => {
    				if(error.response.status === 422) {
    				    this.errors = error.response.data.errors
    				}
    				this.$message.error(error.response.data.message)
    			})
    			.finally(() => {
    				this.isSaving = false
    			})
    	},
    	toggleModal() {
    		this.showModal = !this.showModal
    	},
    	onCancel() {
    		this.showModal = false
    	}
    }
  }
</script>