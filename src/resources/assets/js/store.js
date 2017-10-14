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

        filters: {
            projects: '',

            tests: '',
        }
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

        setProjectsFilter(state, filter) {
            state.filters.projects = filter;
        },

        setTestsFilter(state, filter) {
            state.filters.tests = filter;
        },

        setProjectsFilter(state, filter) {
            state.filters.projects = filter;
        },
    },

    getters: {
        selectedProject(state) {
            return state.projects.find(function (project) {
                return project.id === state.selectedProjectId;
            });
        },

        filteredProjects(state) {
            return state.projects.filter(function(project) {
                return project.name.search(new RegExp(state.filters.projects, "i")) != -1;
            });
        },

        filteredTests(state) {
            let tests = state.projects.find(function (project) {
                return project.id === state.selectedProjectId;
            }).tests.filter(function(test) {
                let s1 = test.state.search(new RegExp(state.filters.tests, "i")) != -1;

                let s2 = test.name.search(new RegExp(state.filters.tests, "i")) != -1;

                let s3 = test.path.search(new RegExp(state.filters.tests, "i")) != -1;

                return s1 || s2 || s3;
            });

            return tests;
        },

        statistics(state, getters) {
            let statistics = {
                count: 0,
                enabled: 0,
                running: 0,
                queued: 0,
                success: 0,
                failed: 0,
                idle: 0,
            };

            if (!getters.selectedProject || !getters.selectedProject.tests) {
                return statistics;
            }

            let tests = getters.selectedProject.tests;

            for (let key in tests) {
                statistics.count++;

                if (tests[key].enabled) {
                    statistics.enabled++;
                }

                if (tests[key].state == 'queued') {
                    statistics.queued++;
                }

                if (tests[key].state == 'running') {
                    statistics.running++;
                }

                if (tests[key].state == 'ok') {
                    statistics.success++;
                }

                if (tests[key].state == 'failed') {
                    statistics.failed++;
                }

                if (tests[key].state == 'idle') {
                    statistics.idle++;
                }
            }

            return statistics;
        },

        isRunning(state, getters) {
            return getters.statistics.running > 0;
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

            if (context.state.wasRunning && !context.getters.isRunning) {
                if (context.getters.statistics.failed > 0) {
                    axios.get(context.state.laravel.url_prefix+'/projects/'+context.state.selectedProjectId+'/notify');
                }
            }

            context.commit('setWasRunning', context.getters.isRunning);
        },
    },
};
