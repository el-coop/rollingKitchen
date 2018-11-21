// vendors

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
Vue.component('font-awesome-icon', FontAwesomeIcon);

// our components

Vue.component('Drawer', require('./Utilities/Drawer'));
Vue.component('ListSection', require('./Utilities/ListSection'));
Vue.component('Datatable', require('./Utilities/Datatable/Datatable'));
Vue.component('Navbar', require('./Global/Navbar'));
Vue.component('ModalComponent', require('./Utilities/ModalComponent'));
Vue.component('ModalForm', require('./Form/ModalForm'));
Vue.component('DynamicForm', require('./Form/DynamicForm'));
Vue.component('FieldForm', require('./Form/FieldForm'));
Vue.component('FieldListPage', require('./Pages/FieldListPage'));
Vue.component('DraggableFieldList', require('./Utilities/DraggableFieldList'));
Vue.component('AjaxForm', require('./Form/AjaxForm'));
Vue.component('Tabs', require('./Utilities/Tabs'));
Vue.component('Tab', require('./Utilities/Tabs/Tab'));
Vue.component('Carousel', require('./Utilities/Carousel'));
Vue.component('SelectChooser', require('./Utilities/SelectChooser'));
Vue.component('SelectView', require('./Utilities/SelectChooser/SelectView'));
Vue.component('DynamicTable', require('./Utilities/DynamicTable'));
