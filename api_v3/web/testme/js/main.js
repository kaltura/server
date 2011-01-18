function delegate (/*Object*/ scope, /*Function*/ method ) {
	var f = function () {
		return method.apply (scope, arguments);
	};
	return f;
}

KTestMe = function() {
	this.bindToElements();
	this.registerHandlers();
	
	this.jqActions.attr("disabled", true);
	this.jqServices.change();
	
	this.history = new Array();
	
	this.historyItem = null;
	
	this.calculateDimensions();
	this.jqWindow.resize();

	this.initCodeExample();
};

KTestMe.prototype = {

	height: null,
	testmeHeight: null,
	resultWidth: null,

	jqActions: null,
	jqServices: null,
	jqActionParams: null,
	jqObjectsContainer: null,
	jqKs: null,
	jqSend: null,
	jqHistory: null,
	jqResultIframe: null,
	jqWindow: null,
	
	historyItem: null,
	codeGenerator: null,
	
	actionInfo: null,
		
	bindToElements: function() {
		this.jqActions = jQuery("select[name=action]");
		this.jqServices = jQuery("select[name=service]");
		this.jqActionParams = jQuery("#action-params");
		this.jqObjectsContainer = jQuery("#objects-containter");
		this.jqKs = jQuery("input[name=ks]");
		this.jqSend = jQuery("#send");
		this.jqHistory = jQuery("select[name=history]");
		this.jqResultIframe = jQuery("#result");
		this.jqWindow = jQuery(window);
	},
	
	registerHandlers: function() {
		this.jqServices.change(delegate(this, this.onServiceChangeHandler));
		this.jqActions.change(delegate(this, this.onActionChangeHandler));
		this.jqSend.click(delegate(this, this.onSendClickHandler));
		this.jqHistory.change(delegate(this, this.onHistoryClickHandler));
		this.jqResultIframe.load(delegate(this, this.onResultIframeLoadHandler));
		this.jqWindow.resize(delegate(this, this.onWindowResizeHandler));
	},
	
	onServiceChangeHandler: function(e) {
		if (!e.target)
			return;
		
		this.jqActions.attr("disabled", true);
		this.jqActions.empty();
		this.jqActions.append("<option>Loading...</option>");

		jQuery.getJSON(
			"ajax-get-actions.php", 
			{ "service": jQuery(e.target).val() }, 
			delegate(this, this.onActionsGetSuccessHandler)
		);
	},
	
	onActionsGetSuccessHandler: function (data) {
		this.jqActions.empty();
		this.jqActions.attr("disabled", false);
		jQuery.each(data, delegate(this, function (i, item) {
			this.jqActions.append("<option>"+item+"</option>");
		}));
		
		if (!this.historyItem)
			this.jqActions.change();
		else
			this.loadHistoryAction();
	},
	
	onActionChangeHandler: function(e) {
		if (!e.target)
			return;
		
		var action = jQuery(e.target).val();
		var service = jQuery("select[name=service]").val();
		
		jQuery("#action-params").empty();
		
		jQuery.getJSON(
			"ajax-get-action-info.php",
			{ "service": service, "action": action },
			delegate(this, this.onGetActionInfoSuccess)
		);
	},
	
	onGetActionInfoSuccess: function(data) {
		this.actionInfo = data;
		jQuery("#actionHelp").attr("title", this.jqServices.val() + "." + this.jqActions.val() + " - " + data.description);
		this.jqObjectsContainer.empty();
		jQuery.each(data.actionParams, delegate(this, function (i, param) {
			if (param.isComplexType)
			{
				if (param.isEnum || param.isStringEnum)
					this.addEnumField(this.jqActionParams, param);
				else if (param.isArray)
					this.addArrayField(this.jqActionParams, param);
				else if (param.isFile)
					this.addFileField(this.jqActionParams, param);
				else
					this.addObjectField(this.jqActionParams, param);
			}
			else
			{
				this.addSimpleField(this.jqActionParams, param);
			}
		}));
		
		jQuery(".help").tooltip({showURL: false, delay: 0, extraClass: "helpTooltip", showBody: " - "});
		
		if (this.historyItem)
		{
			this.loadHistoryData();
		}

		if(this.codeGenerator){
			var service = this.jqServices.find("option:selected").text();
			var action = this.jqActions.find("option:selected").text();
			
			this.codeGenerator.setAction(service, action, data.actionParams);
		}
	},
	
	onSendClickHandler: function(e) {
		this.hideOpenObjectProperties();
		
		// find all the enabled fields
		var params = [];
		var objectTypes = [];
		
		jQuery(".object").each(function(i, item) {
			if (jQuery(item).find(".object-name").size() > 0 && jQuery(item).find(".object-type").size() > 0)
			{
				var name = jQuery.trim(jQuery(item).find(".object-name").text());
				var value = jQuery(item).find(".object-type").val();

				objectTypes[name] = value;
			}
		});

		jQuery(".param").each(function(i, item) {
			if (jQuery(item).find("input:checkbox:checked").size() > 0)
			{
				var name = jQuery(item).find("input:text,select").attr("name");
				if(!name)
					return;
				
				var value = jQuery(item).find("input:text,select").val();
				params[name] = value;
				
				var lastIndex = name.lastIndexOf(":");
				if(lastIndex > 0)
				{
					var objectName = name.substr(0, lastIndex);
					if(objectTypes[objectName])
						params[objectName + ":objectType"] = objectTypes[objectName];
				}
			}
		});
		
		// append all the enabled fields to the form
		jQuery("form").empty();
		for(var prop in params) {
			var jqHiddenField = jQuery("<input type=\"hidden\" name=\""+prop+"\" />");
			jqHiddenField.val(params[prop]);
			jQuery("form").append(jqHiddenField);
		}
		
		// copy the file fields to the form
		jQuery("input:file").clone().appendTo(jQuery("form")).hide();
		
		
		var service = jQuery("select[name=service]").val();
		var action = jQuery("select[name=action]").val();
		if (jQuery("input:file").size() > 0)
			jQuery("form").attr("enctype", "multipart/form-data");
		else
			jQuery("form").attr("enctype", null);
			
		jQuery("form")
			.attr("action", "../index.php?service="+service+"&action="+action)
			.submit();
		
		this.saveToHistory();
	},
	
	onResultIframeLoadHandler: function() {
		if ((this.jqServices.val() == "session") && (this.jqActions.val() == "start")) {
			var iframeDoc = jQuery("#result")[0].contentWindow.document;
			var xmlDoc = (iframeDoc.XMLDocument) ? iframeDoc.XMLDocument : iframeDoc.documentElement;
			var result = jQuery(xmlDoc).find("result");
			if (result.size() && !result.find("error").size())
			{
				this.jqKs
					.val(result.text())
					.effect("highlight", {}, 1000)
					.parent().find("input:checkbox").attr("checked", true);
			}
			else if (this.jqKs.val()) // if not empty, empty it
			{
				this.jqKs
					.val("")
					.effect("highlight", {}, 1000)
					.parent().find("input:checkbox").attr("checked", false);
			}
		}
	},
	
	onWindowResizeHandler: function() {
		this.calculateDimensions();
		jQuery(".object-properties").css("height", this.testmeHeight);
		jQuery(".testme").css("height", this.testmeHeight); // for margin
		jQuery(".right").css("width", this.resultWidth);
	},
	
	addObjectField: function(/*jQuery*/ container, param, inArray) {
		
		var jqObjectType = jQuery("<select id=\"object-type-" + param.name.replace(/:/g, "_") + "\" class=\"object-type\">");
		jqObjectType.append("<option>" + param.type + "</option>");
		
		var scope = this;
		var objectTypesProps = new Object();
		
		var jqObjectProperties = jQuery("<div class=\"object-properties\">");
		jqObjectProperties.attr("id", "object-props-" + param.name + "-" + param.type);
		objectTypesProps[param.type] = jqObjectProperties;

		jQuery.each(param.properties, delegate(this, function (i, property) {
			
			if (property.isReadOnly)
				return;

			property.name = param.name + ":" + property.name;
			
			if (property.isEnum || property.isStringEnum)
				scope.addEnumField(jqObjectProperties, property);
			else if (property.isArray)
				scope.addArrayField(jqObjectProperties, property);
			else if (!property.isComplexType)
				scope.addSimpleField(jqObjectProperties, property);
			else
				scope.addObjectField(jqObjectProperties, property);
		}));
		
		var jqObject = jQuery("<div class=\"object\">");
		jqObject.attr("id", "object-" + param.name);
		var jqObjectTitle = jQuery("<div>");
		var jqObjectName = jQuery("<span class=\"object-name\">temp</span>"); // temp required by IE
		jqObjectName.html(param.name + " ");
		var jqEdit = jQuery("<button class=\"edit-button\">Edit</button>");
		
		jqObjectTitle.append(jqObjectName);
		jqObjectTitle.append(jqEdit);
		jqObjectTitle.append(jqObjectType);
		
		this.jqObjectsContainer.append(jqObjectProperties);
		
		var propsPoper = {
				currentPropsWindow: jqObjectProperties,
				click: function(e){
					var objectPropsId = propsPoper.currentPropsWindow.attr('id');
					var splitId = objectPropsId.split(":");
					var count = splitId.length;

					if(inArray)
					{
						for(var i = 0; i < splitId.length; i++)
							if(!isNaN(splitId[i][0]))
								count--;
					}
					
					propsPoper.currentPropsWindow
						.css("height", this.testmeHeight)
						.css("left", count * 300)
						.toggle();
				},
				change: function(e){
					propsPoper.remove();

					propsPoper.currentPropsWindow = objectTypesProps[jqObjectType.val()];
					scope.jqObjectsContainer.append(propsPoper.currentPropsWindow);
					
					if(scope.codeGenerator)
					{
						scope.codeGenerator.setChangeEvent();
						scope.codeGenerator.onParamsChange();
					}
				},
				remove: function(e){
					if(propsPoper.currentPropsWindow)
					{
						propsPoper.currentPropsWindow.hide();
						propsPoper.currentPropsWindow.remove();
					}

					propsPoper.currentPropsWindow = null;
				} 
		};
		
		jqObjectName.click(delegate(this, propsPoper.click));
		jqEdit.click(delegate(this, propsPoper.click));
		
		jqObjectType.change(delegate(this, propsPoper.change));

		if(inArray)
		{
			var jqRemove = jQuery("<button class=\"array-button\">Remove</button>");
			jqObjectTitle.append(jqRemove);
			
			jqRemove.click(delegate(this, function(e){
				propsPoper.remove();
				jqObject.remove();
			}));
		}
		
		jqObject.append(jqObjectTitle);
		container.append(jqObject);

		jQuery.getJSON(
			"ajax-get-type-subclasses.php",
			{ "type": param.type },
			delegate(this, function(subTypes){
				
				for(var i = 0; i < subTypes.length; i++)
				{
					var subType = subTypes[i];
					jqObjectType.append("<option>" + subType.type + "</option>");


					var jqObjectProperties = jQuery("<div class=\"object-properties\">");
					jqObjectProperties.attr("id", "object-props-" + param.name + "-" + subType.type);
					objectTypesProps[subType.type] = jqObjectProperties;

					jQuery.each(subType.properties, delegate(this, function (i, property) {
						
						if (property.isReadOnly)
							return;

						property.name = param.name + ":" + property.name;
						
						if (property.isEnum || property.isStringEnum)
							scope.addEnumField(jqObjectProperties, property);
						else if (property.isArray)
							scope.addArrayField(jqObjectProperties, property);
						else if (!property.isComplexType)
							scope.addSimpleField(jqObjectProperties, property);
						else
							scope.addObjectField(jqObjectProperties, property);
					}));
				}
			})
		);
	},
	
	addEnumField: function(/*jQuery*/ container, param) {
		var jqCheckBox = jQuery("<input type=\"checkbox\" />").attr("tabindex", -1);
		jqCheckBox.click(delegate(this, this.checkBoxFieldClickHandler));
		
		var jqSelect = jQuery("<select name=\""+param.name+"\" class=\"disabled\"></select>");
		jQuery.each(param.constants, function(i, constant) {
			jqSelect.append("<option value=\""+constant.defaultValue+"\">"+constant.name+"</option>");
		});
		
		jqSelect.focus(delegate(this, this.enableField));
		
		jQuery("<div class=\"param enum\">")
			.append("<label for=\""+param.name+"\">"+param.name+" (<span class=\"enum-type\">"+param.type+"</span>):</label>")
			.append(jqSelect)
			.append(jqCheckBox)
			.append(this.getHelpJQ(param.name + " - " + param.description))
			.appendTo(container);
	},
	
	addSimpleField: function(/*jQuery*/ container, param) {
		var jqCheckBox = jQuery("<input type=\"checkbox\" />").attr("tabindex", -1);
		jqCheckBox.click(delegate(this, this.checkBoxFieldClickHandler));
		
		var jqInput = jQuery("<input type=\"text\" name=\""+param.name+"\" class=\"disabled\" />");
		jqInput.click(delegate(this, this.enableField));
		jqInput.keypress(delegate(this, this.enableField));
		
		jQuery("<div class=\"param "+param.type+"\">")
			.append("<label for=\""+param.name+"\">"+param.name+" ("+param.type+"):</label>")
			.append(jqInput)
			.append(jqCheckBox)
			.append(this.getHelpJQ(param.name + " - " + param.description))
			.appendTo(container);
	},
	
	addFileField:  function(/*jQuery*/ container, param) {
		var jqCheckBox = jQuery("<input type=\"checkbox\" />").attr("tabindex", -1);
		jqCheckBox.click(delegate(this, this.checkBoxFieldClickHandler));
		
		var jqInput = jQuery("<input type=\"file\" name=\""+param.name+"\" class=\"disabled\" />");
		jqInput.click(delegate(this, this.enableField));
		jqInput.keypress(delegate(this, this.enableField));
		
		jQuery("<div class=\"param "+param.type+"\">")
			.append("<label for=\""+param.name+"\">"+param.name+" ("+param.type+"):</label>")
			.append(jqInput)
			.append(jqCheckBox)
			.append(this.getHelpJQ(param.name + " - " + param.description))
			.appendTo(container);
	},
	
	addArrayField: function(/*jQuery*/ container, param) {
		var jqArray = jQuery("<div class=\"array\">");
		var objectTypeId = "object-type-" + param.name.replace(/:/g, "_");
		var jqArrayName = jQuery("<div class=\"array-name\">temp</div>");  // temp required by IE
		jqArrayName.html(param.name + " (<span id=\"" + objectTypeId + "\" class=\"array-type\">array</span>)");
		jqArray.append(jqArrayName);
		
		var jqAdd = jQuery("<button class=\"array-button\">Add</button>");
		jqArrayName.append(jqAdd);
		
		var index = 0;
		var scope = this;
		
		jqAdd.click(delegate(this, function(e){
			var theParam = $.evalJSON($.toJSON(param.arrayType)); // clone
			theParam.name = param.name + ":" + index;
			scope.addObjectField(jqArray, theParam, true);
			index++;
		}));
		
		container.append(jqArray);
	},
	
	checkBoxFieldClickHandler: function(e) {
		if (!e.target)
			return;
		
		var field = jQuery(e.target).siblings("input,select");
		if (!field.hasClass("disabled"))
			field.addClass("disabled");
		else
			field.removeClass("disabled");
	},
	
	saveToHistory: function() {
		var params = [];
		this.jqActionParams.parent().find(".param").each(function(i, item) {
			if (jQuery(item).find("input:checkbox:checked").size() > 0)
			{
				var name = jQuery(item).find("input:text,select").attr("name");
				var value = jQuery(item).find("input:text,select").val();
				
				params[name] = value;
			}
		});
		this.history.push({ service: this.jqServices.val(), action: this.jqActions.val(), params: params });
		var optionName = this.jqServices.val() + "." + this.jqActions.val();
		this.jqHistory.prepend("<option value=\"" + (this.history.length - 1) + "\">" + this.history.length + ". " + optionName + "</option>");
		this.jqHistory.val(optionName);
	},
	
	onHistoryClickHandler: function(e) {
		var index = this.jqHistory.val();
		this.historyItem = this.history[index];
		
		this.jqServices.val(this.historyItem.service);
		this.jqServices.change();
	},
	
	loadHistoryAction: function() {
		this.jqActions.val(this.historyItem.action);
		this.jqActions.change();
	},
	
	loadHistoryData: function() {
		for (var prop in this.historyItem.params) {
			this.jqActionParams
				.parent()
				.find("[name="+prop+"]")
				.val(this.historyItem.params[prop])
				.parent().find("input:checkbox").click();
		}
		
		this.historyItem = null;
	},
	
	getHelpJQ: function(txt) {
		var jqHelp = jQuery("<div />");
		if (txt.indexOf(" - ") != (txt.length - 3)) // when txt ends with " - ", there is no description, only a name, and we don't want to display it
			jqHelp = jQuery("<img src=\"images/help.png\" class=\"help\" title=\""+txt+"\" />");
		
		return jqHelp;
	},
	
	hideOpenObjectProperties: function() {
		jQuery(".object-properties").hide();
	},
	
	enableField: function(e) {
		if (!e.target)
			return;
		
		if (e.keyCode == 9) // ignore tab key
			return;
		
		jQuery(e.target).removeAttr("readonly").removeClass("disabled")
		.siblings("input[type=checkbox]").attr("checked", true);
	},
	
	calculateDimensions: function() {
		this.height = jQuery("body").innerHeight() - jQuery("#kmcSubMenu").outerHeight() - 50;
		
		this.testmeHeight = this.height - (codeHeight+1);
		jQuery(".code").css("height", codeHeight);
		
		var leftBoxWidth = jQuery(".left").outerWidth();
		
		var leftBoxRightMargin = jQuery(".left").css("margin-right").replace("px", "");
		leftBoxRightMargin = Number(leftBoxRightMargin);
		
		var leftBoxLeftMargin = jQuery(".left").css("margin-left").replace("px", "");
		leftBoxLeftMargin = Number(leftBoxLeftMargin);
		this.resultWidth = jQuery("body").innerWidth() - leftBoxWidth - leftBoxLeftMargin - leftBoxRightMargin - 21;
	},
	
	initCodeExample: function(generator) {
		if(!generator)
			generator = new KCodeExamplePHP(jQuery("#example"));
		
		this.codeGenerator = generator;

		if(!this.actionInfo)
			return;

		var service = this.jqServices.find("option:selected").text();
		var action = this.jqActions.find("option:selected").text();
		
		this.codeGenerator.setAction(service, action, this.actionInfo.actionParams);
		this.codeGenerator.setChangeEvent();
		this.codeGenerator.onParamsChange();
	}
};

var kTestMe;
var codeHeight = 179;

jQuery(function() {
	kTestMe = new KTestMe();
});
