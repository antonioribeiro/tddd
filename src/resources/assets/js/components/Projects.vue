<template>
    <div class="list-group">
        <span class="card-projects-items">
            <ul class="list-group">
                <li
                    v-for="project in projects"
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
    </div>
</template>

<script>
    import {mapState} from 'vuex';
    import {mapMutations} from 'vuex'
    import {mapActions} from 'vuex'

    export default {
        computed: mapState(['laravel','projects', 'selectedProject', 'selectedPanel']),

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
