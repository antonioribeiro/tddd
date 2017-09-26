<template>
    <div class="modal fade" tabIndex="-1" id="logModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                    >
                        &times;
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

                    <div class="tab-content modal-scroll">
                        <div v-if="this.selectedPanel == 'log'" v-html="this.selectedTest.log" class="tab-pane terminal active">
                        </div>
                        <div v-if="this.selectedPanel == 'screenshot'" class="tab-pane">
                            <img :src="this.selectedTest.image" alt=""/>
                        </div>
                        <div v-if="this.selectedPanel == 'html'"  v-html="this.selectedTest.html" class="tab-pane">
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
    import {mapMutations} from 'vuex'
    import {mapActions} from 'vuex'

    export default {
        data() {
            return {
                selectedPanel: 'log',
            };
        },

        computed: mapState(['logVisible', 'selectedTest']),

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
            }
        }
    }
</script>
