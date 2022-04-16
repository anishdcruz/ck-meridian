import Index from '@js/views/recurring-exports/index.vue'
import Form from '@js/views/recurring-exports/form.vue'
import Show from '@js/views/recurring-exports/show.vue'

export default [
	{
		path: '/recurring-exports', component: Index,
		meta: { resource: 'recurring-exports' }
	},
	{
		path: '/recurring-exports/create', component: Form,
		meta: { resource: 'recurring-exports', mode: 'create' }
	},
	{
		path: '/recurring-exports/:id/edit', component: Form,
		meta: { resource: 'recurring-exports', mode: 'edit' }
	},
	{
		path: '/recurring-exports/:id/clone', component: Form,
		meta: { resource: 'recurring-exports', mode: 'clone' }
	},
	{
		path: '/recurring-exports/:id', component: Show,
		meta: { resource: 'recurring-exports'}
	}
]