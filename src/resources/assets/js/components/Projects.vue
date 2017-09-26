<template>
    <div class="list-group">
        <span class="card-projects-items">
            <ul class="list-group">
                <li
                    v-for="project in projects"
                    :class="'list-group-item ' + (selectedProject == project ? 'active' : '')"
                    @click="changeProject(project)"
                >
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
        computed: mapState(['projects', 'selectedProject']),

        mounted() {
            this.$store.dispatch('loadProjects');
        },

        methods: {
            ...mapMutations(['setSelectedProject']),

            ...mapActions(['loadProjects']),

            changeProject(project) {
                this.$store.commit('setSelectedProject', {project, force: true});
            },
        }
    }
</script>
