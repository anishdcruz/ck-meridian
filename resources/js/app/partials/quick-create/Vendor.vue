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
  		<x-form-group :label="$t('category')" :errors="errors.category_id">
			<x-tag-input :value="form.category" resource="vendor_categories" column="name" name="name"
			    @input="value => { form.category_id = value.id; form.category = value }">
			</x-tag-input>
		</x-form-group>
  		<x-form-group v-model="form.name" :label="$t('name')" :errors="errors.name"></x-form-group>
  		<x-form-group v-model="form.organization" :label="$t('organization')" :errors="errors.organization" optional></x-form-group>
  		<x-form-group v-model="form.email" :label="$t('email')" :errors="errors.email"></x-form-group>
  		<x-form-group v-model="form.phone" :label="$t('phone')" :errors="errors.phone" optional></x-form-group>
  		<x-form-group source="textarea" v-model="form.primary_address" :label="$t('primary_address')" :errors="errors.primary_address"></x-form-group>
  	</x-modal>
  </div>
</template>
<script>
	let templ = {
		name: null,
		organization: null,
		category: null,
		category_id: null,
		email: null,
		phone: null,
		primary_address: null
    }
  export default {
    name: 'XQuickVendor',
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
      	form: {},
        isSaving: false,
        loading: true,
        showModal: false,
        isEdit: false
      }
    },
    methods: {
    	newForm() {
    		const f = Object.assign({}, templ)
    		this.$set(this.$data, 'form', f)
	        this.toggleModal()
    	},
    	copyForm() {
    		const f = Object.assign({}, templ)
    		this.$set(this.$data, 'form', {
    			...f,
    			...this.source
    		})
    		this.toggleModal()
    	},
    	getForm() {
    		let r = this.url
    		let id = this.id
    		let url = `/api/${r}`
    		let method = 'post'

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
    					const f = Object.assign({}, templ)
    					this.$set(this.$data, 'form', f)
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