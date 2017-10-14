<template>
    <div class="modal fade" tabIndex="-1" id="logModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" v-if="selectedTest">
                <div class="modal-header">
                    <h3 class="modal-title">
                        {{ selectedProject.name }} - {{ selectedTest.name }}
                    </h3>

                    <div class="close">
                        <h3>
                            <span v-if="selectedTest.state == 'failed' || selectedTest.state == 'ok'" :class="'pull-right badge badge-'+(selectedTest.state == 'failed' ? 'danger' : 'success')">
                                {{ selectedTest.state }}
                            </span>
                        </h3>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-8">
                            <div v-if="selectedTest.log" :class="'btn btn-pill ' + getPillColor(constants.LOG_ID_LOG)" @click="setPanelLog()">
                                command output
                            </div>
                            &nbsp;
                            <div v-if="selectedTest.run.screenshots"  :class="'btn btn-pill ' + getPillColor(constants.LOG_ID_SCREENSHOTS)" @click="setPanelScreenshot()">
                                screenshots
                            </div>
                            &nbsp;
                            <div v-if="selectedTest.html" :class="'btn btn-pill ' + getPillColor(constants.LOG_ID_HTML)" @click="setPanelHtml()">
                                {{ getHtmlPaneName() }}
                            </div>
                        </div>

                        <div class="col-4 text-right">
                            <div @click="runTest(selectedTest.id)" :class="'btn btn-sm btn-'+(selectedTest.state == 'running' ? 'secondary' : (selectedTest.state == 'queued' ? 'warning' : 'danger'))">
                                <div>
                                    <span v-if="selectedTest.state == 'running'">
                                        <i class="fa fa-cog fa-spin fa-fw"></i> running...
                                    </span>

                                    <span v-if="selectedTest.state == 'queued'">
                                        <span class="fa fa-clock"></span> in queue
                                    </span>

                                    <span v-if="selectedTest.state !== 'running' && selectedTest.state !== 'queued'">
                                        <span class="fa fa-play"></span> run it
                                    </span>
                                </div>
                            </div>

                            <div @click="editFile(selectedTest.edit_file_url)" class="btn btn-sm btn-primary">
                                open in {{ selectedTest.editor_name }}
                            </div>
                        </div>
                    </div>

                    <div :class="'tab-content modal-scroll' + (selectedPanel == constants.LOG_ID_LOG ? ' terminal' : '')  + (selectedPanel == constants.LOG_ID_HTML ? ' html' : '')">
                        <div v-if="selectedPanel == constants.LOG_ID_LOG" v-html="selectedTest.log" :class="'tab-pane terminal ' + (selectedPanel == constants.LOG_ID_LOG ? 'active' : '')">
                        </div>

                        <div v-if="selectedPanel == constants.LOG_ID_SCREENSHOTS" :class="'tab-pane ' + (selectedPanel == constants.LOG_ID_SCREENSHOTS ? 'active' : '')">
                            <div v-for="screenshot in JSON.parse(selectedTest.run.screenshots)" class="text-center">
                                <h3>{{ String(screenshot).substring(screenshot.lastIndexOf('/') + 1) }}</h3>
                                <img :src="makeScreenshot(screenshot)" :alt="screenshot" class="screenshot"/>
                            </div>
                        </div>

                        <div v-if="selectedPanel == constants.LOG_ID_HTML"  v-html="selectedTest.html" :class="'tab-pane ' + (selectedPanel == constants.LOG_ID_HTML ? 'active' : '')">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btn btn-success" data-dismiss="modal">
                        close
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex';
    import {mapMutations} from 'vuex';
    import {mapActions} from 'vuex';
    import {mapGetters} from 'vuex';

    export default {
        computed: {
            ...mapState(['laravel', 'logVisible', 'constants']),

            ...mapGetters(['selectedPanel', 'selectedTest', 'selectedProject']),
        },

        methods: {
            setPanelLog() {
                this.$store.commit('setSelectedPanel', this.constants.LOG_ID_LOG);
            },

            setPanelScreenshot() {
                this.$store.commit('setSelectedPanel', this.constants.LOG_ID_SCREENSHOTS);
            },

            setPanelHtml() {
                this.$store.commit('setSelectedPanel', this.constants.LOG_ID_HTML);
            },

            getPillColor(button) {
                if (button == this.selectedPanel) {
                    return 'btn-primary';
                }

                return 'btn-outline-primary';
            },

            makeScreenshot(screenshot) {
                return this.laravel.url_prefix+'/files/'+btoa(screenshot)+'/download?random='+Math.random();
            },

            baseName(str) {
                let base = String(str).substring(str.lastIndexOf('/') + 1);

                if (base.lastIndexOf(".") != -1) {
                    base = base.substring(0, base.lastIndexOf("."));
                }

                return base;
            },

            getHtmlPaneName() {
                return this.selectedTest.html.match(/snapshot/i)
                    ? this.constants.LOG_ID_SNAPSHOT
                    : this.constants.LOG_ID_HTML;
            },

            runTest(testId) {
                axios.get(this.laravel.url_prefix+'/tests/run/'+testId);
            },

            editFile(file) {
                axios.get(file);
            },
        }
    }
</script>
