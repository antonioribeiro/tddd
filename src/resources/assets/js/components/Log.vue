<template>
    <div class="modal fade" tabIndex="-1" id="logModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" v-if="selectedTest">
                <div class="modal-header">
                    <h3 class="modal-title">{{ this.selectedTest.name }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                        </span>
                    </button>
                </div>

                <div class="modal-body">
                    <div v-if="selectedTest.log" :class="'btn btn-pill ' + this.getPillColor('log')" @click="this.setPanelLog">
                        command output
                    </div>
                    &nbsp;
                    <div v-if="selectedTest.run.screenshots"  :class="'btn btn-pill ' + this.getPillColor('screenshot')" @click="this.setPanelScreenshot">
                        screenshots
                    </div>
                    &nbsp;
                    <div v-if="selectedTest.html" :class="'btn btn-pill ' + this.getPillColor('html')" @click="this.setPanelHtml">
                        html
                    </div>

                    <div :class="'tab-content modal-scroll ' + (this.selectedPanel == 'log' ? 'terminal' : '')">
                        <div v-if="selectedPanel == 'log'" v-html="selectedTest.log" :class="'tab-pane terminal ' + (selectedPanel == 'log' ? 'active' : '')">
                        </div>

                        <div v-if="selectedPanel == 'screenshot'" :class="'tab-pane ' + (selectedPanel == 'screenshot' ? 'active' : '')">
                            <div v-for="screenshot in JSON.parse(selectedTest.run.screenshots)" class="text-center">
                                <h3>{{ String(screenshot).substring(screenshot.lastIndexOf('/') + 1) }}</h3>
                                <img :src="makeScreenshot(screenshot)" :alt="screenshot" class="screenshot"/>
                            </div>
                        </div>

                        <div v-if="selectedPanel == 'html'"  v-html="selectedTest.html" :class="'tab-pane ' + (selectedPanel == 'html' ? 'active' : '')">
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

    export default {
        computed: mapState(['laravel', 'logVisible', 'selectedTest', 'selectedPanel']),

        methods: {
            setPanelLog() {
                this.$store.commit('setSelectedPanel', 'log');
            },

            setPanelScreenshot() {
                this.$store.commit('setSelectedPanel', 'screenshot');
            },

            setPanelHtml() {
                this.$store.commit('setSelectedPanel', 'html');
            },

            getPillColor(button) {
                if (button == this.selectedPanel) {
                    return 'btn-primary';
                }

                return 'btn-outline-primary';
            },

            makeScreenshot(screenshot) {
                return this.laravel.url_prefix+'/image/download/'+btoa(screenshot);
            },

            baseName(str) {
                var base = new String(str).substring(str.lastIndexOf('/') + 1);

                if (base.lastIndexOf(".") != -1) {
                    base = base.substring(0, base.lastIndexOf("."));
                }

                return base;
            }
        }
    }
</script>
