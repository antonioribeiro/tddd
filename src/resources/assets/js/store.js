export default {
    state: {
        laravel: window.laravel,

        projects: [],

        selectedProject: null,

        openTest: null,

        selectedTest: null,

        selectedPanel: 'log',

        logVisible: false,

        wasRunning: false,
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

        setSelectedPanel(state, panel) {
            state.selectedPanel = panel;
        },

        setOpenTest(state, value) {
            state.openTest = value;
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

        setWasRunning(state, wasIt) {
            state.wasRunning = wasIt;
        },
    },

    actions: {
        loadData(context) {
            axios.get(context.state.laravel.url_prefix+'/dashboard/data' + (context.state.selectedProject ? '/'+context.state.selectedProject.id : ''))
                .then(function (result) {
                    context.commit('setProjects', result.data.projects);

                    var selected = context.state.selectedProject && result.data.projects.filter(project => project.id == context.state.selectedProject.id)[0]
                        ? result.data.projects.filter(project => project.id == context.state.selectedProject.id)[0]
                        : null;

                    if (!selected) {
                        selected = context.state.laravel.project_id
                            ? result.data.projects.filter(project => project.id == context.state.laravel.project_id)[0]
                            : result.data.projects[0]
                    }

                    context.commit('setSelectedProject', {
                        project: selected,

                        force: context.state.laravel.project_id
                                ? true
                                : false,
                    });

                    context.commit('setTests', result.data.tests);
                });
        },
    },
};
