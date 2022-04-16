import Index from '@js/views/team-settings/invitations/index.vue'
import Form from '@js/views/team-settings/invitations/form.vue'
import Show from '@js/views/team-settings/invitations/show.vue'

export default [
	{
		path: 'invitations', component: Index,
		meta: { resource: 'team-settings/invitations' }
	},
	{
		path: 'invitations/create', component: Form,
		meta: { resource: 'team-settings/invitations', mode: 'create' }
	},
	{
		path: 'invitations/:id', component: Show,
		meta: { resource: 'team-settings/invitations'}
	}
]