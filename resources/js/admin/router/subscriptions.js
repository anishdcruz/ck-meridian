import Index from '@admin/views/subscriptions/index.vue'
import Show from '@admin/views/subscriptions/show.vue'

export default [
	{
		path: '/subscriptions', component: Index,
		meta: { resource: 'subscriptions' }
	},
	{
		path: '/subscriptions/:id', component: Show,
		meta: { resource: 'subscriptions'}
	}
]