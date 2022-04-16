import Index from '@admin/views/plans/index.vue'
import Form from '@admin/views/plans/form.vue'
import Show from '@admin/views/plans/show.vue'

export default [
	{
		path: '/plans', component: Index,
		meta: { resource: 'plans' }
	},
	{
		path: '/plans/create', component: Form,
		meta: { resource: 'plans', mode: 'create' }
	},
	{
		path: '/plans/:id/edit', component: Form,
		meta: { resource: 'plans', mode: 'edit' }
	},
	{
		path: '/plans/:id', component: Show,
		meta: { resource: 'plans'}
	}
]