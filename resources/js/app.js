/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import {generalJsErrorReport, vueErrorReport} from "./ErrorHandler";

require('./bootstrap');

import {createApp} from 'vue'
import VueIziToast from './Classes/VueIzitoast';
import {library} from '@fortawesome/fontawesome-svg-core'

import {
    faLink,
    faSignOutAlt,
    faBars,
    faFileUpload,
    faTimesCircle,
    faEuroSign,
    faExternalLinkSquareAlt,
    faInfoCircle
} from '@fortawesome/free-solid-svg-icons'


library.add(faLink, faSignOutAlt, faBars, faFileUpload, faTimesCircle, faEuroSign, faExternalLinkSquareAlt, faInfoCircle);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import componentInstaller from './Components/components';

window.onerror = generalJsErrorReport;

const app = createApp({
    data() {
        return {
            drawerOpen: false,
        }
    }
}).use(VueIziToast);

app.config.compilerOptions.whitespace = 'preserve'
app.config.globalProperties.$translations = window.$translations;

componentInstaller(app);

app.config.errorHandler = vueErrorReport;

app.mount('#app');
