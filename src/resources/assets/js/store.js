export default {
    state: {
        base_uri: '/tests-watcher',

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
            axios.get(context.state.base_uri+'/projects')
                .then(function (result) {
                    context.commit('setProjects', result.data.projects);

                    context.commit('setSelectedProject', {project: result.data.projects[0], force: false});
                });
        },

        loadTests(context) {
            axios.get(context.state.base_uri+'/tests/'+context.state.selectedProject.id)
                .then(function (result) {
                    context.commit('setTests', result.data.tests);
                });
        },
    },
};
