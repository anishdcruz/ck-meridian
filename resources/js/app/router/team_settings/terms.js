import Index from '@js/views/team-settings/terms/index.vue'
import Form from '@js/views/team-settings/terms/form.vue'
import Show from '@js/views/team-settings/terms/show.vue'

export default [
	{
		path: 'terms', component: Index,
		meta: { resource: 'team-settings/terms' }
	},
	{
		path: 'terms/create', component: Form,
		meta: { resource: 'team-settings/terms', mode: 'create' }
	},
	{
		path: 'terms/:id/edit', component: Form,
		meta: { resource: 'team-settings/terms', mode: 'edit' }
	},
	{
		path: 'terms/:id', component: Show,
		meta: { resource: 'team-settings/terms'}
	}
]