import http from '@js/lib/Http'
import { state } from '@js/shared/store'
const modalable = {
	data() {
		return {
			collection: [],
			query: {
				page: 1
			},
			config: state.team_settings
		}
	},
	mounted() {
		this.show = false
		this.fetch()
	},
	methods: {
		fetch() {
			this.loading = true
			http.get(`/api/${this.resource}`, {
          params: this.query
        })
			  .then((res) => {
			  	this.setData(res)
			  })
		}
	}
}
const indexable = {
	data() {
		return {
			collection: [],
			config: state.team_settings
		}
	},
	beforeRouteEnter(to, from, next) {
		http.get(`/api/${to.meta.resource}`, {params: to.query})
		  .then((res) => {
		  	next((vm) => {
		  		vm.setData(res)
		  	})
		  })
	},
	beforeRouteUpdate(to, from, next) {
		http.get(`/api/${to.meta.resource}`, {params: to.query})
		  .then((res) => {
		  	this.setData(res)
		  	next()
		  })
	},
	methods: {
		setData(res) {
			this.$refs.filterable.setData(res)
			this.$bar.finish()
		}
	}
}

const showable = {
	beforeRouteEnter(to, from, next) {
		http.get(`/api/${to.meta.resource}/${to.params.id}`)
		  .then((res) => {
		  	next((vm) => {
		  		vm.setData(res)
		  	})
		  })
	},
	beforeRouteUpdate(to, from, next) {
		http.get(`/api/${to.meta.resource}/${to.params.id}`)
		  .then((res) => {
		  	this.setData(res)
		  	next()
		  })
	},
	methods: {
		setData(res) {
			this.$set(this.$data, 'model', res.data.model)
			this.$bar.finish()
			this.show = true
		},
		removeDB(resource, id) {
			const r = confirm(this.$t('are_you_sure'))
			if(r != true) { return }

			this.$bar.start()
			this.$http.delete(`/api/${resource}/${id}`)
			    .then((res) => {
			        if(res.data.deleted) {
			            this.$router.push(`/${resource}`)
			            this.$message.success(this.$t('success_delete'))
			        }
			    })
			    .catch((error) => {
			      if(error && error.response.status === 422) {
			        this.$message.error(error.response.data.message)
			      }
			    })
			    .finally(() => {
			    	this.$bar.finish()
			    })
		}
	}
}

const formable = {
	data() {
		return {
			isSaving: false,
			show: false,
			form: {
				organization: {}
			},
			errors: {}
		}
	},
	beforeRouteEnter(to, from, next) {
		http.get(`/api/${getURL(to)}`, {params: to.query})
		  .then((res) => {
		  	next((vm) => {
		  		vm.setData(res)
		  	})
		  })
	},
	beforeRouteUpdate(to, from, next) {
		http.get(`/api/${getURL(to)}`, {params: to.query})
		  .then((res) => {
		  	this.setData(res)
		  	next()
		  })
	},
	computed: {
		mode() {
			return this.$route.meta.mode
		}
	},
	methods: {
		beforeSetData(res) {},
		setData(res) {
			this.beforeSetData(res)
			this.$set(this.$data, 'form', res.data.form)
			this.$bar.finish()
			this.show = true
		},
		cancel() {
			let r = this.$route.meta.resource
			let id = this.$route.params.id
			let url = `/${r}`

			if(this.mode === 'edit') {
				url = `/${r}/${id}`
			} else if(this.mode === 'clone') {
				url = `/${r}/${id}?mode=clone`
			}

			this.$router.push(url)
		},
		save() {
			this.isSaving = true
			this.errors = {}

			const { url, method } = this.getForm()

			this.$http[method](url, this.form)
				.then((res) => {
					this.$router.push(`/${this.redirect}/${res.data.id}`)
					this.$message.success(this.$t('saved_success'))
				})
				.catch(this.catch)
				.finally(() => {
					this.isSaving = false
				})
		},
		catch(error) {
			if(error.response.status === 422) {
			    this.errors = error.response.data.errors
			    this.$message.error(error.response.data.message)
			}
		},
		getForm() {
			let r = this.$route.meta.resource
			let id = this.$route.params.id
			let url = `/api/${r}`
			let method = 'post'

			if(this.mode === 'edit') {
				url = `/api/${r}/${id}`
				method = 'put'
			} else if(this.mode === 'invoice') {
				url = `/api/invoices`
				method = 'post'
			}

			return {
				url,
				method
			}
		}
	}
}

const settings = {
	data() {
		return {
			isSaving: false,
			show: false,
			form: {
			},
			errors: {},
			config: state.team_settings
		}
	},
	beforeRouteEnter(to, from, next) {
		http.get(`/api/${to.meta.resource}`)
		  .then((res) => {
		  	next((vm) => {
		  		vm.setData(res)
		  	})
		  })
	},
	beforeRouteUpdate(to, from, next) {
		http.get(`/api/${to.meta.resource}`)
		  .then((res) => {
		  	this.setData(res)
		  	next()
		  })
	},
	computed: {
		mode() {
			return this.$route.meta.mode
		}
	},
	methods: {
		setData(res) {
			this.$set(this.$data, 'form', res.data.form)
			this.$bar.finish()
			this.show = true
		},
		cancel() {
			let r = this.$route.meta.resource

			let url = `/${r}?cancel`

			// this.$router.push(url)
		},
		save() {
			this.isSaving = true
			this.errors = {}

			const { url, method } = this.getForm()

			this.$http[method](url, this.form)
				.then((res) => {
					const id = Math.random().toString(36).substring(7)
					this.$router.push(`/${this.redirect}?id=${id}`)
					this.$message.success(this.$t('saved_success'))
				})
				.catch(this.catch)
				.finally(() => {
					this.isSaving = false
				})
		},
		catch(error) {
			if(error.response.status === 422) {
			  this.errors = error.response.data.errors
			  this.$message.error(error.response.data.message)
			}
		},
		getForm() {
			let r = this.$route.meta.resource

			let url = `/api/${r}`
			let method = 'post'


			return {
				url,
				method
			}
		}
	}
}

const bar = {
	data() {
		return {
			isSaving: false,
			show: false,
			form: {
			},
			errors: {}
		}
	},
	beforeRouteEnter(to, from, next) {
		next((vm) => {
  		vm.setData({})
  	})
	},
	beforeRouteUpdate(to, from, next) {
		this.setData({})
	},
	methods: {
		setData(res) {
			this.$bar.finish()
			this.show = true
		}
	}
}

function getURL(to) {
  let urls = {
    'create': `${to.meta.resource}/create`,
    'edit': `${to.meta.resource}/${to.params.id}/edit`,
    'clone': `${to.meta.resource}/${to.params.id}/edit?mode=clone`,
    'invoice': `${to.meta.resource}/${to.params.id}/edit?mode=invoice`,
  }
  return (urls[to.meta.mode] || urls['create'])
}

const wizard = {
	data() {
		return {
			url: 'general',
			isSaving: false,
			show: false,
			form: {},
			errors: {},
			options: {
				timezones: []
			}
		}
	},
	mounted() {
		http.get(`/api/team-settings/${this.url}`)
		  .then((res) => {
		  	this.setData(res)
		  })
	},
	methods: {
		setData(res) {
			this.$set(this.$data, 'form', res.data.form)
			this.$set(this.$data, 'options', res.data.options)
			this.isSaving = false
			this.show = true
		},
		save() {
			this.isSaving = true
			this.errors = {}

			this.$http.post(`/api/team-settings/${this.url}`, this.form)
				.then((res) => {
					this.$emit('next', this.url)
				})
				.catch((error) => {
				  if(error && error.response.status === 422) {
				    this.errors = error.response.data.errors
				  }
				})
				.finally(() => {
					this.isSaving = false
				})
		},
	}
}

export {
	indexable,
	showable,
	formable,
	modalable,
	settings,
	bar,
	wizard
}