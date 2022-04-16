import Vue from 'vue'
import VueRouter from 'vue-router'

import myAccount from './my_account'
import teamSettings from './team_settings'
import contacts from './contacts'
import items from './items'
import quotations from './quotations'
import invoices from './invoices'
import payments from './payments'
import refunds from './refunds'
import recurringInvoices from './recurring_invoices'
import recurringExports from './recurring_exports'
import vendors from './vendors'
import purchaseOrders from './purchase_orders'
import paymentsMade from './payments_made'
import expenses from './expenses'

import Overview from '@js/views/overview/index.vue'

Vue.use(VueRouter)

const router = new VueRouter({
	mode: 'history',
	scrollBehavior (to, from, savedPosition) {
	    if (savedPosition) {
	        return savedPosition
	    } else {
	        return { x: 0, y: 0 }
	    }
	},
	routes: [
		{path: '/', component: Overview, meta: { resource: 'overview'}},
		...myAccount,
		...teamSettings,

		...quotations,
		...contacts,
		...items,
		...invoices,
		...payments,
		...refunds,
		...recurringInvoices,
		...recurringExports,

		...vendors,
		...purchaseOrders,
		...paymentsMade,
		...expenses
	]
})

export default router

