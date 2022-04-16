import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import faqs from './faqs'
import admins from './admins'
import plans from './plans'
import users from './users'
import subscriptions from './subscriptions'
import teams from './teams'
import currencies from './currencies'
import MyAccount from './my_account'
import Overview from '../views/overview/index.vue'

const router = new VueRouter({
	base: `${window.ck_init.admin_prefix}`,
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
		...MyAccount,
		...faqs,
		...admins,
		...plans,
		...users,
		...subscriptions,
		...teams,
		...currencies
	]
})

export default router

