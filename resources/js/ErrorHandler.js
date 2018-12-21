let errorCount = 0;

function generalJsErrorReport(message, source, lineNo, colno, trace) {
	if (errorCount < 3) {
		errorCount++;
		axios.post('/developer/error/jsError', {
			page: window.location.href,
			userAgent: navigator.userAgent,
			message,
			source,
			lineNo,
			colno,
			trace: trace.stack,
			vm: {
				name: 'js error'
			}
		});
	}
}

function vueErrorReport(err, vm, info) {
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
}

function formatComponentName(vm, includeFile) {
	if (vm.$root === vm) {
		return '<Root>'
	}

	let name = '';

	if (typeof vm === 'string') {
		name = vm;
	} else if (typeof vm === 'function' && vm.options) {
		name = vm.options.name;
	} else if (vm._isVue) {
		name = vm.$options.name || vm.$options._componentTag;
	} else {
		name = vm.name;
	}


	const file = vm._isVue && vm.$options.__file;
	if (!name && file) {
		const match = file.match(/([^/\\]+)\.vue$/);
		name = match && match[1];
	}

	return (
		(name ? `<${classify(name)}>` : `<Anonymous>`) +
		(file && includeFile !== false ? ` at ${file}` : '')
	)
}

function classify(str) {
	return str.replace(/(?:^|[-_])(\w)/g, c => c.toUpperCase())
		.replace(/[-_]/g, '');
}

Vue.config.errorHandler = vueErrorReport;

window.onerror = generalJsErrorReport;
