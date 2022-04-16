import Index from '@js/views/purchase-orders/index.vue'
import Form from '@js/views/purchase-orders/form.vue'
import Show from '@js/views/purchase-orders/show.vue'

export default [
	{
		path: '/purchase-orders', component: Index,
		meta: { resource: 'purchase-orders' }
	},
	{
		path: '/purchase-orders/create', component: Form,
		meta: { resource: 'purchase-orders', mode: 'create' }
	},
	{
		path: '/purchase-orders/:id/edit', component: Form,
		meta: { resource: 'purchase-orders', mode: 'edit' }
	},
	{
		path: '/purchase-orders/:id/clone', component: Form,
		meta: { resource: 'purchase-orders', mode: 'clone' }
	},
	{
		path: '/purchase-orders/:id', component: Show,
		meta: { resource: 'purchase-orders'}
	}
]