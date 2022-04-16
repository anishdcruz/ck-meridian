import Index from '@admin/views/currencies/index.vue'
import Form from '@admin/views/currencies/form.vue'
import Show from '@admin/views/currencies/show.vue'

export default [
	{
		path: '/currencies', component: Index,
		meta: { resource: 'currencies' }
	},
	{
		path: '/currencies/create', component: Form,
		meta: { resource: 'currencies', mode: 'create' }
	},
	{
		path: '/currencies/:id/edit', component: Form,
		meta: { resource: 'currencies', mode: 'edit' }
	},
	{
		path: '/currencies/:id', component: Show,
		meta: { resource: 'currencies'}
	}
]