import Index from '@js/views/contacts/index.vue'
import Form from '@js/views/contacts/form.vue'
import Show from '@js/views/contacts/show.vue'

export default [
	{
		path: '/contacts', component: Index,
		meta: { resource: 'contacts' }
	},
	{
		path: '/contacts/create', component: Form,
		meta: { resource: 'contacts', mode: 'create' }
	},
	{
		path: '/contacts/:id/edit', component: Form,
		meta: { resource: 'contacts', mode: 'edit' }
	},
	{
		path: '/contacts/:id', component: Show,
		meta: { resource: 'contacts'}
	}
]