// vendors

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

Vue.component('font-awesome-icon', FontAwesomeIcon);

// our components
Vue.component('Navbar', require('./Global/Navbar').default);
Vue.component('toast', require('./Global/Toast').default);
Vue.component('FireworksModal', require('./Global/FireworksModal').default);
Vue.component('ConfirmationSubmit', require('./Global/ConfirmationSubmit').default);
Vue.component('InvoicePaymentsModal', require('./Invoice/InvoicePaymentsModal').default);
Vue.component('ManageShiftWorkers', require('./Shift/ManageShiftWorkers').default);
Vue.component('ExportWorkedHours', require('./Shift/ExportWorkedHours').default);
Vue.component('Schedule', require('./Schedule/Schedule').default);
Vue.component('ApproveSchedule', require('./Band/ApproveSchedule').default);
Vue.component('BandPdfForm', require('./Band/BandPdfForm').default);

// utilities
Vue.component('Drawer', require('./Utilities/Drawer').default);
Vue.component('ListSection', require('./Utilities/ListSection').default);
Vue.component('Datatable', require('./Utilities/Datatable/Datatable').default);
Vue.component('ModalComponent', require('./Utilities/ModalComponent').default);
Vue.component('Tabs', require('./Utilities/Tabs/Tabs').default);
Vue.component('Tab', require('./Utilities/Tabs/Tab').default);
Vue.component('Carousel', require('./Utilities/Carousel').default);
Vue.component('SelectChooser', require('./Utilities/SelectChooser/SelectChooser').default);
Vue.component('SelectView', require('./Utilities/SelectChooser/SelectView').default);
Vue.component('DynamicTable', require('./Utilities/DynamicTable').default);
Vue.component('StepsForm', require('./Utilities/StepsForm/StepsForm').default);
Vue.component('FromStep', require('./Utilities/StepsForm/FormStep').default);
Vue.component('ImageManager', require('./Utilities/ImageManager/ImageManager').default);
Vue.component('Calendar', require('./Utilities/Calendar/Calendar').default);
Vue.component('CalendarEntry', require('./Utilities/Calendar/CalendarEntry').default);
Vue.component('CalendarModal', require('./Utilities/Calendar/CalendarModal').default);
Vue.component('CalendarScheduleDisplay', require('./Utilities/Calendar/CalendarScheduleDisplay').default);


// form
Vue.component('AjaxForm', require('./Form/AjaxForm').default);
Vue.component('DynamicForm', require('./Form/DynamicForm').default);
Vue.component('DynamicFields', require('./Form/DynamicFields').default);
Vue.component('TextField', require('./Form/TextField').default);
Vue.component('TextareaField', require('./Form/TextareatField').default);
Vue.component('SelectField', require('./Form/SelectField').default);
Vue.component('FileField', require('./Form/FileField').default);
Vue.component('CheckboxField', require('./Form/CheckboxField').default);
Vue.component('InvoiceField', require('./Form/InvoiceField').default);
Vue.component('AlternativeSubmitField', require('./Form/AlternativeSubmitField').default);
Vue.component('JsonField', require('./Form/JsonField').default);
Vue.component('HelpField', require('./Form/HelpField').default);
Vue.component('MultiselectField', require('./Form/MultiselectField').default);
