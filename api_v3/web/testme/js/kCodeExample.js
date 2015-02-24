
function KCodeExampleBase(){}

KCodeExampleBase.instance = null;
KCodeExampleBase.prototype.init = function(entity, codeLanguage)
{
	KCodeExampleBase.instance = this;
	
	entity.empty();
	
	this.lang = codeLanguage;
    this.jqEntity = entity;

    this.jqClientObject = this.codeVar("client");
    
    this.codeHeader();
    
    kTestMe.call.jqCode = this.jqAction;
};

KCodeExampleBase.prototype.lang = null;

KCodeExampleBase.prototype.jqEntity = null;
KCodeExampleBase.prototype.bracketsCounter = 0;

KCodeExampleBase.prototype.jqClientObject = null;
KCodeExampleBase.prototype.jqAction = null;
KCodeExampleBase.prototype.jqMultiActions = null;
KCodeExampleBase.prototype.jqParams = null;

KCodeExampleBase.prototype.importsArray = {};
KCodeExampleBase.prototype.jqImports = null;
KCodeExampleBase.prototype.jqActionImports = null;
KCodeExampleBase.prototype.jqActionPlugins = null;

// (Hagay Onn) If using port 80, the location object's port member is ""
KCodeExampleBase.getServiceUrl = function (){
	var serviceFullUrl = location.protocol + "//" + location.hostname;
	if(location.port != ""){	// not port 80 (default)
		serviceFullUrl += ":" + location.port;
	}
	serviceFullUrl += "/";
	return serviceFullUrl;
};

KCodeExampleBase.prototype.getNull = function (){
	return jQuery("<span class=\"code-" + this.lang + "-system\">null</span>");
};

KCodeExampleBase.prototype.getVoid = function (){
	return jQuery("<span class=\"code-" + this.lang + "-system\">void</span>");
};

KCodeExampleBase.prototype.getObjectDelimiter = function (){
	return ".";
};

KCodeExampleBase.prototype.getClassDelimiter = function (){
	return ".";
};

KCodeExampleBase.prototype.getArrayOpener = function (){
	return "[";
};

KCodeExampleBase.prototype.getArrayCloser = function (){
	return "]";
};

/**
 * @return string
 */
KCodeExampleBase.prototype.getKsVar = function (){
	return null;
};

/**
 * @return string
 */
KCodeExampleBase.prototype.getKsMethod = function (){
	return "setKs";
};

KCodeExampleBase.prototype.getActionMethod = function (action){
	return action;
};

KCodeExampleBase.prototype.getService = function (service, plugin, entity){
	return this.codeObjectAttribute(this.jqClientObject.clone(true), service);
};

/**
 * @param kCall call
 */
KCodeExampleBase.prototype.codeAction = function (call){

	if(call.jqCode != null)
		call.jqCode.empty();
	
	if(call.getServiceId() == '')
		return;
	
	if(this.jqActionPlugins != null){
		this.jqActionPlugins.empty();
	}
	else{
		this.jqActionPlugins = jQuery("<div class=\"code-action-plugins\"/>");
		this.jqEntity.append(this.jqActionPlugins);
	}

	if(call.jqCode == null){ // is a sub request within a multi-request
		call.jqCode = jQuery('<div class="code-action" id="code-call' + call.name + '"/>');
		this.jqMultiActions.append(call.jqCode);
	}
	else if(this.jqActionImports != null){
		this.importsArray = new Object();
		this.jqActionImports.empty();
	}
	
	if(call instanceof kCall && call.isMultiRequest()){

		// start multi request
		var jqStartMultiRequestFunction = this.codeUserFunction('startMultiRequest');
		var jqStartMultiRequestMethod = this.codeObjectMethod(this.jqClientObject.clone(true), jqStartMultiRequestFunction);
		this.addCode(jqStartMultiRequestMethod, call.jqCode);

		if(this.jqMultiActions == null)
			this.jqMultiActions = jQuery('<div class="code-sub-actions"/>');
		call.jqCode.append(this.jqMultiActions);
		
		for(var fieldName in call.fields){
			var field = call.fields[fieldName];
			this.codeAction(field.dialog);
		}

		// do multi request
		var jqDoMultiRequestFunction = this.codeUserFunction('doMultiRequest');
		var jqDoMultiRequestMethod = this.codeObjectMethod(this.jqClientObject.clone(true), jqDoMultiRequestFunction);
		var jqResults = this.codeVar("results");
		var jqResultsDeclare = this.codeVarDefine(jqResults, "Object");
		var jqRequest = this.codeAssign(jqResultsDeclare, jqDoMultiRequestMethod);
		this.addCode(jqRequest, call.jqCode);
	}
	else{

		call.jqParams = jQuery("<div class=\"code-action-params\"/>");
		call.jqCode.append(call.jqParams);

		var jqActionArgs = this.codeParams(call, null, true);
		var jqService = this.getService(call.getService(), call.getPlugin(), call.jqCode);
		var actionMethod = this.getActionMethod(call.getActionId());
	
		var jqActionCall = this.codeCallAction(jqService, actionMethod, jqActionArgs);
		
		if(call.name == null){
			var jqResult = this.codeVar("result");
			var jqResultDeclare = this.codeVarDefine(jqResult, "Object");
			jqActionCall = this.codeAssign(jqResultDeclare, jqActionCall);
		}
		this.addCode(jqActionCall, call.jqCode);
	}
};

KCodeExampleBase.prototype.codeParams = function (dialog, jqObject, isNew){

	kTestMe.log.info('Coding dialog [' + dialog.className + ': ' + dialog.getName() + '] params');
	
	var jqParams = new Array();
	for(var fieldName in dialog.fields){
		var field = dialog.fields[fieldName];

		kTestMe.log.debug('Creating parameter variable [' + field.className + ': ' + field.getFullName() + '] using full name');
		var paramNameParts = field.getFullName().split(':');
		var jqParam = this.codeFullParam(paramNameParts);

		if(jqObject != null)
			jqParam = this.codeObjectAttribute(jqObject.clone(true), fieldName);
		
		jqParams.push(jqParam.clone(true));
		if(isNew || field.jqCode == null){
			field.jqCode = jQuery("<div class=\"code-param\"/>");
			dialog.jqParams.append(field.jqCode);
		}
		field.currentValueChange(delegate(this, this.codeCallParam, [field, jqParam]));
		
		if(field.enabled || jqObject == null){
			this.codeCallParam(field, jqParam, isNew);
		}
		else if(isNew && field.enabled){
			this.codeCallParam(field, jqParam, isNew);
		}
	}
	
	return jqParams;
};

KCodeExampleBase.prototype.codeFileValue = function (filename){
	return this.codeString(filename);
};

/**
 * @param kArrayField arrayField
 * @param kField arrayItem
 **/
KCodeExampleBase.prototype.codeArrayItemParam = function (arrayField, arrayItem){
	var index = arrayField.fieldsCount - 1;
	kTestMe.log.info('Coding added array [' + arrayField.getFullName() + '] item name [' + arrayItem.getFullName() + '] index [' + index + ']');
	
	arrayItem.jqCode = jQuery("<div class=\"code-param\"/>");
	arrayField.jqParams.append(arrayItem.jqCode);
	
	var jqArrayItem = this.codeArrayItem(arrayField.jqCodeParam, index);
	arrayItem.currentValueChange(delegate(this, this.codeCallParam, [arrayItem, jqArrayItem]));
	this.codeCallParam(arrayItem, jqArrayItem);
};

/**
 * @param kArrayField field
 * @param jQuery jqParam
 **/
KCodeExampleBase.prototype.codeArrayValue = function (field, jqParam, isNew){
	kTestMe.log.info('Coding array [' + field.getFullName() + ']');

	var paramType = field.getType();

	var jqParamVal = null;
	if(field.enabled)
		jqParamVal = this.codeNewArray(paramType);

	if(isNew || field.jqInitCode == null){
		field.jqInitCode = jQuery("<div class=\"code-array-init\"/>");
		field.jqCode.append(field.jqInitCode);
	}
	else{
		field.jqInitCode.empty();
	}
	
	var jqParamDef = this.codeVarDefine(jqParam.clone(true), paramType);
	var jqDeclareCode = this.codeDeclareVar(jqParamDef, paramType, jqParamVal);
	this.addCode(jqDeclareCode, field.jqInitCode);
	
	field.jqCodeParam = jqParam;
	field.childFieldAdd(delegate(this, this.codeArrayItemParam));
	if(field.enabled){
		
		if(field.jqParams == null){
			field.jqParams = jQuery("<div class=\"code-array-items\"/>");
			field.jqCode.append(field.jqParams);
		}
		
		var index = 0;
		kTestMe.log.info('Before array [' + field.getFullName() + '] items');
		for(var fieldName in field.fields){
			var item = field.fields[fieldName];

			kTestMe.log.info('Coding array [' + field.getFullName() + '] item [' + index + '] name [' + item.getFullName() + ']');
			
			if(isNew || item.jqCode == null){
				item.jqCode = jQuery("<div class=\"code-param\"/>");
				field.jqParams.append(item.jqCode);
			}
			
			var jqArrayItem = this.codeArrayItem(jqParam, index++);
			item.currentValueChange(delegate(this, this.codeCallParam, [item, jqArrayItem]));
			
			if(item.enabled)
				this.codeCallParam(item, jqArrayItem);
		}
		kTestMe.log.info('After array [' + field.getFullName() + '] items [' + index + ']');
	}
};

KCodeExampleBase.prototype.codeObjectValue = function (field, jqParam, isNew){
	
	kTestMe.log.info('Coding object [' + field.getFullName() + '] [' + jqParam.text() + ']');
	
	var paramType = field.getType();
	var jqParamVal = null;
	if(field.enabled)
		jqParamVal = this.codeNewInstance(paramType);

	if(isNew || field.jqInitCode == null){
		field.jqInitCode = jQuery("<div class=\"code-object-init\"/>");
		field.jqCode.append(field.jqInitCode);
	}
	else{
		field.jqInitCode.empty();
	}
	
	var jqParamDef = this.codeVarDefine(jqParam.clone(true), paramType);
	var jqDeclareCode = this.codeDeclareVar(jqParamDef, paramType, jqParamVal);
	this.addCode(jqDeclareCode, field.jqInitCode);

	if(field.dialog == null){
		kTestMe.log.info('Coding object [' + field.getFullName() + '] [' + jqParam.text() + '] no dialog defined');
		return;
	}

	if(isNew || field.dialog.jqCode == null){
		field.dialog.jqCode = jQuery("<div class=\"code-param-object\"/>");
		field.jqCode.append(field.dialog.jqCode);
	}
	
	if(isNew || field.dialog.jqParams == null){
		field.dialog.jqParams = jQuery("<div class=\"code-object-params\"/>");
		field.dialog.jqCode.append(field.dialog.jqParams);
	}
	
	if(field.enabled)
		this.codeParams(field.dialog, jqParam, isNew);
};

KCodeExampleBase.prototype.codeEnumValue = function (field){
	return field.getType() + this.getClassDelimiter() + field.getValueName();
};

KCodeExampleBase.prototype.codeCallParam = function (field, jqParam, isNew){
	
	kTestMe.log.info('Coding parameter [' + field.className + ': ' + field.getFullName() + ']');
	
	if(field.param == null){
		kTestMe.log.info('Coding parameter [' + field.className + ': ' + field.getFullName() + '] param is null');
		return;
	}
		
	if (field.param.isComplexType && !field.param.isEnum && !field.param.isStringEnum)
	{
		kTestMe.log.debug('Coding parameter [' + field.className + ': ' + field.getFullName() + '] is complex');
		if (field.param.isArray){
			this.codeArrayValue(field, jqParam, isNew);
		}
		else if(!field.param.isEnum && !field.param.isStringEnum){
			this.codeObjectValue(field, jqParam, isNew);
		}
		return;
	}
	
	field.jqCode.empty();
	
	var paramType = field.getType();
	var jqParamVal = null;
	if(field.enabled){
		if (field.param.isComplexType) // isEnum
		{
			jqParamVal = this.codeEnumValue(field);
		}
		else if (field.param.isFile){
			jqParamVal = this.codeFileValue(field.getValue());
		}
		else if (paramType == 'string'){
			jqParamVal = this.codeString(field.getValue());
		}
		else{
			jqParamVal = this.codeInt(field.getValue());
		}
	}
	else{
		kTestMe.log.debug('Coding parameter [' + field.className + ': ' + field.getFullName() + '] is disabled');		
	}
	
	var jqParamDef = this.codeVarDefine(jqParam.clone(true), paramType);
	var jqParamCode = this.codeDeclareVar(jqParamDef, paramType, jqParamVal);
	this.addCode(jqParamCode, field.jqCode);
};

KCodeExampleBase.prototype.codeFullParam = function (paramNameParts){
	var param = paramNameParts.pop();

	if(paramNameParts.length == 1 && !isNaN(paramNameParts[0]))
		param = 'request' + paramNameParts.pop() + param;
		
	if(!paramNameParts.length)
		return this.codeVar(param);
	
	var jqObject = this.codeFullParam(paramNameParts);
	return this.codeObjectAttribute(jqObject, param);
};

KCodeExampleBase.prototype.codeCallAction = function (jqService, actionMethod, jqActionArgs){
	var jqActionFunction = this.codeUserFunction(actionMethod, jqActionArgs);
	return this.codeObjectMethod(jqService, jqActionFunction);
};

KCodeExampleBase.prototype.addCode = function (code, entity){
	if(!code)
		return;
	
	if(!entity)
		entity = this.jqEntity;
	
	entity.append(code);
	entity.append("<br/>");
	
	return entity;
};

KCodeExampleBase.prototype.codePackage = function (packageName){
	var jqCode = jQuery("<span/>");

	jqCode.append("<span class=\"code-" + this.lang + "-system\">package </span>");
	jqCode.append("<span class=\"code-" + this.lang + "-package\">" + packageName + "</span>");
	
	return jqCode;
};

KCodeExampleBase.prototype.codeImport = function (packageName){
	if(this.importsArray[packageName])
		return null;
	this.importsArray[packageName] = true;
	
	var jqCode = jQuery("<span/>");

	jqCode.append("<span class=\"code-" + this.lang + "-system\">import </span>");
	jqCode.append("<span class=\"code-" + this.lang + "-package\">" + packageName + "</span>");
	
	return jqCode;
};

KCodeExampleBase.prototype.codeDeclareVar = function (jqObjectDef, type, newValue){
	var value = null;
	if(newValue != null){
		switch(type){
			case "int":
				value = this.codeInt(newValue);
		
			case "bool":
				value = this.codeBool(newValue != 0 && newValue != "false");
		
			default:
				value = newValue;
		}
	}
	
	return this.codeAssign(jqObjectDef, value);
};

KCodeExampleBase.prototype.codeVarDefine = function (jqObject, type){
	var jqCode = jQuery("<span class=\"code-" + this.lang + "-var-type\">" + type + "</span>");
	jqCode.append(" ");
	jqCode.append(jqObject);
	return jqCode;
};

KCodeExampleBase.prototype.codeVar = function (name, varName){
	if(!varName)
		varName = name;
	
	var jqVar = jQuery("<span class=\"code-" + this.lang + "-var code-var-" + varName + "\">" + name + "</span>");
	var scope = this;
	jqVar.hover(
		function(){
			var jq = jQuery(".code-var-" + varName);
			jq.addClass("code-" + scope.lang + "-over");
		},
		function(){
			var jq = jQuery(".code-var-" + varName);
			jq.removeClass("code-" + scope.lang + "-over");
		}
	);
	return jqVar;
};

KCodeExampleBase.prototype.codeObjectMethod = function (jqObject, jqFunction){
	var jqCode = jQuery("<span/>");
	
	jqCode.append(jqObject);	
	jqCode.append(this.getObjectDelimiter());
	jqCode.append(jqFunction);
	
	return jqCode;
};

KCodeExampleBase.prototype.codeClassMethod = function (className, jqFunction){
	var jqCode = jQuery("<span/>");
	
	jqCode.append("<span class=\"code-" + this.lang + "-class-name\">" + className + "</span>");	
	jqCode.append(this.getClassDelimiter());
	jqCode.append(jqFunction);
	
	return jqCode;
};

KCodeExampleBase.prototype.codeArrayItem = function (jqObject, index){
	kTestMe.log.info('Coding array item var [' + jqObject.text() + '] index [' + index + ']');
	
	var jqCode = jQuery("<span/>");
	
	jqCode.append(jqObject.clone(true));	
	jqCode.append(this.getArrayOpener());
	jqCode.append(index);
	jqCode.append(this.getArrayCloser());
	
	kTestMe.log.info('Coded array item var [' + jqCode.text() + ']');
	return jqCode;
};

KCodeExampleBase.prototype.codeAssignArrayItem = function (jqArray, index, jqValue){

	var jqCode = jQuery("<span/>");
	
	jqCode.append(jqArray);	
	jqCode.append(this.getArrayOpener());
	jqCode.append(index);
	jqCode.append(this.getArrayCloser());
	jqCode.append(" = ");
	jqCode.append(jqValue);	
	
	return jqCode;
};

KCodeExampleBase.prototype.codeObjectAttribute = function (jqObject, attributeName){
	var jqCode = jQuery("<span/>");
	
	jqCode.append(jqObject);	
	jqCode.append(this.getObjectDelimiter());
	jqCode.append(attributeName);
	
	return jqCode;
};

KCodeExampleBase.prototype.codeAssign = function (jqVar, jqVal){
	var jqCode = jQuery("<span class=\"code-" + this.lang + "-assign\"/>");
	
	jqCode.append(jqVar);	
	jqCode.append(" = ");
	
	if(jqVal != null)
		jqCode.append(jqVal);
	else
		jqCode.append(this.getNull());
	
	return jqCode;
};

KCodeExampleBase.prototype.setBracketsEvent = function (bracketCounter, jqBrackets){
	var scope = this;
	for(var i = 0; i < jqBrackets.length; i++){
		jqBrackets[i].mouseover(function(){
			jQuery(".bracket-" + bracketCounter).addClass("code-" + scope.lang + "-bracket-over");
		});
		jqBrackets[i].mouseout(function(){
			jQuery(".bracket-" + bracketCounter).removeClass("code-" + scope.lang + "-bracket-over");
		});
	}
};

KCodeExampleBase.prototype.codeClassDeclare = function (className, jqBode, modifiers, parentClass, interfaces){
	var jqCode = jQuery("<div class=\"code-" + this.lang + "-class\"/>");
	
	if(modifiers && modifiers.length){
		for(var i = 0; i < modifiers.length; i++){
			jqCode.append("<span class=\"code-" + this.lang + "-system\">" + modifiers[i] + "</span> ");
		}
	}

	jqCode.append("<span class=\"code-" + this.lang + "-system\">class</span> ");
	jqCode.append(className);
	
	if(parentClass){
		jqCode.append(" extends ");
		jqCode.append(parentClass);
	}

	if(interfaces && interfaces.length){
		jqCode.append(" implements ");
		for(var i = 0; i < interfaces.length; i++){
			if(i)
				jqCode.append(", ");
			
			jqCode.append(interfaces[i]);
		}
	}
	
	jqCode.append("{");
	jqCode.append(jqBode);
	jqCode.append("}");
	
	return jqCode;
};

KCodeExampleBase.prototype.codeFunctionDeclare = function (functionName, jqBode, modifiers, functionArgs, returnType){
	var bracketCounter = this.bracketsCounter++;
	var jqOpenBracket = jQuery("<span id=\"bracket-open-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">(</span>");
	var jqCloseBracket = jQuery("<span id=\"bracket-close-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">)</span>");
	this.setBracketsEvent(bracketCounter, [jqOpenBracket, jqCloseBracket]);
	
	var jqCode = jQuery("<div class=\"code-" + this.lang + "-func\"/>");
	
	if(modifiers && modifiers.length){
		for(var i = 0; i < modifiers.length; i++){
			jqCode.append("<span class=\"code-" + this.lang + "-system\">" + modifiers[i] + "</span> ");
		}
	}

	jqCode.append(returnType);
	jqCode.append(" " + functionName);
	jqCode.append(jqOpenBracket);

	if(functionArgs && functionArgs.length){
		for(var i = 0; i < functionArgs.length; i++){
			if(i)
				jqCode.append(", ");
			
			jqCode.append(functionArgs[i]);
		}
	}

	jqCode.append(jqCloseBracket);
	jqCode.append("{");
	jqCode.append(jqBode);
	jqCode.append("}");
	
	return jqCode;
};

KCodeExampleBase.prototype.codeTryCatch = function (jqTry, exceptionDeclare, jqCatch){
	var bracketCounter = this.bracketsCounter++;
	var jqOpenBracket = jQuery("<span id=\"bracket-open-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">(</span>");
	var jqCloseBracket = jQuery("<span id=\"bracket-close-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">)</span>");
	this.setBracketsEvent(bracketCounter, [jqOpenBracket, jqCloseBracket]);
	
	var jqCode = jQuery("<div class=\"code-" + this.lang + "-try-catch\"/>");
	jqCode.append("<span class=\"code-" + this.lang + "-system\">try</span>{");
	jqCode.append(jqTry);
	jqCode.append("}");
	jqCode.append("<span class=\"code-" + this.lang + "-system\">catch</span>");

	jqCode.append(jqOpenBracket);
	jqCode.append(exceptionDeclare);
	jqCode.append(jqCloseBracket);
	jqCode.append("{");
	jqCode.append(jqCatch);
	jqCode.append("}");
	
	return jqCode;
};

KCodeExampleBase.prototype.codeUserFunction = function (functionName, functionArgs){
	var jqCode = jQuery("<span class=\"\"/>");
	jqCode.append(this.codeFunction(functionName, functionArgs));
	return jqCode;
};

KCodeExampleBase.prototype.codeSystemFunction = function (functionName, functionArgs){
	var jqCode = jQuery("<span class=\"code-" + this.lang + "-system\"/>");
	jqCode.append(this.codeFunction(functionName, functionArgs));
	return jqCode;
};

KCodeExampleBase.prototype.codeFunction = function (functionName, functionArgs){
	var bracketCounter = this.bracketsCounter++;
	var jqCode = jQuery("<span class=\"code-" + this.lang + "-call-func\"/>");
	var jqFunctionName = jQuery("<span class=\"code-" + this.lang + "-func-name\">" + functionName + "</span>");
	var jqOpenBracket = jQuery("<span id=\"bracket-open-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">(</span>");
	var jqCloseBracket = jQuery("<span id=\"bracket-close-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">)</span>");
	this.setBracketsEvent(bracketCounter, [jqOpenBracket, jqCloseBracket]);
	
	jqCode.append(jqFunctionName);	
	jqCode.append(jqOpenBracket);
	
	if(functionArgs){		
		for(var i = 0; i < functionArgs.length; i++){
			if(i)
				jqCode.append(", ");
			
			jqCode.append(functionArgs[i]);
		}
	}
		
	jqCode.append(jqCloseBracket);
	return jqCode;
};

KCodeExampleBase.prototype.codeNewArray = function (className){
	return "array";
};

KCodeExampleBase.prototype.getArrayType = function (className){
	return "array";
};

KCodeExampleBase.prototype.codeNewInstance = function (className, constructorArgs){
	var bracketCounter = this.bracketsCounter++;
	var jqCode = jQuery("<span class=\"code-" + this.lang + "-new-instance code-" + this.lang + "-system\"/>");
	var jqClassName = jQuery("<span class=\"code-" + this.lang + "-class-name\">" + className + "</span>");
	var jqOpenBracket = jQuery("<span id=\"bracket-open-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">(</span>");
	var jqCloseBracket = jQuery("<span id=\"bracket-close-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">)</span>");
	this.setBracketsEvent(bracketCounter, [jqOpenBracket, jqCloseBracket]);

	jqCode.append("new ");
	jqCode.append(jqClassName);	
	jqCode.append(jqOpenBracket);
	
	if(constructorArgs){
		for(var i = 0; i < constructorArgs.length; i++)	{
			if(i)
				jqCode.append(", ");
			
			jqCode.append(constructorArgs[i]);
		}
	}
		
	jqCode.append(jqCloseBracket);
	return jqCode;
};

KCodeExampleBase.prototype.codeError = function (jq, errorMessage){
	var jqError = jQuery("<span class=\"code-" + this.lang + "-err\" title=\"" + errorMessage + "\" />");
	jqError.append(jq);
	return jqError;
};

KCodeExampleBase.prototype.codeBool = function (value){
	return jQuery("<span class=\"code-" + this.lang + "-bool\">" + (value ? "true" : "false") + "</span>");
};

KCodeExampleBase.prototype.codeInt = function (value){
	var jqInt = jQuery("<span class=\"code-" + this.lang + "-int\"/>");
	jqInt.append(value);
	
	if(value instanceof jQuery)
		value = value.text();
	
	if(isNaN(value) || !value.length)
		return this.codeError(jqInt, 'Value must be numeric');
	
	return jqInt;
};

KCodeExampleBase.prototype.codeString = function (value){
	if(isNaN(value) || !value.length)
		return jQuery("<span class=\"code-" + this.lang + "-str\">\"" + value + "\"</span>");
	return jQuery("<span class=\"code-" + this.lang + "-str\">" + value + "</span>");
};

KCodeExampleBase.prototype.codeHeader = function (){};


function KCodeExamplePHP(entity){
	this.init(entity, 'php');
}

KCodeExamplePHP.prototype = new KCodeExampleBase();

KCodeExamplePHP.prototype.init = function(entity, codeLanguage){
	KCodeExampleBase.prototype.init.apply(this, arguments);
};

KCodeExamplePHP.prototype.getObjectDelimiter = function (){
	return "->";
};

KCodeExamplePHP.prototype.getActionMethod = function (action){
	return action == "list" ? "listAction" : action;
};

KCodeExamplePHP.prototype.getClassDelimiter = function (){
	return "::";
};

KCodeExamplePHP.prototype.getService = function (service, plugin, entity){
	if(!plugin)
		return KCodeExampleBase.prototype.getService.apply(this, arguments);
	
	var pluginClientName = plugin + "ClientPlugin";
	var pluginClientClass = "Kaltura" + pluginClientName.substr(0, 1).toUpperCase() + pluginClientName.substr(1);
	var jqPluginObject = this.codeVar(plugin + "Plugin");
	var jqFunction = this.codeFunction('get', [this.jqClientObject.clone(true)]);
	
	this.addCode(this.codeAssign(jqPluginObject.clone(true), this.codeClassMethod(pluginClientClass, jqFunction)), entity);
	return this.codeObjectAttribute(jqPluginObject.clone(true), service);
};

KCodeExamplePHP.prototype.codeHeader = function (){

	this.jqEntity.append(jQuery("<span class=\"code-php-code\">&lt;?php</span>"));
	this.jqEntity.append("<br/>");
	this.addCode(this.codeSystemFunction("require_once", [this.codeString("lib/KalturaClient.php")]));

	var jqConfigObject = this.codeVar("config");

	var jqPartnerId = this.codeError(this.codeVar("partnerId"), 'Variable partnerId must be defined');
	
	this.addCode(this.codeAssign(jqConfigObject.clone(true), this.codeNewInstance("KalturaConfiguration")));
	this.addCode(this.codeAssign(this.codeObjectAttribute(jqConfigObject.clone(true), "serviceUrl"), this.codeString(KCodeExampleBase.getServiceUrl())));
	this.addCode(this.codeAssign(this.jqClientObject.clone(true), this.codeNewInstance("KalturaClient", [jqConfigObject.clone(true)])));
	
	this.jqAction = jQuery("<div class=\"code-action\"/>");
	this.jqEntity.append(this.jqAction);
};

KCodeExamplePHP.prototype.addCode = function (code, entity){
	if(!code)
		return;
	
	code.append(";");
	KCodeExampleBase.prototype.addCode.apply(this, arguments);
};

KCodeExamplePHP.prototype.codeNewArray = function (className){
	return "array()";
};

KCodeExamplePHP.prototype.getArrayType = function (className){
	return "";
};

KCodeExamplePHP.prototype.codeNewInstance = function (className, constructorArgs){
	if(className == "array")
		return this.codeNewArray();
	
	return KCodeExampleBase.prototype.codeNewInstance.apply(this, arguments);
};

KCodeExamplePHP.prototype.codeVarDefine = function (jqObject, type){
	return jqObject;
};

KCodeExamplePHP.prototype.codeVar = function (name){
	var vars = ["$" + name, name];
	return KCodeExampleBase.prototype.codeVar.apply(this, vars);
};

KCodeExamplePHP.prototype.codeString = function (value){
	if(isNaN(value) || !value.length)
		return jQuery("<span class=\"code-" + this.lang + "-str\">'" + value + "'</span>");
	return jQuery("<span class=\"code-" + this.lang + "-str\">" + value + "</span>");
};


function KCodeExampleJavascript(entity){
	this.init(entity, 'javascript');
}

KCodeExampleJavascript.prototype = new KCodeExampleBase();

KCodeExampleJavascript.prototype.init = function(entity, codeLanguage){
	KCodeExampleBase.prototype.init.apply(this, arguments);
};

KCodeExampleJavascript.prototype.addCode = function (code, entity){
	if(!code)
		return;
	
	code.append(";");
	KCodeExampleBase.prototype.addCode.apply(this, arguments);
};

KCodeExampleJavascript.prototype.codeNewArray = function (className){
	return this.codeNewInstance("Array");
};

KCodeExampleJavascript.prototype.codeHtml = function (tag, attributes, childElements){

	var jqCode = jQuery("<span class=\"code-" + this.lang + "-html\"/>");

	jqCode.append("<span class=\"code-" + this.lang + "-html-tag\">&lt;</span>");
	jqCode.append(tag);

	if(attributes && typeof(attributes) == "object"){
		for(var attribute in attributes){
			jqCode.append(" ");
			jqCode.append("<span class=\"code-" + this.lang + "-html-attr\">" + attribute + "</span>");
			jqCode.append("<span class=\"code-" + this.lang + "-html-equel\">=</span>");
			jqCode.append("<span class=\"code-" + this.lang + "-html-quote\">&quot;</span>");
			jqCode.append("<span class=\"code-" + this.lang + "-html-value\">" + attributes[attribute] + "</span>");
			jqCode.append("<span class=\"code-" + this.lang + "-html-quote\">&quot;</span>");
		}
	}
	jqCode.append("<span class=\"code-" + this.lang + "-html-tag\">&gt;</span>");
	
	if(childElements && childElements.length){
		
		for(var i = 0; i < childElements.length; i++){
			jqCode.append(childElements[i]);
		}

	}

	jqCode.append("<span class=\"code-" + this.lang + "-html-tag\">&lt;/</span>");
	jqCode.append(tag);
	jqCode.append("<span class=\"code-" + this.lang + "-html-tag\">&gt;</span>");
	
	return jqCode;
};


KCodeExampleJavascript.prototype.addHtmlCode = function (code, entity){
	
	if(!entity)
		entity = this.jqEntity;

	entity.append(code);
	entity.append("<br/>");

	return entity;
};

KCodeExampleJavascript.prototype.codeVarDefine = function (jqObject, type){
	var jqCode = jQuery("<span class=\"code-" + this.lang + "-var-system\">var</span>");
	jqCode.append(" ");
	jqCode.append(jqObject);
	return jqCode;
};

/**
 * @return string
 */
KCodeExampleJavascript.prototype.getKsMethod = function (){
	return null;
};

/**
 * @return string
 */
KCodeExampleJavascript.prototype.getKsVar = function (){
	return "ks";
};

KCodeExampleJavascript.prototype.codeFunctionDeclare = function (functionName, jqBode, modifiers, functionArgs, returnType){
	var bracketCounter = this.bracketsCounter++;
	var jqOpenBracket = jQuery("<span id=\"bracket-open-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">(</span>");
	var jqCloseBracket = jQuery("<span id=\"bracket-close-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">)</span>");
	this.setBracketsEvent(bracketCounter, [jqOpenBracket, jqCloseBracket]);
	
	var jqCode = jQuery("<span class=\"code-" + this.lang + "-func\"/>");
	
	jqCode.append("<span class=\"code-" + this.lang + "-var-system\">function</span>");
	jqCode.append(" " + functionName);
	jqCode.append(jqOpenBracket);

	if(functionArgs && functionArgs.length){
		for(var i = 0; i < functionArgs.length; i++){
			if(i)
				jqCode.append(", ");
			
			jqCode.append(functionArgs[i]);
		}
	}

	jqCode.append(jqCloseBracket);
	jqCode.append("{");
	jqCode.append(jqBode);
	jqCode.append("}");
	
	return jqCode;
};

KCodeExampleJavascript.prototype.codeCallAction = function (jqService, actionMethod, jqActionArgs){
	
	var jqArgs = [this.codeVar("cb")];
	for(var i = 0; i < jqActionArgs.length; i++)
		jqArgs.push(jqActionArgs[i]);
	
	var jqActionFunction = this.codeUserFunction(actionMethod, jqArgs);
	return this.codeObjectMethod(jqService, jqActionFunction);
};

KCodeExampleJavascript.prototype.codeHeader = function (){
	var bracketCounter = this.bracketsCounter++;
	
	this.addHtmlCode(this.codeHtml("script", {type: "text/javascript", src: "js/ox.ajast.js"}));
	this.addHtmlCode(this.codeHtml("script", {type: "text/javascript", src: "js/webtoolkit.md5.js"}));

	this.addHtmlCode(this.codeHtml("script", {type: "text/javascript", src: "js/KalturaClientBase.js"}));
	this.addHtmlCode(this.codeHtml("script", {type: "text/javascript", src: "js/KalturaTypes.js"}));
	this.addHtmlCode(this.codeHtml("script", {type: "text/javascript", src: "js/KalturaVO.js"}));
	this.addHtmlCode(this.codeHtml("script", {type: "text/javascript", src: "js/KalturaServices.js"}));
	this.addHtmlCode(this.codeHtml("script", {type: "text/javascript", src: "js/KalturaClient.js"}));
	
	this.jqAction = jQuery("<div class=\"code-action\"/>");

	var jqBody = jQuery("<div/>");


	var jqFunctionBody = jQuery("<div/>");
	
	var jqSuccess = this.codeVar("success");
	var jqResults = this.codeVar("results");
	
	var jqOpenBracket = jQuery("<span id=\"bracket-open-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">(</span>");
	var jqCloseBracket = jQuery("<span id=\"bracket-close-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">)</span>");
	
	var jqIfNotSuccess = jQuery("<div/>");
	jqIfNotSuccess.append("<span class=\"code-" + this.lang + "-var-system\">if</span>");
	jqIfNotSuccess.append(jqOpenBracket.clone(true));
	jqIfNotSuccess.append("!");
	jqIfNotSuccess.append(jqSuccess.clone(true));
	jqIfNotSuccess.append(jqCloseBracket.clone(true));
	jqIfNotSuccess.append("<br/>");
	var jqIfNotSuccessAlert = this.codeUserFunction("alert", [jqResults.clone(true)]);
	jqIfNotSuccessAlert.addClass("indent");
	this.addCode(jqIfNotSuccessAlert, jqIfNotSuccess);
	jqFunctionBody.append(jqIfNotSuccess);
	jqFunctionBody.append("<br/>");


	var jqIfError = jQuery("<div/>");
	jqIfError.append("<span class=\"code-" + this.lang + "-var-system\">if</span>");
	jqIfError.append(jqOpenBracket.clone(true));
	jqIfError.append(this.codeObjectAttribute(jqResults.clone(true), "code"));
	jqIfError.append(" && ");
	jqIfError.append(this.codeObjectAttribute(jqResults.clone(true), "message"));
	jqIfError.append(jqCloseBracket.clone(true));
	jqIfError.append("{<br/>");

	var jqIfErrorAlert = this.codeUserFunction("alert", [this.codeObjectAttribute(jqResults.clone(true), "message")]);
	jqIfErrorAlert.addClass("indent");
	this.addCode(jqIfErrorAlert, jqIfError);
	
	var jqReturn = jQuery("<span class=\"code-" + this.lang + "-var-system\">return</span>");
	jqReturn.addClass("indent");
	this.addCode(jqReturn, jqIfError);
	
	jqIfError.append("}");
	jqFunctionBody.append(jqIfError);
	jqFunctionBody.append("<br/>");

	
	this.addCode(this.codeUserFunction("handleResults", [jqResults.clone(true)]), jqFunctionBody);
	jqFunctionBody.addClass("indent");
	var jqHandleResults = this.codeFunctionDeclare('', jqFunctionBody, [], [jqSuccess, jqResults]);

	var jqCallBack = this.codeVar("cb");
	var jqCallBackDeclare = this.codeVarDefine(jqCallBack.clone(true));
	this.addCode(this.codeAssign(jqCallBackDeclare, jqHandleResults), jqBody);
	
	var jqConfigObject = this.codeVar("config");
	var jqConfigDeclare = this.codeVarDefine(jqConfigObject.clone(true));
	var jqClientDeclare = this.codeVarDefine(this.jqClientObject.clone(true), "KalturaClient");

	var jqPartnerId = this.codeError(this.codeVar("partnerId"), 'Variable partnerId must be defined');
	
	this.addCode(this.codeAssign(jqConfigDeclare, this.codeNewInstance("KalturaConfiguration", [jqPartnerId])), jqBody);
	this.addCode(this.codeAssign(this.codeObjectAttribute(jqConfigObject.clone(true), "serviceUrl"), this.codeString(KCodeExampleBase.getServiceUrl())), jqBody);
	this.addCode(this.codeAssign(jqClientDeclare, this.codeNewInstance("KalturaClient", [jqConfigObject.clone(true)])), jqBody);
	
	jqBody.append(this.jqAction);
	jqBody.addClass("indent");

	var jqHtml = this.codeHtml("script", null, [jqBody]);
	this.addHtmlCode(jqHtml);
};


function KCodeExampleJava(entity){
	this.init(entity, 'java');
}

KCodeExampleJava.prototype = new KCodeExampleBase();

KCodeExampleJava.prototype.init = function(entity, codeLanguage){
	KCodeExampleBase.prototype.init.apply(this, arguments);
};

/**
 * @return string
 */
KCodeExampleJava.prototype.getKsMethod = function (){
	return "setSessionId";
};

KCodeExampleJava.prototype.getService = function (service, plugin, entity){
	var getter = "get" + service.substr(0, 1).toUpperCase() + service.substr(1) + "Service";
	var jqGetter = this.codeFunction(getter);
	return this.codeObjectMethod(this.jqClientObject.clone(true), jqGetter);
};

KCodeExampleJava.prototype.addCode = function (code, entity){
	if(!code)
		return;
	
	code.append(";");
	KCodeExampleBase.prototype.addCode.apply(this, arguments);
};

KCodeExampleJava.prototype.codeNewArray = function (className){
	this.addCode(this.codeImport("java.util.ArrayList"), this.jqActionImports);
	
	if(className)
		return this.codeNewInstance("ArrayList&lt;" + className + "&gt;");
	return this.codeNewInstance("ArrayList");
};

KCodeExampleJava.prototype.getArrayType = function (className){
	this.addCode(this.codeImport("java.util.ArrayList"), this.jqActionImports);
	
	if(className)
		return "ArrayList&lt;" + className + "&gt;";
	return "ArrayList";
};

KCodeExampleJava.prototype.codeArrayItem = function (jqObject, index){
	var jqGet = this.codeUserFunction("get", [this.codeString(index)]);
	return this.codeObjectMethod(jqObject, jqGet);
};


KCodeExampleJava.prototype.codeAssignArrayItem = function (jqArray, index, jqValue){
	var jqFunction = this.codeUserFunction("add", [index, jqValue]);
	return this.codeObjectMethod(jqArray, jqFunction);
};

KCodeExampleJava.prototype.codeNewInstance = function (className, constructorArgs){
	if(className == "array"){
		this.addCode(this.codeImport("java.util.ArrayList"), this.jqActionImports);
		className = "ArrayList";
	}
	
	return KCodeExampleBase.prototype.codeNewInstance.apply(this, arguments);
};

KCodeExampleJava.prototype.codeVarDefine = function (jqObject, type){

	switch(type){
		case "int":
		case "bool":
		case "Object":
			break;

		case "string":
			type = "String";
			break;

		case "file":
			type = "File";
			this.addCode(this.codeImport("java.io.File"), this.jqActionImports);
			break;
			
		default:
			if(type.indexOf("ArrayList") < 0)
				this.addCode(this.codeImport("com.kaltura.client.types." + type), this.jqActionImports);
			break;
	}
		
	return KCodeExampleBase.prototype.codeVarDefine.apply(this, arguments);
};

KCodeExampleJava.prototype.codeHeader = function (){

	this.addCode(this.codePackage("com.kaltura.code.example"));
	
	this.importsArray = {};	
	this.jqImports = jQuery("<div class=\"code-java-imports\"/>");
	this.jqActionImports = jQuery("<div class=\"code-java-action-imports\"/>");
	this.jqEntity.append(this.jqImports);
	this.jqEntity.append(this.jqActionImports);

	this.addCode(this.codeImport("com.kaltura.client.enums.*"), this.jqImports);
	this.addCode(this.codeImport("com.kaltura.client.types.*"), this.jqImports);
	this.addCode(this.codeImport("com.kaltura.client.services.*"), this.jqImports);
	this.addCode(this.codeImport("com.kaltura.client.KalturaApiException"), this.jqImports);
	this.addCode(this.codeImport("com.kaltura.client.KalturaClient"), this.jqImports);
	this.addCode(this.codeImport("com.kaltura.client.KalturaConfiguration"), this.jqImports);

	this.jqAction = jQuery("<div class=\"code-action\"/>");
	
	var jqBody = jQuery("<div/>");
	var jqConfigObject = this.codeVar("config");
	var jqConfigObjectDeclare = this.codeVarDefine(jqConfigObject, "KalturaConfiguration");
	var jqConfigObjectInit = this.codeAssign(jqConfigObjectDeclare.clone(true), this.codeNewInstance("KalturaConfiguration"));
	this.addCode(jqConfigObjectInit, jqBody);

	var jqPartnerId = this.codeError(this.codeVar("partnerId"), 'Variable partnerId must be defined');
	var jqSetPartnerId = this.codeUserFunction("setPartnerId", [jqPartnerId]);
	this.addCode(this.codeObjectMethod(jqConfigObject.clone(true), jqSetPartnerId), jqBody);
	var jqSetEndpoint = this.codeUserFunction("setEndpoint", [this.codeString(KCodeExampleBase.getServiceUrl())]);
	this.addCode(this.codeObjectMethod(jqConfigObject.clone(true), jqSetEndpoint), jqBody);

	var jqClientDeclare = this.codeVarDefine(this.jqClientObject.clone(true), "KalturaClient");
	var jqClientInit = this.codeAssign(jqClientDeclare, this.codeNewInstance("KalturaClient", [jqConfigObject.clone(true)]));
	this.addCode(jqClientInit, jqBody);
	
	jqBody.append(this.jqAction);

	var jqExceptionObject = this.codeVar("e");
	var jqExceptionObjectDeclare = this.codeVarDefine(jqExceptionObject.clone(true), "KalturaApiException");
	var jqTraceFunction = this.codeUserFunction("printStackTrace");
	var jqTrace = this.codeObjectMethod(jqExceptionObject.clone(true), jqTraceFunction);
	
	var jqTry = jQuery("<div/>");
	jqTry.addClass("indent");
	jqTry.append(jqBody);
	
	var jqCatch = jQuery("<div/>");
	jqCatch.addClass("indent");
	this.addCode(jqTrace, jqCatch);
	
	var jqTryCatch = this.codeTryCatch(jqTry, jqExceptionObjectDeclare.clone(true), jqCatch);
	jqTryCatch.addClass("indent");
	
	var jqArgsDeclare = this.codeVarDefine("args", "String[]");
	var jqMain = this.codeFunctionDeclare("main", jqTryCatch, ["public", "static"], [jqArgsDeclare], this.getVoid());
	jqMain.addClass("indent");
	
	var jqClass = this.codeClassDeclare("CodeExample", jqMain);
		
	this.jqEntity.append(jqClass);
};


function KCodeExampleCsharp(entity){
	this.init(entity, 'csharp');
}

KCodeExampleCsharp.prototype = new KCodeExampleBase();

KCodeExampleCsharp.prototype.init = function(entity, codeLanguage){
	KCodeExampleBase.prototype.init.apply(this, arguments);
};

/**
 * @return string
 */
KCodeExampleCsharp.prototype.getKsMethod = function (){
	return null;
};

/**
 * @return string
 */
KCodeExampleCsharp.prototype.getKsVar = function (){
	return "KS";
};

KCodeExampleCsharp.prototype.getActionMethod = function (action){
	return action.substr(0, 1).toUpperCase() + action.substr(1);
};

KCodeExampleCsharp.prototype.getService = function (service, plugin, entity){
	service = service.substr(0, 1).toUpperCase() + service.substr(1) + "Service";
	return this.codeObjectAttribute(this.jqClientObject.clone(true), service);
};

KCodeExampleCsharp.prototype.addCode = function (code, entity){
	if(!code)
		return;
	
	code.append(";");
	KCodeExampleBase.prototype.addCode.apply(this, arguments);
};

KCodeExampleCsharp.prototype.codeImport = function (packageName){
	var jqCode = jQuery("<span/>");

	jqCode.append("<span class=\"code-" + this.lang + "-system\">using </span>");
	jqCode.append("<span class=\"code-" + this.lang + "-package\">" + packageName + "</span>");
	
	return jqCode;
};

KCodeExampleCsharp.prototype.codePackage = function (packageName, jqBode){
	var jqCode = jQuery("<span class=\"code-" + this.lang + "-package\"/>");

	jqCode.append("<span class=\"code-" + this.lang + "-system\">namespace </span>");
	jqCode.append("<span class=\"code-" + this.lang + "-package-name\">" + packageName + "</span>");

	jqCode.append("{");
	jqCode.append(jqBode);
	jqCode.append("}");
	
	return jqCode;
};

KCodeExampleCsharp.prototype.codeNewArray = function (className){
	if(className)
		return this.codeNewInstance("List&lt;" + className + "&gt;");
	
	return this.codeNewInstance("List");
};

KCodeExampleCsharp.prototype.getArrayType = function (className){
	if(className)
		return "List&lt;" + className + "&gt;";
	return "List";
};

KCodeExampleCsharp.prototype.codeNewInstance = function (className, constructorArgs){
	if(className == "array")
		className = "List";
	
	return KCodeExampleBase.prototype.codeNewInstance.apply(this, arguments);
};

KCodeExampleCsharp.prototype.codeVarDefine = function (jqObject, type){

	switch(type){
		case "int":
		case "bool":
		case "Object":
			break;

		case "string":
			type = "String";
			break;

		case "file":
			type = "File";
			break;
			
		default:
			break;
	}
		
	return KCodeExampleBase.prototype.codeVarDefine.apply(this, arguments);
};

KCodeExampleCsharp.prototype.codeHeader = function (){
	
	this.jqImports = jQuery("<div class=\"code-csharp-imports\"/>");
	this.jqActionImports = jQuery("<div class=\"code-csharp-action-imports\"/>");
	this.jqEntity.append(this.jqImports);
	this.jqEntity.append(this.jqActionImports);

	this.addCode(this.codeImport("System"), this.jqImports);
	this.addCode(this.codeImport("System.Collections.Generic"), this.jqImports);
	this.addCode(this.codeImport("System.Text"), this.jqImports);
	this.addCode(this.codeImport("System.IO"), this.jqImports);
	
	this.jqAction = jQuery("<div class=\"code-action\"/>");
	
	var jqBody = jQuery("<div/>");
	var jqConfigObject = this.codeVar("config");
	var jqConfigObjectDeclare = this.codeVarDefine(jqConfigObject, "KalturaConfiguration");
	var jqPartnerId = this.codeError(this.codeVar("partnerId"), 'Variable partnerId must be defined');
	var jqConfigObjectInit = this.codeAssign(jqConfigObjectDeclare.clone(true), this.codeNewInstance("KalturaConfiguration", [jqPartnerId]));
	this.addCode(jqConfigObjectInit, jqBody);
	
	this.addCode(this.codeAssign(this.codeObjectAttribute(jqConfigObject.clone(true), "ServiceUrl"), this.codeString(KCodeExampleBase.getServiceUrl())), jqBody);
			
	var jqClientDeclare = this.codeVarDefine(this.jqClientObject.clone(true), "KalturaClient");
	var jqClientInit = this.codeAssign(jqClientDeclare, this.codeNewInstance("KalturaClient", [jqConfigObject.clone(true)]));
	this.addCode(jqClientInit, jqBody);
	
	jqBody.append(this.jqAction);
	jqBody.addClass("indent");

	var jqArgsDeclare = this.codeVarDefine("args", "string[]");
	var jqMain = this.codeFunctionDeclare("Main", jqBody, ["static"], [jqArgsDeclare], this.getVoid());
	jqMain.addClass("indent");
	
	var jqClass = this.codeClassDeclare("CodeExample", jqMain);
	jqClass.addClass("indent");
	var jqPackage = this.codePackage("Kaltura", jqClass);
	this.jqEntity.append(jqPackage);
};


function KCodeExamplePython(entity){
	this.init(entity, 'python');
}

KCodeExamplePython.prototype = new KCodeExampleBase();

KCodeExamplePython.prototype.init = function(entity, codeLanguage){
	KCodeExampleBase.prototype.init.apply(this, arguments);
};

KCodeExamplePython.prototype.getNull = function (){
	return jQuery("<span class=\"code-" + this.lang + "-system\">None</span>");
};

/**
 * @return string
 */
KCodeExamplePython.prototype.getKsMethod = function (){
	return "setKs";
};

KCodeExamplePython.prototype.codeNewArray = function (className){
	return "[]";
};

KCodeExamplePython.prototype.codeAssignArrayItem = function (jqArray, index, jqValue){
	var jqFunction = this.codeUserFunction("append", [jqValue]);
	return this.codeObjectMethod(jqArray, jqFunction);
};

KCodeExamplePython.prototype.codeVarDefine = function (jqObject, type){
	return jqObject;
};

KCodeExamplePython.prototype.codeImport = function (packageName){
	var jqCode = jQuery("<span/>");

	jqCode.append("<span class=\"code-" + this.lang + "-system\">from </span>");
	jqCode.append("<span class=\"code-" + this.lang + "-package\">" + packageName + "</span>");
	jqCode.append("<span class=\"code-" + this.lang + "-system\"> import *</span>");
	
	return jqCode;
};

KCodeExamplePython.prototype.codeNewInstance = function (className, constructorArgs){
	var bracketCounter = this.bracketsCounter++;
	var jqCode = jQuery("<span class=\"code-" + this.lang + "-new-instance code-" + this.lang + "-system\"/>");
	var jqClassName = jQuery("<span class=\"code-" + this.lang + "-class-name\">" + className + "</span>");
	var jqOpenBracket = jQuery("<span id=\"bracket-open-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">(</span>");
	var jqCloseBracket = jQuery("<span id=\"bracket-close-" + bracketCounter + "\" class=\"code-" + this.lang + "-bracket bracket-" + bracketCounter + "\">)</span>");
	this.setBracketsEvent(bracketCounter, [jqOpenBracket, jqCloseBracket]);

	jqCode.append(jqClassName);	
	jqCode.append(jqOpenBracket);
	
	if(constructorArgs){
		for(var i = 0; i < constructorArgs.length; i++)	{
			if(i)
				jqCode.append(", ");
			
			jqCode.append(constructorArgs[i]);
		}
	}
		
	jqCode.append(jqCloseBracket);
	return jqCode;
};

KCodeExamplePython.prototype.codeHeader = function (){

	this.importsArray = {};	
	this.jqImports = jQuery("<div class=\"code-python-imports\"/>");
	this.jqActionImports = jQuery("<div class=\"code-python-action-imports\"/>");
	this.jqEntity.append(this.jqImports);
	this.jqEntity.append(this.jqActionImports);

	this.addCode(this.codeImport("KalturaClient"), this.jqImports);

	var jqConfigObject = this.codeVar("config");
	var jqConfigObjectDeclare = this.codeVarDefine(jqConfigObject, "KalturaConfiguration");

	this.addCode(this.codeAssign(jqConfigObjectDeclare.clone(true), this.codeNewInstance("KalturaConfiguration", [this.codeVar("PARTNER_ID")])));
	
	this.addCode(this.codeAssign(this.codeObjectAttribute(jqConfigObject.clone(true), "serviceUrl"), this.codeString(KCodeExampleBase.getServiceUrl())));
		
	this.addCode(this.codeAssign(this.jqClientObject.clone(true), this.codeNewInstance("KalturaClient", [jqConfigObject.clone(true)])));
	
	this.jqAction = jQuery("<div class=\"code-action\"/>");
	this.jqEntity.append(this.jqAction);
};


function switchToCodeGenerator(type, generator){
	kTestMe.initCodeExample(generator);
	jQuery(".code-menu").removeClass("active");
	jQuery(".code-menu-" + type).addClass("active");
}

function switchToPHP(){
	switchToCodeGenerator('php', new KCodeExamplePHP(jQuery("#example")));
}

function switchToJavascript(){
	switchToCodeGenerator('javascript', new KCodeExampleJavascript(jQuery("#example")));
}

function switchToJava(){
	switchToCodeGenerator('java', new KCodeExampleJava(jQuery("#example")));
}

function switchToCSharp(){
	switchToCodeGenerator('csharp', new KCodeExampleCsharp(jQuery("#example")));
}

function switchToPython(){
	switchToCodeGenerator('python', new KCodeExamplePython(jQuery("#example")));
}

function toggleCode(){
	$('#codeExample').toggle();
	if($('#codeToggle').html() == 'Hide Code Example'){
		$('#codeToggle').html('Show Code Example');
	}
	else{
		$('#codeToggle').html('Hide Code Example');
	}
	kTestMe.calculateDimensions(1);
	kTestMe.jqWindow.resize();
}
