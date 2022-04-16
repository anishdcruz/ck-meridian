import Index from '@js/views/my-account/index.vue'
import General from '@js/views/my-account/general.vue'
import Teams from '@js/views/my-account/teams.vue'
import Security from '@js/views/my-account/security.vue'
import Subscription from '@js/views/my-account/subscription.vue'
import PaymentMethods from '@js/views/my-account/payment-methods.vue'
import Invoices from '@js/views/my-account/invoices.vue'



export default [
	{
		path: '/my-account', component: Index,
		children: [
			{
				path: '', component: General, meta: { resource: 'my-account/general' }
			},
			{
				path: 'security', component: Security, meta: { resource: 'my-account/security' }
			},
			{
				path: 'teams', component: Teams, meta: { resource: 'my-account/teams' }
			},
			{
				path: 'subscription', component: Subscription, meta: { resource: 'my-account/subscription' }
			},
			{
				path: 'payment-methods', component: PaymentMethods, meta: { resource: 'my-account/payment-methods' }
			},
			{
				path: 'invoices', component: Invoices, meta: { resource: 'my-account/invoices' }
			}
		]
	}
]