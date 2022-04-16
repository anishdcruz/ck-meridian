import Index from '@js/views/payments/index.vue'
import Show from '@js/views/payments/show.vue'

export default [
	{
		path: '/payments', component: Index,
		meta: { resource: 'payments' }
	},
	{
		path: '/payments/:id', component: Show,
		meta: { resource: 'payments'}
	}
]