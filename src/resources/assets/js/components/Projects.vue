<template>
    <div class="list-group">
        <span class="card-projects-items">
            <ul class="list-group">
                <li
                    v-for="project in projects"
                    :class="'list-group-item ' + (selectedProject == project ? 'active' : '')"
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
    </div>
</template>

<script>
    import {mapState} from 'vuex';
    import {mapMutations} from 'vuex'
    import {mapActions} from 'vuex'

    export default {
        computed: mapState(['projects', 'selectedProject', 'selectedPanel']),

        mounted() {
            this.$store.dispatch('loadProjects');
        },

        methods: {
            ...mapMutations(['setSelectedProject']),

            ...mapActions(['loadProjects']),

            changeProject(project) {
                if (this.selectedProject != project) {
                    this.$store.commit('setSelectedProject', {project, force: true});

                    this.$store.commit('setSelectedPanel', 'log');

                    this.$store.commit('setWasRunning', false);
                }
            },

            toggleTest(test) {
                axios.get(this.laravel.url_prefix+'/projects/'+this.selectedProject.id+'/enable/'+!test.enabled)
                    .then(() => this.loadTests());
            },
        }
    }
</script>
