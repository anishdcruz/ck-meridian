import Index from '@js/views/team-settings/roles/index.vue'
import Form from '@js/views/team-settings/roles/form.vue'
import Show from '@js/views/team-settings/roles/show.vue'

export default [
	{
		path: 'roles', component: Index,
		meta: { resource: 'team-settings/roles' }
	},
	{
		path: 'roles/create', component: Form,
		meta: { resource: 'team-settings/roles', mode: 'create' }
	},
	{
		path: 'roles/:id/edit', component: Form,
		meta: { resource: 'team-settings/roles', mode: 'edit' }
	},
	{
		path: 'roles/:id', component: Show,
		meta: { resource: 'team-settings/roles'}
	}
]