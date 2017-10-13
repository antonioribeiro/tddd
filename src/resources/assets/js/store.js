export default {
    state: {
        laravel: window.laravel,

        projects: [],

        selectedProjectId: null,

        openTest: null,

        selectedTest: null,

        selectedPanel: 'log',

        logVisible: false,

        wasRunning: false,
    },

    mutations: {
        setSelectedProject(state, payload) {
            if (!state.selectedProject || payload.force) {
                state.selectedProjectId = payload.project.id;
            }
        },

        setSelectedProjectId(state, projectId) {
            state.selectedProjectId = projectId;
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

        setWasRunning(state, wasIt) {
            state.wasRunning = wasIt;
        },
    },

    getters: {
        selectedProject: state => {
            return state.projects.find(function (project) {
                return project.id === state.selectedProjectId;
            })
        },
    },

    actions: {
        setSelectedProjectId(context, projectId) {
            let selectedProjectId = context.state.selectedProjectId;

            context.commit('setSelectedProjectId', projectId);

            if (projectId != selectedProjectId) {
                context.dispatch('loadData');
            }
        },

        loadData(context) {
            axios.get(context.state.laravel.url_prefix+'/dashboard/data')
                .then(function (result) {
                    context.commit('setProjects', result.data.projects);

                    var selected = context.getters.selectedProject && result.data.projects.filter(project => project.id == context.getters.selectedProject.id)[0]
                        ? result.data.projects.filter(project => project.id == context.getters.selectedProject.id)[0]
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
                });
        },
    },
};
