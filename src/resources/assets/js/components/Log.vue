<template>
    <div class="modal fade" tabIndex="-1" id="logModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" v-if="selectedTest">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                        </span>
                    </button>
                    <h3>{{ this.selectedTest.name }}</h3>
                </div>

                <div class="modal-body">
                    <div :class="'btn btn-pill ' + this.getPillColor('log')" @click="this.setPanelLog">
                        command output
                    </div>
                    &nbsp;
                    <div :class="'btn btn-pill ' + this.getPillColor('screenshot')" @click="this.setPanelScreenshot">
                        screenshot
                    </div>
                    &nbsp;
                    <div :class="'btn btn-pill ' + this.getPillColor('html')" @click="this.setPanelHtml">
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
        data() {
            return {
                selectedPanel: 'log',
            };
        },

        computed: mapState(['laravel', 'logVisible', 'selectedTest']),

        methods: {
            setPanelLog() {
                this.selectedPanel = 'log';
            },

            setPanelScreenshot() {
                this.selectedPanel = 'screenshot';
            },

            setPanelHtml() {
                this.selectedPanel = 'html';
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
