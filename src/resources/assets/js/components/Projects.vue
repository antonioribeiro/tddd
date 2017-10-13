<template>
    <span>
        <div class="card bg-inverse card-projects">
            <div class="card-block text-center">
                <div class="row">
                    <div class="col project-title">
                        Projects
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-10">
                        <input v-model="search" class="form-control form-control-sm search-project" placeholder="filter">
                    </div>
                </div>
            </div>
        </div>

        <div class="list-group">
            <div class="card-projects-items">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="list-group">
                            <li
                                v-for="project in filteredProjects"
                                :class="'list-group-item ' + (! project.enabled ? 'dim ' : '') + (selectedProject.id == project.id ? 'active ' : '')"
                                @click="changeProject(project)"
                            >
                                <div class="row">
                                    <div class="col">
                                        <input
                                            type="checkbox"
                                            class="project-checkbox testCheckbox"
                                            @click="toggleProject(project)"
                                            :checked="project.enabled"
                                        />

                                        {{ project.name }}
                                    </div>
                                    <div class="col-2 text-right">
                                        <span v-if="project.state == 'running'">
                                            <i class="fa fa-spinner fa-pulse  fa-spin fa-fw"></i>
                                        </span>

                                        <span v-if="project.state == 'failed'" class="project-state text-danger">
                                            <i class="fa fa-times"></i>
                                        </span>

                                        <span v-if="project.state == 'ok'" class="project-state text-success">
                                            <i class="fa fa-check"></i>
                                        </span>

                                        <span v-if="project.state == 'queued'" class="project-state text-warning">
                                            <i class="fa fa-clock-o"></i>
                                        </span>

                                        <span v-if="project.state == 'idle'" class="project-state text-default pale">
                                            <i class="fa fa-pause"></i>
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import {mapState} from 'vuex';
    import {mapMutations} from 'vuex'
    import {mapActions} from 'vuex'
    import {mapGetters} from 'vuex'

    export default {
        data() {
            return {
                search: '',
            }
        },

        computed: {
            ...mapState([
                'laravel',
                'projects',
                'selectedPanel'
            ]),

            ...mapGetters([
                'selectedProject',
            ]),

            filteredProjects() {
                var vue = this;

                return vue.projects.filter(function(project) {
                    return project.name.search(new RegExp(vue.search, "i")) != -1;
                });
            }
        },

        methods: {
            ...mapActions(['loadData', 'setSelectedProjectId']),

            changeProject(project) {
                if (this.selectedProject != project) {
                    this.setSelectedProjectId(project.id);

                    this.$store.commit('setSelectedPanel', 'log');

                    this.$store.commit('setWasRunning', false);
                }
            },

            toggleProject(project) {
                axios.get(this.laravel.url_prefix+'/projects/'+project.id+'/enable/'+!project.enabled)
                    .then(response => this.loadData());
            },
        }
    }
</script>
