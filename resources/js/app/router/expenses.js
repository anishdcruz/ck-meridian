import Index from '@js/views/expenses/index.vue'
import Form from '@js/views/expenses/form.vue'
import Show from '@js/views/expenses/show.vue'

export default [
	{
		path: '/expenses', component: Index,
		meta: { resource: 'expenses' }
	},
	{
		path: '/expenses/create', component: Form,
		meta: { resource: 'expenses', mode: 'create' }
	},
	{
		path: '/expenses/:id/edit', component: Form,
		meta: { resource: 'expenses', mode: 'edit' }
	},
	{
		path: '/expenses/:id', component: Show,
		meta: { resource: 'expenses'}
	}
]