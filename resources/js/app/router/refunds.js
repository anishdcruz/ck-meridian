import Index from '@js/views/refunds/index.vue'
import Show from '@js/views/refunds/show.vue'

export default [
	{
		path: '/refunds', component: Index,
		meta: { resource: 'refunds' }
	},
	{
		path: '/refunds/:id', component: Show,
		meta: { resource: 'refunds'}
	}
]