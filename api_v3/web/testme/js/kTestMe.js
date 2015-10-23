
function delegate (/*Object*/ scope, /*Function*/ method, /* Array */ attributes) {
	if(!method)
		throw "Method is not defined.";
	
	var f = function () {
		return method.apply (scope, (attributes ? attributes : arguments));
	};
	return f;
}


var kTestMe = {

	autoIncrementId: 1,
	
	services: {},
	classes: {},
	
	height: null,
	testmeHeight: null,
	responseWidth: null,

	jqObjectsContainer: null,
	jqResponse: null,
	jqWindow: null,
	jqHistory: null,
	history: new Array(),

	call: null,
	codeGenerator: null,
	
	log: {
		lastLog: null,
		
		format: function(message){

			var err = new Error();
			var caller_line = '';
			if(err.stack){
				caller_line = err.stack.split("\n")[2];
				caller_line = caller_line.substr(caller_line.indexOf('/') + 2); // remove at http://
				caller_line = caller_line.substr(caller_line.indexOf('/')); // remove domain
			}
			
			var time = '';
			var d = new Date();
			var millis = d.getMilliseconds();
			if(this.lastLog){
				var diff = millis - this.lastLog;
				time = ' [' + diff + ']';
			}
			this.lastLog = millis;
			
			return caller_line + time + ': ' + message;
		},
		
		log: function(message){
			if(window.console && window.console.log)
				window.console.log(this.format(message));
		},
		
		debug: function(message){
			if(!window.console)
				return;

			if(window.console.debug)
				window.console.debug(this.format(message));
			else if(window.console.log)
				window.console.log(this.format(message));
		},
		
		info: function(message){
			if(!window.console)
				return;

			if(window.console.info)
				window.console.info(this.format(message));
			else if(window.console.log)
				window.console.log(this.format(message));
		},
		
		warn: function(message){
			if(!window.console)
				return;

			if(window.console.warn)
				window.console.warn(this.format(message));
			else if(window.console.log)
				window.console.log(this.format(message));
		},
		
		error: function(message){
			if(!window.console)
				return;

			if(window.console.error)
				window.console.error(this.format(message));
			else if(window.console.log)
				window.console.log(this.format(message));
		}
	},
	
	init: function(id){
		this.jqResponse = jQuery('#response');
		this.jqWindow = jQuery(window);
		this.jqObjectsContainer = jQuery('.objects-containter');
		this.jqHistory = jQuery('#history');

		this.jqHistory.change(delegate(this, this.onHistoryLoad));
		this.jqResponse.load(delegate(this, this.onResponse));
		this.jqWindow.resize(delegate(this, this.onResize));
		jQuery('#request').submit(delegate(this, this.onSubmit));
		
		this.call = new kMainCall(id);
		this.call.getUnLockedServiceId();
		this.call.getUnLockedActionId();
		
		this.calculateDimensions();
		this.jqWindow.resize();
		
		this.initCodeExample();
		
		this.log.debug('Testme initialized.');
	},
	
	initCodeExample: function(generator) {
		if(!generator)
			generator = new KCodeExamplePHP(jQuery("#example"));
		
		this.codeGenerator = generator;
		this.call.ready(delegate(generator, generator.codeAction, [this.call]));
		this.codeGenerator.codeAction(this.call);
	},
	
	appendDialog: function(dialog) {
		this.jqObjectsContainer.append(dialog.jqElement);
	},

	getKSFromTextByFormat: function(responseText, format){
		const INVALID ="";
		switch (format)	{
			case 'xml' :
				var isThereError = responseText.indexOf('<error>') != -1;
				if (!isThereError) {
					var startResult = responseText.indexOf('<result>');
					var endResult = responseText.indexOf('</result>');
					if (startResult > -1 && endResult > -1) {
						return responseText.substring(startResult + 8, endResult);
					}
				}
				break;
			case 'json' :
				var isThereError = responseText.indexOf("{") != -1;
				if (!isThereError) {
					return responseText.slice(1, -1);
				}
				break;
			default :
				break;
		}
		return INVALID;
	},

	onResponse: function(responseText, format) {
		
		if ((this.call.getServiceId() == 'session') && (this.call.getActionId() == 'start')) {

			var ksText = this.getKSFromTextByFormat(responseText, format);
			var field = jQuery('#ks');
			if(!field || !field.size()){
				kTestMe.log.error('KS field not found.');
				return;
			}
			
			if (ksText.length > 0){
				field.val(ksText);
			}
			// if not empty, empty it
			else if (field.val()){
				field.val('');
			}
			field.click();
		}
		
		if(this.call != null){
			var historyLength = this.history.length;
			this.history.push(this.call.getRequest(historyLength));
			var title = this.history.length + '. ' + this.call.getTitle();
			this.jqHistory.append('<option value="' + historyLength + '">' + title + '</option>');
		}
	},
	
	onSubmit: function() {
		this.log.debug('Request submitted.');
	},
	
	onHistoryLoad: function() {
		var index = this.jqHistory.val();
		if(isNaN(index))
			return;
		
		this.call.setRequest(this.history[index]);
		this.codeGenerator.codeAction(this.call);
		this.log.debug('History Loaded.');
	},
	
	onResize: function() {
		this.calculateDimensions();
		jQuery(".object-properties").css("height", this.testmeHeight - 30);
		jQuery(".left").css("height", this.testmeHeight); // for margin
		jQuery(".right").css("height", this.testmeHeight); // for margin
		jQuery(".right").css("width", this.responseWidth);
	},

	registerService: function(serviceId, serviceName, servicePackage, deprecated){
		this.services[serviceId] = {
			id: serviceId, 
			name: serviceName, 
			packageName: servicePackage, 
			deprecated: deprecated, 
			actions: {
				length: 0
			}
		};
		this.log.info('Service [' + serviceId + '] registered.');
	},
	
	registerAction: function(serviceId, actionId, actionName, actionLabel){
		if(!this.services[serviceId]){
			this.log.error('[kTestMe::registerAction] Service id [' + serviceId + '] not found.');
			return;
		}
		
		this.services[serviceId].actions[actionId] = {
			id: actionId, 
			name: actionName, 
			label: actionLabel,
			params: new Array(),
			paramsLoaded: false
		};
		this.services[serviceId].actions.length++;
		this.log.info('Action [' + serviceId + '.' + actionId + '] registered.');
	},
	
	registerActionParam: function(serviceId, actionId, param){
		if(!this.serviceActionsLoaded(serviceId, actionId)){
			this.log.error('[kTestMe::registerActionParam] Action [' + serviceId + '.' + actionId + '] not found');
			return;
		}

		this.services[serviceId].actions[actionId].params.push(param);
		this.services[serviceId].actions[actionId].paramsLoaded = true;
		this.log.info('Action [' + serviceId + '.' + actionId + '] Parameter [' + param.name + '] registered.');
	},
	
	registerClass: function(param){
		if(this.classes[param.type])
			return;
		
		this.classes[param.type] = param;
		this.log.info('Class [' + param.type + '] registered.');
	},
	
	registerSubClasses: function(type, subTypes){
		if(!this.classLoaded(type)){
			this.log.error('[kTestMe::registerSubClasses] Type [' + type + '] not found');
			return;
		}
		
		this.classes[type].subClasses = subTypes;
		for(var i = 0; i < subTypes.length; i++)
			this.registerClass(subTypes[i]);
		
		this.log.info('Class [' + type + '] sub-classes [' + subTypes.length + '] registered.');
	},
	
	serviceActionsLoaded: function(serviceId){
		return(this.services[serviceId] && this.services[serviceId].actions.length > 0);
	},
	
	actionParamsLoaded: function(serviceId, actionId){
		return(
				this.serviceActionsLoaded(serviceId) && 
				this.services[serviceId].actions[actionId] && 
				this.services[serviceId].actions[actionId].paramsLoaded);
	},
	
	classLoaded: function(type){
		return(this.classes[type] ? true : false);
	},
	
	subClassesLoaded: function(type){
		return(this.classes[type] && this.classes[type].subClasses);
	},
	
	getClass: function(type){
		if(this.classLoaded(type))
			return this.classes[type];
			
		return null;
	},
	
	getSubClasses: function(type){
		if(this.subClassesLoaded(type))
			return this.classes[type].subClasses;
			
		return {};
	},
	
	getServices: function(){
		return this.services;
	},
	
	getServiceActions: function(serviceId){
		if(this.serviceActionsLoaded(serviceId))
			return this.services[serviceId].actions;
			
		return {};
	},
	
	getActionParams: function(serviceId, actionId){
		if(this.actionParamsLoaded(serviceId, actionId))
			return this.services[serviceId].actions[actionId].params;
			
		return new Array();
	},
	
	calculateDimensions: function() {
		this.height = jQuery("body").innerHeight();
		if (jQuery("#kmcSubMenu").size() == 0) // when displayed in admin console without the menu
			this.height = this.height - 10;
		else
			this.height = this.height - jQuery("#kmcSubMenu").outerHeight() - 44; 
		
		this.testmeHeight = this.height - jQuery('#codeSubMenu').outerHeight(true);
		if (jQuery('#codeExample').is(':visible'))
			this.testmeHeight -= jQuery('#codeExample').outerHeight(true);

		if (jQuery('#httpSpy').is(':visible'))
			this.testmeHeight -= jQuery('#httpSpy').outerHeight(true);
		
		var leftBoxWidth = jQuery(".left").outerWidth();
		
		var leftBoxRightMargin = jQuery(".left").css("margin-right").replace("px", "");
		leftBoxRightMargin = Number(leftBoxRightMargin);
		
		var leftBoxLeftMargin = jQuery(".left").css("margin-left").replace("px", "");
		leftBoxLeftMargin = Number(leftBoxLeftMargin);
		this.responseWidth = jQuery("body").innerWidth() - leftBoxWidth - leftBoxLeftMargin - leftBoxRightMargin - 21;

		this.log.debug('Dimensions recalculated.');
	}
};


jQuery(function(){
	kTestMe.init("dvService");
});