import Index from '@admin/views/faqs/index.vue'
import Form from '@admin/views/faqs/form.vue'
import Show from '@admin/views/faqs/show.vue'

export default [
	{
		path: '/faqs', component: Index,
		meta: { resource: 'faqs' }
	},
	{
		path: '/faqs/create', component: Form,
		meta: { resource: 'faqs', mode: 'create' }
	},
	{
		path: '/faqs/:id/edit', component: Form,
		meta: { resource: 'faqs', mode: 'edit' }
	},
	{
		path: '/faqs/:id', component: Show,
		meta: { resource: 'faqs'}
	}
]