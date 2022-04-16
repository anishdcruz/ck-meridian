import Index from '@js/views/recurring-invoices/index.vue'
import Form from '@js/views/recurring-invoices/form.vue'
import Show from '@js/views/recurring-invoices/show.vue'

export default [
	{
		path: '/recurring-invoices', component: Index,
		meta: { resource: 'recurring-invoices' }
	},
	{
		path: '/recurring-invoices/create', component: Form,
		meta: { resource: 'recurring-invoices', mode: 'create' }
	},
	{
		path: '/recurring-invoices/:id/edit', component: Form,
		meta: { resource: 'recurring-invoices', mode: 'edit' }
	},
	{
		path: '/recurring-invoices/:id/clone', component: Form,
		meta: { resource: 'recurring-invoices', mode: 'clone' }
	},
	{
		path: '/recurring-invoices/:id', component: Show,
		meta: { resource: 'recurring-invoices'}
	}
]