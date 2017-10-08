/**
 * Application bootstrap
 */

require('./bootstrap-app');

/**
 * Vue
 */

window.Vue = require('vue');
window.Vuex = require('vuex');

/**
 * Vuex store
 */

Vue.use(Vuex);

import store from './store';

/**
 * Axios
 */

Vue.prototype.$http = window.axios;

/**
 * Load components
 */

Vue.component('projects', require('./components/Projects.vue'));
Vue.component('tests', require('./components/Tests.vue'));
Vue.component('state', require('./components/State.vue'));
Vue.component('log', require('./components/Log.vue'));

/**
 * Start application
 */

const app = new Vue({
    el: '#app',

    store: new Vuex.Store(store),
});
