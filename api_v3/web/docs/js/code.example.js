
function KCodeExampleBase(){}

KCodeExampleBase.prototype.init = function(codeLanguage, codeTitle)
{
	this.lang = codeLanguage;
	this.title = codeTitle;
};

KCodeExampleBase.prototype.lang = null;
KCodeExampleBase.prototype.title = null;

KCodeExampleBase.prototype.jqEntity = null;
KCodeExampleBase.prototype.bracketsCounter = 0;

KCodeExampleBase.prototype.jqClientObject = null;
KCodeExampleBase.prototype.jqAction = null;
KCodeExampleBase.prototype.jqParams = null;

KCodeExampleBase.prototype.importsArray = {};
KCodeExampleBase.prototype.jqImports = null;
KCodeExampleBase.prototype.jqActionImports = null;

KCodeExampleBase.prototype.setEntity = function (entity){
	if(this.jqEntity)
		return;
	
	entity.empty();
    this.jqEntity = entity;

    this.jqClientObject = this.codeVar("client");
    
    this.codeHeader();
	this.setChangeEvent();
};

KCodeExampleBase.prototype.getTitle = function (){
	return this.title;
};

KCodeExampleBase.prototype.getLanguage = function (){
	return this.lang;
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

KCodeExampleBase.prototype.getKsVar = function (){
	return false;
};

KCodeExampleBase.prototype.getKsMethod = function (){
	return "setKs";
};

KCodeExampleBase.prototype.getActionMethod = function (action){
	return action;
};

KCodeExampleBase.prototype.getService = function (service, plugin, entity){
	return this.codeObjectAttribute(this.jqClientObject.clone(true), service);
};

KCodeExampleBase.prototype.onParamsChange = function (){
	var scope = this;
	if(!scope.jqParams)
		return;
	scope.jqParams.empty();
	
	var ksCheckBox = jQuery("input:checkbox[id=chk-ks]");
	if (ksCheckBox.attr("checked")){

		var ksField = jQuery("input:text[name=ks]");
		var ks = ksField.val();
		
		if(scope.getKsMethod()){
			var jqSetKs = scope.codeUserFunction(scope.getKsMethod(), [scope.codeString(ks)]);
			scope.addCode(scope.codeObjectMethod(scope.jqClientObject.clone(true), jqSetKs), scope.jqParams);
		}
		else if(scope.getKsVar()){
			scope.addCode(scope.codeAssign(scope.codeObjectAttribute(scope.jqClientObject.clone(true), scope.getKsVar()), scope.codeString(ks)), scope.jqParams);
		}
	}
	
	var params = [];
	jQuery(".param").each(function(i, item) {
		var jqItem = jQuery(item);
		if (!jqItem.find("input:checkbox:checked").size())
			return;
		
		var jqField = jqItem.find("input:text,select");
		var name = jqField.attr("name");
		
		if(name == "ks")
			return;
		
		var jqValue = null;
		
		if(jqItem.hasClass("enum")){
			var jqEnumType = jqItem.find(".enum-type");
			var enumType = jqEnumType.text();
			jqValue = enumType + scope.getClassDelimiter() + jqField.find("option:selected").text();
		}
		else{
			var value = jqField.val();
			jqValue = scope.codeString(value);
		}
		params[name] = jqValue;
	});
	
	var addedObjects = new Object();
	for(var name in params){
		var nameParts = name.split(":");
		
		if(nameParts.length > 1){
			var objectName = "";
			var objectTempName = "";
			var jqObject = null;
			for(var i = 0; i < nameParts.length; i++){
				var attribute = nameParts[i];
				objectName = objectName ? objectName + "_" + attribute : attribute;
				var attributeName = attribute.charAt(0).toUpperCase() + attribute.substr(1);
				objectTempName = objectTempName ? objectTempName + attributeName : attribute;

				if(jqObject){
					if(isNaN(attribute)){
						jqObject = scope.codeObjectAttribute(jqObject.clone(true), attribute);
					}
					else{
						jqObject = scope.codeArrayItem(jqObject.clone(true), attribute);
					}
				}
				else{
					jqObject = scope.codeVar(objectTempName);
				}
				
				var jqType = jQuery("#object-type-" + objectName);
				if(jqType.size() && !addedObjects[objectName]){

					addedObjects[objectName] = true;
					
					var objectType;
					var jqNewInstance = null;
					if(jqType.hasClass("array-type")){
						//var objectType = jqType.text();
						var arrayObjectType = jqType.next().text();
						objectType = scope.getArrayType(arrayObjectType);
						jqNewInstance = scope.codeNewArray(arrayObjectType);
					}
					else{
						objectType = jqType.find("option:selected").text();
						jqNewInstance = scope.codeNewInstance(objectType);
					}

					if(objectName != attribute){
						jqOriginalObject = jqObject;
						jqObject = scope.codeVar(objectTempName);
						var jqTempParamDef = scope.codeVarDefine(jqObject.clone(true), objectType);
						var jqTempParamDeclare = scope.codeDeclareVar(jqTempParamDef.clone(true), objectType, jqNewInstance);
						scope.addCode(jqTempParamDeclare, scope.jqParams);
					}
					else{
						var jqAssign = scope.codeAssign(jqObject, jqNewInstance);
						scope.addCode(jqAssign, scope.jqParams);
					}
				}
			}
		}
		
		var objectName = nameParts.shift();
		var jqVar = scope.codeVar(objectName);
		
		if(nameParts.length){
			var attribute = null;
			for(var i = 0; i < nameParts.length; i++){
				attribute = nameParts[i];
				if(i == nameParts.length - 1)
					continue;
				
				var attributeName = attribute.charAt(0).toUpperCase() + attribute.substr(1);
				objectName = objectName + attributeName;
			}
			
			jqVar = scope.codeVar(objectName);
			if(isNaN(attribute)){
				jqVar = scope.codeObjectAttribute(jqVar, attribute);
			}
			else{
				jqVar = scope.codeArrayItem(jqVar, attribute);
			}
		}
		
		var jqAssign = scope.codeAssign(jqVar, params[name]);
		scope.addCode(jqAssign, scope.jqParams);
	}

	var addedObjects = new Object();
	var objectsNames = new Array();
	for(var name in params){
		var nameParts = name.split(":");
		
		while(nameParts.length >= 2){
			var objectName = nameParts.join("_");
			var fieldName = nameParts.join(":");
			nameParts.pop();
			
			if(addedObjects[objectName])
				continue;
			addedObjects[objectName] = true;

			var jqType = jQuery("#object-type-" + objectName);
			if(!jqType.size())
				continue;

			objectsNames.push(fieldName);
		}
	}
	objectsNames.sort();

	var objectsAssigns = new Array();
	for(var i in objectsNames){
		
		var nameParts = objectsNames[i].split(":");
		var objectName = nameParts.join("_");
		var lastAttribute = nameParts.pop();
		var objectTempName = nameParts[0];
		
		while(objectsAssigns.length && objectName.indexOf(objectsAssigns[objectsAssigns.length - 1].name) != 0){
			scope.addCode(objectsAssigns.pop().assign, scope.jqParams);
		}
		
		for(var i = 1; i < nameParts.length; i++){
			var attribute = nameParts[i];
			objectTempName += attribute.charAt(0).toUpperCase() + attribute.substr(1);
		}
		
		var jqTempObject = scope.codeVar(objectTempName + lastAttribute.charAt(0).toUpperCase() + lastAttribute.substr(1));
		var jqObject = scope.codeVar(objectTempName);
		var jqAssign = null;
		
		if(isNaN(lastAttribute)){
			jqObject = scope.codeObjectAttribute(jqObject.clone(true), lastAttribute);
			jqAssign = scope.codeAssign(jqObject.clone(true), jqTempObject.clone(true));
		}
		else{
			jqAssign = scope.codeAssignArrayItem(jqObject.clone(true), lastAttribute, jqTempObject);
		}
		objectsAssigns.push({
			name: objectName,
			assign: jqAssign
		});
	}
	
	while(objectsAssigns.length)
		scope.addCode(objectsAssigns.pop().assign, scope.jqParams);
};

KCodeExampleBase.prototype.setAction = function (service, action, params, plugin){
	if(!this.jqEntity)
		return;
	
	if(this.jqAction){
		this.jqAction.empty();
	}
	else{
		this.jqAction = jQuery("<div class=\"code-action\"/>");
		this.jqEntity.append(this.jqAction);
	}
	
	if(this.jqActionImports){
		this.importsArray = new Object();
		this.jqActionImports.empty();
	}

	var jqInitParams = jQuery("<div class=\"code-action-init-params\"/>");
	this.jqParams = jQuery("<div class=\"code-action-params\"/>");
	this.jqAction.append(jqInitParams);
	this.jqAction.append(this.jqParams);

	var jqActionArgs = new Array();
	
	if(params){
		for(var i = 0; i < params.length; i++){
			var paramType = params[i].type;
			//var arrayObjectType = null;
			var jqType = jQuery("#object-type-" + params[i].name);
			if(jqType.size()){
				if(jqType.hasClass("array-type")){
					paramType = 'array';
					//arrayObjectType = jqType.next().text();
				}
				else{
					paramType = jqType.find("option:selected").text();
				}
			}
			
			var jqParam = this.codeVar(params[i].name);
			var jqParamDef = this.codeVarDefine(jqParam.clone(true), paramType);
			var jqParamDeclare = this.codeDeclareVar(jqParamDef, paramType);
			this.addCode(jqParamDeclare, jqInitParams);
			jqActionArgs.push(jqParam);
		}
	}

	var jqService = this.getService(service, plugin, this.jqAction);
	var actionMethod = this.getActionMethod(action);
	var jqResult = this.codeVar("results");
	var jqResultDeclare = this.codeVarDefine(jqResult, "Object");
	var jqActionCall = this.codeCallAction(jqService, actionMethod, jqActionArgs);
	var jqActionResults = this.codeAssign(jqResultDeclare, jqActionCall);
	this.addCode(jqActionResults, this.jqAction);

	this.setChangeEvent();
	this.onParamsChange();
};

KCodeExampleBase.prototype.codeCallAction = function (jqService, actionMethod, jqActionArgs){
	var jqActionFunction = this.codeUserFunction(actionMethod, jqActionArgs);
	return this.codeObjectMethod(jqService, jqActionFunction);
};

KCodeExampleBase.prototype.setChangeEvent = function (){
    jQuery("input,select").change(this.onParamsChange);
    jQuery("button").change(this.setChangeEvent);
};

KCodeExampleBase.prototype.addCode = function (code, entity){
	if(!code)
		return;

	if(!this.jqEntity)
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
	return jqObjectDef;
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
	var jqCode = jQuery("<span/>");
	
	jqCode.append(jqObject);	
	jqCode.append(this.getArrayOpener());
	jqCode.append(index);
	jqCode.append(this.getArrayCloser());
	
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
	
	if(jqVal)
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

KCodeExampleBase.prototype.codeString = function (str){
	if(isNaN(str) || !str.length)
		return jQuery("<span class=\"code-" + this.lang + "-str\">\"" + str + "\"</span>");
	return jQuery("<span class=\"code-" + this.lang + "-str\">" + str + "</span>");
};

KCodeExampleBase.prototype.codeHeader = function (){};


function KCodeExamplePHP(entity){
	this.init('php', 'PHP');
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
	var jqPluginObject = this.codeVar("pluginClientName");
	var jqFunction = this.codeFunction('get', [this.jqClientObject.clone(true)]);
	
	this.addCode(this.codeAssign(jqPluginObject.clone(true), this.codeClassMethod(pluginClientClass, jqFunction)), entity);
	return this.codeObjectAttribute(jqPluginObject.clone(true), service);
};

KCodeExamplePHP.prototype.codeHeader = function (){
	if(!this.jqEntity)
		return;
	
	this.jqEntity.append(jQuery("<span class=\"code-php-code\">&lt;?php</span>"));
	this.jqEntity.append("<br/>");
	this.addCode(this.codeSystemFunction("require_once", [this.codeString("lib/KalturaClient.php")]));

	var jqConfigObject = this.codeVar("config");

	this.addCode(this.codeAssign(jqConfigObject.clone(true), this.codeNewInstance("KalturaConfiguration", [this.codeVar("partnerId")])));
	this.addCode(this.codeAssign(this.codeObjectAttribute(jqConfigObject.clone(true), "serviceUrl"), this.codeString("http://" + location.hostname + "/")));
	this.addCode(this.codeAssign(this.jqClientObject.clone(true), this.codeNewInstance("KalturaClient", [jqConfigObject.clone(true)])));
};

KCodeExamplePHP.prototype.addCode = function (code, entity){
	if(!code)
		return;
	
	code.append(";");
	KCodeExampleBase.prototype.addCode.apply(this, arguments);
};

KCodeExamplePHP.prototype.codeDeclareVar = function (jqObjectDef, type, newValue){
	return this.codeAssign(jqObjectDef, newValue);
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

KCodeExamplePHP.prototype.codeString = function (str){
	if(isNaN(str) || !str.length)
		return jQuery("<span class=\"code-" + this.lang + "-str\">'" + str + "'</span>");
	return jQuery("<span class=\"code-" + this.lang + "-str\">" + str + "</span>");
};


function KCodeExampleJavascript(entity){
	this.init('javascript', 'Javascript');
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
	if(!this.jqEntity)
		return;
	
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

KCodeExampleJavascript.prototype.getKsMethod = function (){
	return false;
};

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

	this.addCode(this.codeAssign(jqConfigDeclare, this.codeNewInstance("KalturaConfiguration", [this.codeVar("partnerId")])), jqBody);
	this.addCode(this.codeAssign(this.codeObjectAttribute(jqConfigObject.clone(true), "serviceUrl"), this.codeString("http://" + location.hostname + "/")), jqBody);
	this.addCode(this.codeAssign(jqClientDeclare, this.codeNewInstance("KalturaClient", [jqConfigObject.clone(true)])), jqBody);
	
	jqBody.append(this.jqAction);
	jqBody.addClass("indent");

	var jqHtml = this.codeHtml("script", null, [jqBody]);
	this.addHtmlCode(jqHtml);
};


function KCodeExampleJava(entity){
	this.init('java', 'Java');
}

KCodeExampleJava.prototype = new KCodeExampleBase();

KCodeExampleJava.prototype.init = function(entity, codeLanguage){
	KCodeExampleBase.prototype.init.apply(this, arguments);
};

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

KCodeExampleJava.prototype.codeDeclareVar = function (jqObjectDef, type, newValue){
	switch(type){
		case "int":
			return this.codeAssign(jqObjectDef, newValue ? newValue : "0");

		case "bool":
			return this.codeAssign(jqObjectDef, newValue ? newValue : "false");
			
		default:
			return this.codeAssign(jqObjectDef, newValue);
	}
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
	if(!this.jqEntity)
		return;

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

	var jqSetPartnerId = this.codeUserFunction("setPartnerId", [this.codeVar("partnerId")]);
	this.addCode(this.codeObjectMethod(jqConfigObject.clone(true), jqSetPartnerId), jqBody);

	var jqSetEndpoint = this.codeUserFunction("setEndpoint", [this.codeString("http://" + location.hostname + "/")]);
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
	this.init('csharp', 'C#');
}

KCodeExampleCsharp.prototype = new KCodeExampleBase();

KCodeExampleCsharp.prototype.init = function(entity, codeLanguage){
	KCodeExampleBase.prototype.init.apply(this, arguments);
};

KCodeExampleCsharp.prototype.getKsMethod = function (){
	return false;
};

KCodeExampleBase.prototype.getKsVar = function (){
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

KCodeExampleCsharp.prototype.codeDeclareVar = function (jqObjectDef, type, newValue){
	switch(type){
		case "int":
			return this.codeAssign(jqObjectDef, newValue ? newValue : "0");

		case "bool":
			return this.codeAssign(jqObjectDef, newValue ? newValue : "false");
			
		default:
			return this.codeAssign(jqObjectDef, newValue);
	}
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
	if(!this.jqEntity)
		return;
	
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
	var jqConfigObjectInit = this.codeAssign(jqConfigObjectDeclare.clone(true), this.codeNewInstance("KalturaConfiguration", [this.codeVar("partnerId")]));
	this.addCode(jqConfigObjectInit, jqBody);
	this.addCode(this.codeAssign(this.codeObjectAttribute(jqConfigObject.clone(true), "ServiceUrl"), this.codeString("http://" + location.hostname + "/")), jqBody);

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
	this.init('python', 'Python');
}

KCodeExamplePython.prototype = new KCodeExampleBase();

KCodeExamplePython.prototype.init = function(entity, codeLanguage){
	KCodeExampleBase.prototype.init.apply(this, arguments);
};

KCodeExampleBase.prototype.getNull = function (){
	return jQuery("<span class=\"code-" + this.lang + "-system\">None</span>");
};

KCodeExamplePython.prototype.getKsMethod = function (){
	return "setKs";
};

KCodeExamplePython.prototype.codeDeclareVar = function (jqObjectDef, type, newValue){
	switch(type){
		case "int":
			return this.codeAssign(jqObjectDef, newValue ? newValue : "0");

		case "bool":
			return this.codeAssign(jqObjectDef, newValue ? newValue : "false");
			
		default:
			return this.codeAssign(jqObjectDef, newValue);
	}
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
	if(!this.jqEntity)
		return;

	this.importsArray = {};	
	this.jqImports = jQuery("<div class=\"code-python-imports\"/>");
	this.jqActionImports = jQuery("<div class=\"code-python-action-imports\"/>");
	this.jqEntity.append(this.jqImports);
	this.jqEntity.append(this.jqActionImports);

	this.addCode(this.codeImport("KalturaClient"), this.jqImports);

	var jqConfigObject = this.codeVar("config");
	var jqConfigObjectDeclare = this.codeVarDefine(jqConfigObject, "KalturaConfiguration");

	this.addCode(this.codeAssign(jqConfigObjectDeclare.clone(true), this.codeNewInstance("KalturaConfiguration", [this.codeVar("PARTNER_ID")])));
	this.addCode(this.codeAssign(this.codeObjectAttribute(jqConfigObject.clone(true), "serviceUrl"), this.codeString("http://" + location.hostname + "/")));
	this.addCode(this.codeAssign(this.jqClientObject.clone(true), this.codeNewInstance("KalturaClient", [jqConfigObject.clone(true)])));
};
