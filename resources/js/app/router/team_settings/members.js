import Index from '@js/views/team-settings/members/index.vue'
import Form from '@js/views/team-settings/members/form.vue'
import Show from '@js/views/team-settings/members/show.vue'

export default [
	{
		path: 'members', component: Index,
		meta: { resource: 'team-settings/members' }
	},
	{
		path: 'members/create', component: Form,
		meta: { resource: 'team-settings/members', mode: 'create' }
	},
	{
		path: 'members/:id', component: Show,
		meta: { resource: 'team-settings/members'}
	}
]