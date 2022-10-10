/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./ErrorHandler');

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
    faExternalLinkSquareAlt
} from '@fortawesome/free-solid-svg-icons'


library.add(faLink, faSignOutAlt, faBars, faFileUpload, faTimesCircle, faEuroSign, faExternalLinkSquareAlt);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import componentInstaller from './Components/components';


const app = createApp({})
    .use(VueIziToast)
    .provide('$translations', window.translations);

app.config.compilerOptions.whitespace = 'preserve'

componentInstaller(app);

app.config.errorHandler = function (err, vm, info) {
    if (process.env.MIX_APP_ENV === 'local') {
        console.log(err);
        return;
    }
    axios.post('/developer/error/jsError', {
        page: window.location.href,
        userAgent: navigator.userAgent,
        message: `Error in ${info}: "${err.toString()}" - ${formatComponentName(vm)}`,
        source: err.fileName,
        lineNo: err.lineNumber,
        colNo: err.colNumber,
        trace: err.stack,
        vm: {
            props: vm.$options.propsData,
            data: vm._data
        }
    })
};

app.mount('#app');
