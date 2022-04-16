import Vue from 'vue'

import App from './App.vue'
import components from '@js/components'

import { quillEditor } from 'vue-quill-editor'

import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

Vue.use(components)

Vue.component('quill-editor', quillEditor)
let i = 0
let missing = []
Vue.prototype.$t = (key, extra_data = {}) => {
	let str = T_FILES[key]

	if(typeof str === 'undefined') {
    // if(!missing.includes(key)) {
    //   i++
    //   missing.push(key)
    //   console.log(i, key)
    // }

		return key
	}

	if(extra_data) {
		str = str.replace(/:(\w+)/g, function(_, $1) {
			return extra_data[$1]
		})
	}

	return str
}

Vue.prototype.$inSAASMode = window.ck_init.mode === 'saas'

import { init } from '@js/shared/store'

init(window.ck_init)
import '@js/lib/Filters'
import router from './router'

import http from '@js/lib/Http'

http.interceptors.response.use(function (response) {
    // Do something with response data
    return response;
  }, function (err) {
    // Do something with response error
    if(err.response.status === 401) {
      components.LoadingBar.finish()
     	window.location.replace(`/`);
    }

    if(err.response.status === 403) {
      components.LoadingBar.finish()
      components.Message.error(err.response.data.message, 0)
    }

    if(err.response.status === 500) {
    	components.LoadingBar.finish()
      components.Message.error(err.response.data.message, 0)
    }

    if(err.response.status === 404) {
    	components.LoadingBar.finish()
      components.Message.error(err.response.data.message, 0)
    }
    return Promise.reject(err);
  });

router.beforeEach((to, from, next) => {
    components.LoadingBar.start()
    next()
})

const app = new Vue({
	el: '#root',
	render: (h) => h(App),
	router
})