<template>
    <div v-if="selectedProject">
        <div class="row table-header">
            <div class="col-md-12 title">
                {{ selectedProject.name }}
            </div>
        </div>

        <div class="card toolbar">
            <div class="card-block">
                <div class="row align-middle">
                    <div class="col-md-6 align-middle">
                        <state state="idle" :text="'tests: '+this.statistics.count"></state>&nbsp;
                        <state state="ok" :text="'success: '+this.statistics.success"></state>&nbsp;
                        <state state="failed" :text="'failed: '+this.statistics.failed"></state>&nbsp;
                        <state state="running" :text="'running: '+this.statistics.running"></state>&nbsp;
                        <state state="enabled" :text="'enabled: '+this.statistics.enabled"></state>&nbsp;
                        <state state="disabled" :text="'disabled: '+(this.statistics.count-this.statistics.enabled)"></state>&nbsp;
                        <state state="idle" :text="'idle: '+this.statistics.idle"></state>&nbsp;
                        <state state="running" :text="'queued: '+this.statistics.queued"></state>&nbsp;
                    </div>

                    <div class="col-md-6 text-right align-middle">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="input-group mb-2 mb-sm-0 search-group">
                                    <input v-model="filter" class="form-control" placeholder="filter">
                                    <div v-if="filter" @click="resetFilter" class="input-group-addon search-addon">
                                        <i class="fa fa-trash"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7" v-if="selectedProject.enabled">
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

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <input @click="enableAll()" type="checkbox" :checked="allEnabled()">
                    </th>
                    <th width="6%">run</th>
                    <th width="8%">state</th>
                    <th>suite</th>
                    <th width="50%">test</th>
                    <th>time</th>
                    <th>last</th>
                    <th width="5%">log</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="test in filteredTests" :class="! test.enabled ? 'dim' : ''">
                    <td>
                        <input
                            type="checkbox"
                            class="testCheckbox"
                            @click="toggleTest(test)"
                            :checked="test.enabled"
                        />
                    </td>

                    <td>
                        <div @click="runTest(test.id)" v-if="test.state !== 'running' && test.state !== 'queued' && selectedProject.enabled" :class="'btn btn-sm btn-' + (test.state == 'failed' ? 'danger' : 'secondary')">
                            run
                        </div>
                    </td>

                    <td :class="'state state-'+test.state">
                        {{ test.state }}

                        <i v-if="test.state == 'running'" class="fa fa-spinner fa-pulse  fa-spin fa-fw"></i>
                    </td>

                    <td>
                        {{ test.suite_name }}
                    </td>

                    <td>
                        <div @click="editFile(test.edit_file_url)" class="table-link">
                            <span class="table-test-path">{{ test.path }}</span><span class="table-test-name">{{ test.name }}</span>
                        </div>
                    </td>

                    <td>{{ test.time }}</td>

                    <td>{{ test.updated_at }}</td>

                    <td>
                        <div @click="showLog(test)" v-if="test.state !== 'running'" :class="'btn btn-sm btn-' + (test.state == 'failed' ? 'primary' : 'secondary')">
                            show
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <log></log>
    </div>
</template>

<script>
    import {mapState} from 'vuex';
    import {mapMutations} from 'vuex'
    import {mapActions} from 'vuex'
    import {mapGetters} from 'vuex'

    export default {
        computed: {
            ...mapState([
                'laravel',
                'projects',
                'openTest',
                'wasRunning',
                'constants',
            ]),

            ...mapGetters([
                'selectedProject',
                'filteredTests',
                'setTestsFilter',
                'selectedTest',
                'statistics',
            ]),

            filter: {
                get () {
                    return this.$store.state.filters.tests
                },
                set (value) {
                    this.$store.commit('setTestsFilter', value)
                }
            },
        },

        methods: {
            ...mapActions(['loadData']),

            runTest(testId) {
                axios.get(this.laravel.url_prefix+'/tests/run/'+testId);
            },

            runAll() {
                axios.post(this.laravel.url_prefix+'/projects/run', { projects: this.selectedProject.id });
            },

            reset() {
                axios.get(this.laravel.url_prefix+'/tests/reset/'+this.selectedProject.id);
            },

            showLog(test) {
                this.$store.commit('setSelectedTestId', test.id);

                jQuery('#logModal').modal('show');
            },

            allEnabled() {
                return this.statistics.count == this.statistics.enabled;
            },

            toggleTest(test) {
                axios.get(this.laravel.url_prefix+'/tests/'+this.selectedProject.id+'/'+test.id+'/enable/'+!test.enabled);
            },

            enableAll() {
                axios.get(this.laravel.url_prefix+'/tests/'+this.selectedProject.id+'/all/enable/'+!this.allEnabled());
            },

            editFile(file) {
                axios.get(file);
            },

            doOpenTest() {
                if (!this.openTest || !this.selectedProject) {
                    return false;
                }

                var test = this.selectedProject.tests.filter(test => test.id == this.openTest)[0]

                if (test) {
                    this.$store.commit('setOpenTest', null);

                    this.showLog(test);
                }
            },

            resetFilter() {
                this.$store.commit('setTestsFilter', '')
            },
        },

        mounted() {
            var vue = this;

            vue.loadData();

            setInterval(function () {
                vue.loadData();
            }, this.laravel.poll_interval);

            setInterval(function () {
                vue.doOpenTest();
            }, 300);

            this.$store.commit('setOpenTest', this.laravel.test_id);
        }
    }
</script>
