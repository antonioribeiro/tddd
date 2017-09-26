
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.Vuex = require('vuex');

Vue.use(Vuex);

let store = new Vuex.Store({
    state: {
        projects: [],
        selectedProject: null,
        selectedTest: null,
        logVisible: false,
    },

    mutations: {
        setSelectedProject(state, payload) {
            if (!state.selectedProject || payload.force) {
                state.selectedProject = payload.project;
            }
        },

        setSelectedTest(state, test) {
            state.selectedTest = test;
        },

        setLogVisible(state, visible) {
            state.logVisible = visible;
        },

        setProjects(state, projects) {
            state.projects = projects;
        },

        setTests(state, tests) {
            state.selectedProject.tests = tests;
        },
    },

    actions: {
        loadProjects(context) {
            axios.get('/ci-watcher/projects')
            .then(function (result) {
                context.commit('setProjects', result.data.projects);

                context.commit('setSelectedProject', {project: result.data.projects[0], force: false});
            });
        },

        loadTests(context) {
            axios.get('/ci-watcher/tests/'+context.state.selectedProject.id)
            .then(function (result) {
                context.commit('setTests', result.data.tests);
            });
        },
    },
});

/**
 * Axios
 */

Vue.prototype.$http = window.axios;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
Vue.component('projects', require('./components/Projects.vue'));
Vue.component('tests', require('./components/Tests.vue'));
Vue.component('state', require('./components/State.vue'));
Vue.component('log', require('./components/Log.vue'));

const app = new Vue({
    el: '#app',

    store,
});
