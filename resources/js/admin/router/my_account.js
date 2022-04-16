import Index from '../views/my-account/index.vue'
import General from '../views/my-account/general.vue'
import Security from '../views/my-account/security.vue'



export default [
	{
		path: '/my-account', component: Index,
		children: [
			{
				path: '', component: General, meta: { resource: 'my-account/general' }
			},
			{
				path: 'security', component: Security, meta: { resource: 'my-account/security' }
			}
		]
	}
]