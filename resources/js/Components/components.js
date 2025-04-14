// vendors

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

export default function(app){
    app.component('font-awesome-icon', FontAwesomeIcon);

// our components
    app.component('Navbar', require('./Global/Navbar').default);
    app.component('toast', require('./Global/Toast').default);
    app.component('FireworksModal', require('./Global/FireworksModal').default);
    app.component('ConfirmationSubmit', require('./Global/ConfirmationSubmit').default);
    app.component('InvoicePaymentsModal', require('./Invoice/InvoicePaymentsModal').default);
    app.component('ManageShiftWorkers', require('./Shift/ManageShiftWorkers').default);
    app.component('ExportWorkedHours', require('./Shift/ExportWorkedHours').default);
    app.component('Schedule', require('./Schedule/Schedule').default);
    app.component('ApproveSchedule', require('./Band/ApproveSchedule').default);
    app.component('BandPdfForm', require('./Band/BandPdfForm').default);
    app.component('BandSetlistForm', require('./Band/BandSetlistForm').default);
    app.component('ServicesForm', require('./Admin/ServicesForm').default);
    app.component('LinkDateSelector', require('./Global/LinkDateSelector').default);
    app.component('FeeCalculator', require('./Kitchen/FeeCalculator').default);


// utilities
    app.component('Drawer', require('./Utilities/Drawer').default);
    app.component('ListSection', require('./Utilities/ListSection').default);
    app.component('Datatable', require('./Utilities/Datatable/Datatable').default);
    app.component('ModalComponent', require('./Utilities/ModalComponent').default);
    app.component('Tabs', require('./Utilities/Tabs/Tabs').default);
    app.component('Tab', require('./Utilities/Tabs/Tab').default);
    app.component('Carousel', require('./Utilities/Carousel').default);
    app.component('SelectChooser', require('./Utilities/SelectChooser/SelectChooser').default);
    app.component('SelectView', require('./Utilities/SelectChooser/SelectView').default);
    app.component('DynamicTable', require('./Utilities/DynamicTable').default);
    app.component('StepsForm', require('./Utilities/StepsForm/StepsForm').default);
    app.component('FromStep', require('./Utilities/StepsForm/FormStep').default);
    app.component('ImageManager', require('./Utilities/ImageManager/ImageManager').default);
    app.component('Calendar', require('./Utilities/Calendar/Calendar').default);
    app.component('CalendarEntry', require('./Utilities/Calendar/CalendarEntry').default);
    app.component('CalendarModal', require('./Utilities/Calendar/CalendarModal').default);
    app.component('CalendarScheduleDisplay', require('./Utilities/Calendar/CalendarScheduleDisplay').default);
    app.component('LogoForm', require('./Utilities/LogoForm').default)
    app.component('Tooltip', require('./Utilities/Tooltip').default)

// form
    app.component('AjaxForm', require('./Form/AjaxForm').default);
    app.component('DynamicForm', require('./Form/DynamicForm').default);
    app.component('DynamicFields', require('./Form/DynamicFields').default);
    app.component('TextField', require('./Form/TextField').default);
    app.component('TextareaField', require('./Form/TextareatField').default);
    app.component('SelectField', require('./Form/SelectField').default);
    app.component('FileField', require('./Form/FileField').default);
    app.component('CheckboxField', require('./Form/CheckboxField').default);
    app.component('InvoiceField', require('./Form/InvoiceField').default);
    app.component('AlternativeSubmitField', require('./Form/AlternativeSubmitField').default);
    app.component('JsonField', require('./Form/JsonField').default);
    app.component('HelpField', require('./Form/HelpField').default);
    app.component('MultiselectField', require('./Form/MultiselectField').default);
    app.component('CheckboxPopupField', require('./Form/CheckboxPopupField').default);
    app.component('CheckedInfoForm', require('./Form/CheckedInfoForm').default);
    app.component('ConditionalField', require('./Form/ConditionalField').default);
    app.component('SendWorkerUpdateInfoEmail', require('./Admin/SendWorkerUpdateInfoEmail').default)

}

