<template>
    <div v-if="selectedProject">
        <div>
            <div class="row table-header">
                <div class="col-md-9">
                    <h2>{{ selectedProject.name }}</h2>
                </div>

                <div class="col-md-3 text-right">
                    <div class="btn btn-danger" @click="runAll()">
                        run all
                    </div>
                    &nbsp;
                    <div class="btn btn-warning" @click="reset()">
                        reset state
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <input @click="enableAll()" type="checkbox" :checked="allEnabled()">
                    </th>
                    <th>run</th>
                    <th width="70%">test</th>
                    <th>last run</th>
                    <th>state</th>
                    <th>log</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="test in tests" :class="! test.enabled ? 'dim' : ''">
                    <td>
                        <input
                            type="checkbox"
                            class="testCheckbox"
                            @click="toggleTest(test)"
                            :checked="test.enabled"
                        />
                    </td>

                    <td>
                        <div @click="runTest(test.id)" class="btn btn-danger btn-sm">
                            run
                        </div>
                    </td>

                    <td>{{ test.name }}</td>

                    <td>{{ test.updated_at }}</td>

                    <td>
                        <state :state="test.state"></state>
                    </td>

                    <td>
                        <div @click="showLog(test)" v-if="test.state == 'failed'" class="btn btn-primary btn-sm">
                            show
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <log v-if="selectedTest"></log>
    </div>
</template>

<script>
    import {mapState} from 'vuex';
    import {mapMutations} from 'vuex'
    import {mapActions} from 'vuex'

    export default {
        computed: {
            ...mapState(['projects', 'selectedProject', 'selectedTest']),

            tests() {
                this.makeStatistics();

                return this.selectedProject.tests;
            }
        },

        watch: {
            selectedProject() {
                this.loadTests();
            }
        },

        data() {
            return {
                statistics: {},

                wasRunning: false,
            }
        },

        methods: {
            ...mapActions(['loadTests']),

            runTest(testId) {
                axios.get('/ci-watcher/tests/run/'+testId);
            },

            runAll() {
                axios.get('/ci-watcher/tests/run/all/'+this.selectedProject.id);
            },

            reset() {
                axios.get('/ci-watcher/tests/reset/'+this.selectedProject.id);
            },

            showLog(test) {
                this.$store.commit('setSelectedTest', test);

                jQuery('#logModal').modal('show');
            },

            allEnabled() {
                var key = null;

                return true;
            },

            toggleTest(test) {
                axios.get('/ci-watcher/tests/enable/'+!test.enabled+'/'+this.selectedProject.id+'/'+test.id)
                    .then(() => this.loadTests());
            },

            loadTests() {
                if (this.selectedProject) {
                    this.$store.dispatch('loadTests');
                }
            },

            enableAll() {
                axios.get('/ci-watcher/tests/enable/'+!this.allEnabled()+'/'+this.selectedProject.id)
                    .then(() => this.loadTests());
            },

            clearStatistics: function () {
                this.statistics = {
                    count: 0,
                    enabled: 0,
                    running: 0,
                    queued: 0,
                    success: 0,
                    failed: 0,
                    idle: 0,
                };
            },

            sendNotifications: function () {
                if (this.statistics.failed > 0) {
                    axios.get('/ci-watcher/tests/notify/failed');
                }
            },

            makeStatistics() {
                this.clearStatistics();

                var key = null;

                for (key in this.selectedProject.tests) {
                    if (this.selectedProject.tests.hasOwnProperty(key)) {
                        this.statistics.count++;

                        if (!this.selectedProject.tests[key].enabled) {
                            this.statistics.enabled++;
                        }

                        if (this.selectedProject.tests[key].state == 'queued') {
                            this.statistics.queued++;
                        }

                        if (this.selectedProject.tests[key].state == 'running') {
                            this.statistics.running++;
                        }

                        if (this.selectedProject.tests[key].state == 'success') {
                            this.statistics.success++;
                        }

                        if (this.selectedProject.tests[key].state == 'failed') {
                            this.statistics.failed++;
                        }

                        if (this.selectedProject.tests[key].state == 'idle') {
                            this.statistics.idle++;
                        }
                    }
                }

                if (this.wasRunning && !this.isRunning()) {
                    this.sendNotifications();
                }

                this.wasRunning = this.isRunning();
            },

            isRunning() {
                return this.statistics.running > 0;
            }
        },

        mounted() {
            var vue = this;

            setInterval(function () {
                vue.loadTests();
            }, 1500);
        }
    }
</script>
