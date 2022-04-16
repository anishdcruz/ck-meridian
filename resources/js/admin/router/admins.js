import Index from '@admin/views/admins/index.vue'
import Form from '@admin/views/admins/form.vue'
import Show from '@admin/views/admins/show.vue'

export default [
	{
		path: '/admins', component: Index,
		meta: { resource: 'admins' }
	},
	{
		path: '/admins/create', component: Form,
		meta: { resource: 'admins', mode: 'create' }
	},
	{
		path: '/admins/:id/edit', component: Form,
		meta: { resource: 'admins', mode: 'edit' }
	},
	{
		path: '/admins/:id', component: Show,
		meta: { resource: 'admins'}
	}
]