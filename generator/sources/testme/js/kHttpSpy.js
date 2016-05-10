
function kActionRequest(){
	this.inputs = {};
}

kActionRequest.prototype = {
	className: 'kActionRequest',
	service: null,
	action: null,
	inputs: null
};


var kHttpSpy = {
	fileReader: null,
	
	init: function(){
		
		if (typeof window.FileReader !== 'function') {
            return;
        }
		
		jqMenuItem = jQuery('<li class="code-menu"><a class="code-menu-toggle" href="#" id="httpSpyToggle">Show HTTP Spy</a></li>');
		jQuery('#codeSubMenu').append(jqMenuItem);
		
		jqMenuItem.click(kHttpSpy.toggleView);
		jQuery('#parseHttpSpy').click(kHttpSpy.parse);
	},
	
	toggleView: function(){

		$('#httpSpy').toggle();
		if($('#httpSpyToggle').html() == 'Hide Code Example'){
			$('#httpSpyToggle').html('Show Code Example');
		}
		else{
			$('#httpSpyToggle').html('Hide Code Example');
		}
		kTestMe.calculateDimensions(1);
		kTestMe.jqWindow.resize();
	},

	parse: function(){

        var input = document.getElementById('fileHttpSpy');
        if (!input.files) {
            alert("This browser doesn't seem to support the `files` property of file inputs.");
            return;
        }

        if (!input.files[0]) {
            alert("Please select a file to parse");
            return;
        }

        var file = input.files[0];
        kHttpSpy.fileReader = new FileReader();
        kHttpSpy.fileReader.onload = kHttpSpy.parseText;
        kHttpSpy.fileReader.readAsText(file);
	},

	parseText: function(){
		var har;
		try{
			eval('har = ' + kHttpSpy.fileReader.result);
		}
		catch(e){
			alert(e.message);
			return;
		}
		
		if(
				(typeof har != 'object') ||
				(typeof har.log != 'object') ||
				(typeof har.log.entries != 'object')
		){
			alert('Invalid log file format');
			return;
		}
		
		if(!har.log.entries.length){
			alert('No requests found in the log file');
			return;
		}
		
		var actionRequest = kHttpSpy.parseRequest(har.log.entries[0].request);
		kTestMe.call.loadRequest(actionRequest);
	},
	
	/**
	 * @param Object request json as parsed from the har file
	 * @return kActionRequest
	 */
	parseRequest: function(request){
		var actionRequest = new kActionRequest();

		kHttpSpy.parseData(actionRequest, request.queryString);
		kHttpSpy.parseData(actionRequest, request.postData);
		
		return actionRequest;
	},
	
	/**
	 * @param kActionRequest
	 * @param Object request json as parsed from the har file
	 */
	parseData: function(actionRequest, array){
		
		// TODO support multi request
		for(var i = 0; i < array.length; i++){
			var fieldName = array[i].name;
			switch(fieldName){
				case 'service':
					actionRequest.service = array[i].value;
					break;

				case 'action':
					actionRequest.action = array[i].value;
					break;

				default:
					actionRequest.inputs[fieldName] = array[i].value; // TODO support internal objects airarchy
					break;
			}
		}
	}
};

/**
 * @param kActionRequest actionRequest
 */
kMainCall.prototype.loadRequest = function(actionRequest){
	this.setAction(actionRequest.service, actionRequest.action, true);
	this.loadValues(actionRequest.inputs); // TODO it's not working - check why
	
	// TODO - support multi request
};

jQuery(function(){
	kHttpSpy.init();
});

