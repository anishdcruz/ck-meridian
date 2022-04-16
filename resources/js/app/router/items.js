import Index from '@js/views/items/index.vue'
import Form from '@js/views/items/form.vue'
import Show from '@js/views/items/show.vue'

export default [
	{
		path: '/items', component: Index,
		meta: { resource: 'items' }
	},
	{
		path: '/items/create', component: Form,
		meta: { resource: 'items', mode: 'create' }
	},
	{
		path: '/items/:id/edit', component: Form,
		meta: { resource: 'items', mode: 'edit' }
	},
	{
		path: '/items/:id', component: Show,
		meta: { resource: 'items'}
	}
]