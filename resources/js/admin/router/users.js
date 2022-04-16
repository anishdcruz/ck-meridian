import Index from '@admin/views/users/index.vue'
import Show from '@admin/views/users/show.vue'

export default [
	{
		path: '/users', component: Index,
		meta: { resource: 'users' }
	},
	{
		path: '/users/:id', component: Show,
		meta: { resource: 'users'}
	}
]