<template>
    <div v-if="selectedProject">
        <div>
            <div class="row table-header">
                <div class="col-md-12 title">
                    {{ selectedProject.name }}
                </div>
            </div>

            <div class="card toolbar">
                <div class="card-block">
                    <div class="row align-middle">
                        <div class="col-md-7 align-middle">
                            <state state="idle" :text="'tests: '+this.statistics.count"></state>&nbsp;
                            <state state="ok" :text="'success: '+this.statistics.success"></state>&nbsp;
                            <state state="failed" :text="'failed: '+this.statistics.failed"></state>&nbsp;
                            <state state="running" :text="'running: '+this.statistics.running"></state>&nbsp;
                            <state state="enabled" :text="'enabled: '+this.statistics.enabled"></state>&nbsp;
                            <state state="disabled" :text="'disabled: '+(this.statistics.count-this.statistics.enabled)"></state>&nbsp;
                            <state state="idle" :text="'idle: '+this.statistics.idle"></state>&nbsp;
                            <state state="running" :text="'queued: '+this.statistics.queued"></state>&nbsp;
                        </div>

                        <div class="col-md-5 text-right align-middle">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="input-group mb-2 mb-sm-0 search-group">
                                        <input v-model="search" class="form-control" placeholder="search">
                                        <div v-if="search" @click="search = ''" class="input-group-addon search-addon">
                                            <i class="fa fa-trash"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="btn btn-danger" @click="runAll()">
                                        run all
                                    </div>
                                    <div class="btn btn-warning" @click="reset()">
                                        reset state
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    <th width="7%">state</th>
                    <th width="70%">test</th>
                    <th>last run</th>
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
                        <div @click="runTest(test.id)" v-if="test.state !== 'running' && test.state !== 'queued'" :class="'btn btn-sm btn-' + (test.state == 'failed' ? 'danger' : 'default')">
                            run
                        </div>
                    </td>

                    <td :class="'state state-'+test.state">
                        <!--<state :state="test.state"></state>-->

                        {{ test.state }}
                        <i v-if="test.state == 'running'" class="fa fa-spinner fa-pulse  fa-spin fa-fw"></i>
                    </td>

                    <td>{{ test.name }}</td>

                    <td>{{ test.updated_at }}</td>

                    <td>
                        <div @click="showLog(test)" v-if="test.state !== 'running'" :class="'btn btn-sm btn-' + (test.state == 'failed' ? 'danger' : 'default')">
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
                var vue = this;

                var tests = vue.selectedProject.tests.filter(function(test) {
                    var s1 = test.state.search(new RegExp(vue.search, "i")) != -1;

                    var s2 = test.name.search(new RegExp(vue.search, "i")) != -1;

                    return s1 || s2;
                });

                this.makeStatistics(tests);

                return tests;
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

                search: '',
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

                console.log('clicked');
                jQuery('#logModal').modal('show');
            },

            allEnabled() {
                return this.statistics.count == this.statistics.enabled;
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

            makeStatistics(tests) {
                this.clearStatistics();

                var key = null;

                for (key in tests) {
                    if (tests.hasOwnProperty(key)) {
                        this.statistics.count++;

                        if (tests[key].enabled) {
                            this.statistics.enabled++;
                        }

                        if (tests[key].state == 'queued') {
                            this.statistics.queued++;
                        }

                        if (tests[key].state == 'running') {
                            this.statistics.running++;
                        }

                        if (tests[key].state == 'ok') {
                            this.statistics.success++;
                        }

                        if (tests[key].state == 'failed') {
                            this.statistics.failed++;
                        }

                        if (tests[key].state == 'idle') {
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
