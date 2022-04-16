import Index from '@js/views/quotations/index.vue'
import Form from '@js/views/quotations/form.vue'
import Show from '@js/views/quotations/show.vue'

import Invoice from '@js/views/invoices/form.vue'

export default [
	{
		path: '/quotations', component: Index,
		meta: { resource: 'quotations' }
	},
	{
		path: '/quotations/create', component: Form,
		meta: { resource: 'quotations', mode: 'create' }
	},
	{
		path: '/quotations/:id/edit', component: Form,
		meta: { resource: 'quotations', mode: 'edit' }
	},
	{
		path: '/quotations/:id/clone', component: Form,
		meta: { resource: 'quotations', mode: 'clone' }
	},
	{
		path: '/quotations/:id/invoice', component: Invoice,
		meta: { resource: 'quotations', mode: 'invoice' }
	},
	{
		path: '/quotations/:id', component: Show,
		meta: { resource: 'quotations'}
	}
]