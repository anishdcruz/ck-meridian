import Index from '@js/views/payments-made/index.vue'
import Form from '@js/views/payments-made/form.vue'
import Show from '@js/views/payments-made/show.vue'

export default [
	{
		path: '/payments-made', component: Index,
		meta: { resource: 'payments-made' }
	},
	{
		path: '/payments-made/create', component: Form,
		meta: { resource: 'payments-made', mode: 'create' }
	},
	{
		path: '/payments-made/:id/edit', component: Form,
		meta: { resource: 'payments-made', mode: 'edit' }
	},
	{
		path: '/payments-made/:id/clone', component: Form,
		meta: { resource: 'payments-made', mode: 'clone' }
	},
	{
		path: '/payments-made/:id', component: Show,
		meta: { resource: 'payments-made'}
	}
]