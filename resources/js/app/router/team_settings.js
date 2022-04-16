import Index from '@js/views/team-settings/index.vue'
import General from '@js/views/team-settings/general.vue'
import Taxes from '@js/views/team-settings/taxes.vue'
import Contacts from '@js/views/team-settings/contacts.vue'
import Items from '@js/views/team-settings/items.vue'
import Quotations from '@js/views/team-settings/quotations.vue'
import Documents from '@js/views/team-settings/documents.vue'
import Invoice from '@js/views/team-settings/invoices.vue'
import PaymentGateway from '@js/views/team-settings/payment-gateway.vue'
import Payments from '@js/views/team-settings/payments.vue'
import Refund from '@js/views/team-settings/refund.vue'
import Vendors from '@js/views/team-settings/vendors.vue'
import PurchaseOrder from '@js/views/team-settings/purchase-orders.vue'
import Expenses from '@js/views/team-settings/expenses.vue'

import roles from './team_settings/roles'
import invitations from './team_settings/invitations'
import members from './team_settings/members'
import terms from './team_settings/terms'

export default [
	{
		path: '/team-settings', component: Index,
		children: [
			{
				path: '', component: General, meta: { resource: 'team-settings/general' }
			},
			{
				path: 'taxes', component: Taxes, meta: { resource: 'team-settings/taxes' }
			},
			{
				path: 'documents', component: Documents, meta: { resource: 'team-settings/documents' }
			},
			{
				path: 'contacts', component: Contacts
			},
			{
				path: 'items', component: Items
			},
			{
				path: 'quotations', component: Quotations, meta: { resource: 'team-settings/quotations' }
			},
			{
				path: 'invoices', component: Invoice, meta: { resource: 'team-settings/invoices' }
			},
			{
				path: 'payments', component: Payments, meta: { resource: 'team-settings/payments' }
			},
			{
				path: 'refunds', component: Refund, meta: { resource: 'team-settings/refunds' }
			},
			{
				path: 'payment-gateway', component: PaymentGateway, meta: { resource: 'team-settings/payment-gateway' }
			},
			{
				path: 'vendors', component: Vendors
			},
			{
				path: 'purchase-orders', component: PurchaseOrder, meta: { resource: 'team-settings/purchase-orders' }
			},
			{
				path: 'expenses', component: Expenses
			},
			...roles,
			...members,
			...invitations,
			...terms
		]
	}
]