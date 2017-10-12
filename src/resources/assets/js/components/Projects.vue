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
            <span class="card-projects-items">

                <span class="row">
                    <span class="col-md-12">
                        <ul class="list-group">
                    <li
                            v-for="project in filteredProjects"
                            :class="'list-group-item ' + (! project.enabled ? 'dim ' : '') + (selectedProject.id == project.id ? 'active ' : '')"
                            @click="changeProject(project)"
                    >
                        <input
                                type="checkbox"
                                class="project-checkbox testCheckbox"
                                @click="toggleProject(project)"
                                :checked="project.enabled"
                        />

                        {{ project.name }}
                    </li>
                </ul>
                    </span>
                </span>
            </span>
        </div>
    </span>
</template>

<script>
    import {mapState} from 'vuex';
    import {mapMutations} from 'vuex'
    import {mapActions} from 'vuex'

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
                'selectedProject',
                'selectedPanel'
            ]),

            filteredProjects() {
                var vue = this;

                return vue.projects.filter(function(project) {
                    return project.name.search(new RegExp(vue.search, "i")) != -1;
                });
            }
        },

        methods: {
            ...mapMutations(['setSelectedProject']),

            ...mapActions(['loadData']),

            changeProject(project) {
                if (this.selectedProject != project) {
                    this.$store.commit('setSelectedProject', {project, force: true});

                    this.$store.commit('setSelectedPanel', 'log');

                    this.$store.commit('setWasRunning', false);
                }
            },

            toggleProject(project) {
                axios.get(this.laravel.url_prefix+'/projects/'+project.id+'/enable/'+!project.enabled)
                    .then(() => this.loadData());
            },
        }
    }
</script>
