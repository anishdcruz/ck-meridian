import Index from '@js/views/vendors/index.vue'
import Form from '@js/views/vendors/form.vue'
import Show from '@js/views/vendors/show.vue'

export default [
	{
		path: '/vendors', component: Index,
		meta: { resource: 'vendors' }
	},
	{
		path: '/vendors/create', component: Form,
		meta: { resource: 'vendors', mode: 'create' }
	},
	{
		path: '/vendors/:id/edit', component: Form,
		meta: { resource: 'vendors', mode: 'edit' }
	},
	{
		path: '/vendors/:id', component: Show,
		meta: { resource: 'vendors'}
	}
]