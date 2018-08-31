/**
 * lodash _
 */

window._ = require('lodash');

/**
 * jQuery & Bootstrap
 */

window.$ = window.jQuery = require('jquery');

window.Popper = require('popper.js');

window.bootstrap = require('bootstrap');

const Pusher = require('pusher-js');

/**
 * Axios
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
