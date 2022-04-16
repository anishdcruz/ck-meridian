try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}


window.Vue = require('vue');


Vue.component('stripe-card', require('./components/StripeCard.vue').default)
Vue.component('x-button', require('./components/Button.vue').default)


const app = new Vue({
	el: '#app'
})
