import Index from '@admin/views/teams/index.vue'
import Show from '@admin/views/teams/show.vue'

export default [
	{
		path: '/teams', component: Index,
		meta: { resource: 'teams' }
	},
	{
		path: '/teams/:id', component: Show,
		meta: { resource: 'teams'}
	}
]