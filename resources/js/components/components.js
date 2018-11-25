// vendors

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

Vue.component('font-awesome-icon', FontAwesomeIcon);

// our components
Vue.component('Navbar', require('./Global/Navbar'));
Vue.component('FieldListPage', require('./Pages/FieldListPage'));

// utilities
Vue.component('Drawer', require('./Utilities/Drawer'));
Vue.component('ListSection', require('./Utilities/ListSection'));
Vue.component('Datatable', require('./Utilities/Datatable/Datatable'));
Vue.component('ModalComponent', require('./Utilities/ModalComponent'));
Vue.component('DraggableFieldList', require('./Utilities/DraggableFieldList'));
Vue.component('Tabs', require('./Utilities/Tabs/Tabs'));
Vue.component('Tab', require('./Utilities/Tabs/Tab'));
Vue.component('Carousel', require('./Utilities/Carousel'));
Vue.component('SelectChooser', require('./Utilities/SelectChooser/SelectChooser'));
Vue.component('SelectView', require('./Utilities/SelectChooser/SelectView'));
Vue.component('DynamicTable', require('./Utilities/DynamicTable'));
Vue.component('StepsForm', require('./Utilities/StepsForm/StepsForm'));
Vue.component('FromStep', require('./Utilities/StepsForm/FormStep'));
Vue.component('ImageManager', require('./Utilities/ImageManager/ImageManager'));


// form
Vue.component('AjaxForm', require('./Form/AjaxForm'));
Vue.component('ModalForm', require('./Form/ModalForm'));
Vue.component('DynamicForm', require('./Form/DynamicForm'));
Vue.component('DynamicFields', require('./Form/DynamicFields'));
Vue.component('FieldForm', require('./Form/FieldForm'));
Vue.component('TextField', require('./Form/TextField'));
Vue.component('TextareaField', require('./Form/TextareatField'));
Vue.component('SelectField', require('./Form/SelectField'));
