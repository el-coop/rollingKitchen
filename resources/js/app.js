/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VModal from 'vue-js-modal';
import 'izitoast/dist/css/iziToast.css';
import VueIziToast from 'vue-izitoast';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faLink, faSignOutAlt, faBars, faFileUpload, faTimesCircle } from '@fortawesome/free-solid-svg-icons'

library.add(faLink, faSignOutAlt, faBars, faFileUpload, faTimesCircle);

Vue.use(VModal);
Vue.use(VueIziToast);
Vue.prototype.$translations = window.translations;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
require('./Components/components');

Vue.prototype.$bus = new Vue();


const app = new Vue({
	el: '#app'
});

