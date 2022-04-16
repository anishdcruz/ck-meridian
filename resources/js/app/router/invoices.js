import Index from '@js/views/invoices/index.vue'
import Form from '@js/views/invoices/form.vue'
import Show from '@js/views/invoices/show.vue'

export default [
	{
		path: '/invoices', component: Index,
		meta: { resource: 'invoices' }
	},
	{
		path: '/invoices/create', component: Form,
		meta: { resource: 'invoices', mode: 'create' }
	},
	{
		path: '/invoices/:id/edit', component: Form,
		meta: { resource: 'invoices', mode: 'edit' }
	},
	{
		path: '/invoices/:id/clone', component: Form,
		meta: { resource: 'invoices', mode: 'clone' }
	},
	{
		path: '/invoices/:id', component: Show,
		meta: { resource: 'invoices'}
	}
]