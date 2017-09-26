new Vuex.Store({
    state: {
        projects: [],
        selectedProject: null,
    },
    mutations: {
        load(state) {
            axios.get('/ci-watcher/projects')
                .then(function (result) {
                    state.projects = result.data.projects;

                    if (!this.selectedProject) {
                        state.selectedProject = state.projects[0].id;
                    }
                });
        }
    },
});
