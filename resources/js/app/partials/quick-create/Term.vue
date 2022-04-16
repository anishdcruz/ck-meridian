<template>
  <div>
  	<span class="form-side">
  		<small class="form-quick" @click="newForm">{{$t('new')}}</small>
  		<small v-if="source" class="form-quick-sep">&nbsp;|&nbsp;</small>
  		<small v-if="source" class="form-quick" @click="copyForm">{{$t('copy')}}</small>
  	</span>
  	<x-modal width="650" :loading="isSaving" @cancel="onCancel" :okText="$t('save')" @ok="save" v-if="showModal">
  		<div slot="title">
  			<div v-if="isEdit">
  				<span>{{$t('copy')}} {{title}}</span>
  			</div>
  			<div v-else>{{$t('new')}} {{title}}</div>
  		</div>
  		<x-form-group v-model="form.label" :label="$t('label')" :errors="errors.label"></x-form-group>
  		<x-form-group source="textarea" v-model="form.description" :label="$t('description')" :errors="errors.description"></x-form-group>
  	</x-modal>
  </div>
</template>
<script>
	let templ = {
		label: null,
		description: null
    }
  export default {
    name: 'XQuickTerm',
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