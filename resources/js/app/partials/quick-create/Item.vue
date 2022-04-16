<template>
  <div>
  	<span class="form-side">
  		<small class="form-quick" @click="newForm">{{$t('new')}}</small>
  	</span>
  	<x-modal width="450" :loading="isSaving" @cancel="onCancel" :okText="$t('save')" @ok="save" v-if="showModal">
  		<div slot="title">
  			<div v-if="isEdit">
  				<span>{{$t('copy')}} {{title}}</span>
  			</div>
  			<div v-else>{{$t('new')}} {{title}}</div>
  		</div>
  		<x-row>
			<x-form-group col="12" :label="$t('category')" :errors="errors.category_id">
				<x-tag-input :value="form.category" resource="item_categories" column="name" name="name"
				    @input="value => { form.category_id = value.id; form.category = value }">
				</x-tag-input>
			</x-form-group>
			<x-form-group col="12" :label="$t('uom')" :errors="errors.uom_id">
				<x-tag-input :value="form.uom" resource="item_uoms" column="name" name="name"
				    @input="value => { form.uom_id = value.id; form.uom = value }">
				</x-tag-input>
			</x-form-group>
		</x-row>
  		<x-form-group source="textarea" v-model="form.description" :label="$t('description')" :errors="errors.description"></x-form-group>
		<x-row>
			<x-form-group col="12" v-model="form.unit_price" :label="$t('unit_price')" :errors="errors.unit_price"></x-form-group>
		</x-row>
  	</x-modal>
  </div>
</template>
<script>
	let templ = {
		uom: null,
		uom_id: null,
		category: null,
		category_id: null,
		description: null,
		unit_price: 0
    }
  export default {
    name: 'XQuickItem',
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
    watch: {
    	'source': 'copyForm'
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
    			...this.source.item
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
    					if(this.source) {
    						this.$emit('copy', res.data.item)
    					} else {
    						this.$emit('new', res.data.item)
    					}
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