
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
	this.setChangeEvent();
};

KCodeExampleBase.prototype.lang = null;

KCodeExampleBase.prototype.jqEntity = null;
KCodeExampleBase.prototype.bracketsCounter = 0;

KCodeExampleBase.prototype.jqClientObject = null;
KCodeExampleBase.prototype.jqAction = null;
KCodeExampleBase.prototype.jqParams = null;

KCodeExampleBase.prototype.service = null;
KCodeExampleBase.prototype.action = null;
KCodeExampleBase.prototype.params = null;

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

KCodeExampleBase.prototype.getKsMethod = function (){
	return "setKs";
};

KCodeExampleBase.prototype.onParamsChange = function (){
	var scope = KCodeExampleBase.instance;
	if(!scope.jqParams)
		return;
	scope.jqParams.empty();
	
	var params = [];
	jQuery(".param").each(function(i, item) {
		var jqItem = jQuery(item);
		var jqField = jqItem.find("input:text,select");
		var name = jqField.attr("name");
		
		if(name == "ks"){
			var value = jqField.val();
			var jqSetKs = scope.codeUserFunction(scope.getKsMethod(), [scope.codeString(value)]);
			scope.addCode(scope.codeObjectMethod(scope.jqClientObject.clone(), jqSetKs), scope.jqParams);
			return;
		}
		
		if (jqItem.find("input:checkbox:checked").size() > 0)
		{
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
		}
	});
	
	var addedObjects = new Object();
	for(var name in params){
		var nameParts = name.split(":");
		
		if(nameParts.length > 1){
			var objectName = "";
			var jqObject = null;
			for(var i = 0; i < nameParts.length; i++){
				var attribute = nameParts[i];
				objectName = objectName ? objectName + "_" + attribute : attribute;

				if(jqObject){
					if(isNaN(attribute)){
						jqObject = scope.codeObjectAttribute(jqObject.clone(), attribute);
					}
					else{
						jqObject = scope.codeArrayItem(jqObject.clone(), attribute);
					}
				}
				else{
					jqObject = scope.codeVar(objectName);
				}
				
				var jqType = jQuery("#object-type-" + objectName);
				if(jqType.size() && !addedObjects[objectName]){

					addedObjects[objectName] = true;
					
					var jqNewInstance;
					if(jqType.hasClass("array-type")){
						var objectType = jqType.text();
						jqNewInstance = "array()";
					}
					else{
						var objectType = jqType.find("option:selected").text();
						jqNewInstance = scope.codeNewInstance(objectType);
					}
						
					var jqAssign = scope.codeAssign(jqObject, jqNewInstance);
					scope.addCode(jqAssign, scope.jqParams);
				}
			}
		}
		
		var jqVar = scope.codeVar(nameParts.shift());
		
		if(nameParts.length){
			for(var i = 0; i < nameParts.length; i++){
				var attribute = nameParts[i];

				if(isNaN(attribute)){
					jqVar = scope.codeObjectAttribute(jqVar, attribute);
				}
				else{
					jqVar = scope.codeArrayItem(jqVar, attribute);
				}
			}
		}
		
		var jqAssign = scope.codeAssign(jqVar, params[name]);
		scope.addCode(jqAssign, scope.jqParams);
	}
};

KCodeExampleBase.prototype.setAction = function (service, action, params){
	if(this.jqAction){
		this.jqAction.empty();
	}
	else{
		this.jqAction = jQuery("<div class=\"code-action\"/>");
		this.jqEntity.append(this.jqAction);
	}

	var jqInitParams = jQuery("<div class=\"code-action-init-params\"/>");
	this.jqParams = jQuery("<div class=\"code-action-params\"/>");
	this.jqAction.append(jqInitParams);
	this.jqAction.append(this.jqParams);

	var jqActionArgs = new Array();
	
	if(params){
		for(var i = 0; i < params.length; i++){
			var jqParam = this.codeVar(params[i].name);
			var jqParamDef = this.codeVarDefine(jqParam, params[i].type);
			var jqParamDeclare = this.codeDeclareVar(jqParamDef.clone(), params[i].type);
//			var jqAssignNull = this.codeAssign(jqParamDef.clone());
			this.addCode(jqParamDeclare, jqInitParams);
			jqActionArgs.push(jqParam);
		}
	}

	var jqResult = this.codeVar("results");
	var jqService = this.codeObjectAttribute(this.jqClientObject.clone(), service);
	var jqActionFunction = this.codeUserFunction(action, jqActionArgs);
	var jqActionCall = this.codeObjectMethod(jqService, jqActionFunction);
	var jqActionResults = this.codeAssign(jqResult, jqActionCall);
	this.addCode(jqActionResults, this.jqAction);

	this.setChangeEvent();
	this.onParamsChange();
};

KCodeExampleBase.prototype.setChangeEvent = function (){
    jQuery("input,select").change(KCodeExampleBase.instance.onParamsChange);
    jQuery("button").change(KCodeExampleBase.instance.setChangeEvent);
};

KCodeExampleBase.prototype.addCode = function (code, entity){
	if(!entity)
		entity = this.jqEntity;
	
	entity.append(code);
	entity.append("<br/>");
};

KCodeExampleBase.prototype.codePackage = function (packageName){
	var jqCode = jQuery("<span/>");

	jqCode.append("<span class=\"code-" + this.lang + "-system\">package </span>");
	jqCode.append("<span class=\"code-" + this.lang + "-package\">" + packageName + "</span>");
	
	return jqCode;
};

KCodeExampleBase.prototype.codeImport = function (packageName){
	var jqCode = jQuery("<span/>");

	jqCode.append("<span class=\"code-" + this.lang + "-system\">import </span>");
	jqCode.append("<span class=\"code-" + this.lang + "-package\">" + packageName + "</span>");
	
	return jqCode;
};

KCodeExampleBase.prototype.codeDeclareVar = function (jqObjectDef, type){
	return jqObjectDef;
};

KCodeExampleBase.prototype.codeVarDefine = function (jqObject, type){
	var jqCode = jQuery("<span class=\"code-" + this.lang + "-var-type\">" + type + "</span>");
	jqCode.append(" ");
	jqCode.append(jqObject);
	return jqCode;
};

KCodeExampleBase.prototype.codeVar = function (name){
	return jQuery("<span class=\"code-" + this.lang + "-var code-var-" + name + "\">" + name + "</span>");
};

KCodeExampleBase.prototype.codeObjectMethod = function (jqObject, jqFunction){
	var jqCode = jQuery("<span/>");
	
	jqCode.append(jqObject);	
	jqCode.append(this.getObjectDelimiter());
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
			
			jqCode.append(modifiers[functionArgs]);
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
	return jQuery("<span class=\"code-" + this.lang + "-str\">\"" + str + "\"</span>");
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

KCodeExamplePHP.prototype.getClassDelimiter = function (){
	return "::";
};

KCodeExamplePHP.prototype.codeHeader = function (){

	this.jqEntity.append(jQuery("<span class=\"code-php-code\">&lt;?php</span>"));
	this.jqEntity.append("<br/>");
	this.addCode(this.codeSystemFunction("require_once", [this.codeString("lib/KalturaClient.php")]));

	var jqConfigObject = this.codeVar("config");
	
	this.addCode(this.codeAssign(jqConfigObject.clone(), this.codeNewInstance("KalturaConfiguration")));
	this.addCode(this.codeAssign(this.codeObjectAttribute(jqConfigObject.clone(), "serviceUrl"), this.codeString(location.hostname)));
	this.addCode(this.codeAssign(this.jqClientObject.clone(), this.codeNewInstance("KalturaClient", [jqConfigObject.clone()])));
};

KCodeExamplePHP.prototype.addCode = function (code, entity){
	code.append(";");
	KCodeExampleBase.prototype.addCode.apply(this, arguments);
};

KCodeExamplePHP.prototype.codeDeclareVar = function (jqObjectDef, type){
	return this.codeAssign(jqObjectDef);
};

KCodeExamplePHP.prototype.codeVarDefine = function (jqObject, type){
	return jqObject;
};

KCodeExamplePHP.prototype.codeVar = function (name){
	name = "$" + name;
	return KCodeExampleBase.prototype.codeVar.apply(this, arguments);
};

KCodeExamplePHP.prototype.codeString = function (str){
	return jQuery("<span class=\"code-" + this.lang + "-str\">'" + str + "'</span>");
};


function KCodeExampleJava(entity){
	this.init(entity, 'java');
}

KCodeExampleJava.prototype = new KCodeExampleBase();

KCodeExampleJava.prototype.jqImports = null;

KCodeExampleJava.prototype.init = function(entity, codeLanguage){
	KCodeExampleBase.prototype.init.apply(this, arguments);
};

KCodeExampleJava.prototype.getKsMethod = function (){
	return "setSessionId";
};

KCodeExampleJava.prototype.addCode = function (code, entity){
	code.append(";");
	KCodeExampleBase.prototype.addCode.apply(this, arguments);
};

KCodeExampleJava.prototype.codeDeclareVar = function (jqObjectDef, type){
	switch(type){
		case "int":
			return this.codeAssign(jqObjectDef, "0");
			
		case "bool":
			return this.codeAssign(jqObjectDef, "false");
			
		default:
			return this.codeAssign(jqObjectDef);
	}
};

KCodeExampleJava.prototype.codeVarDefine = function (jqObject, type){
	if(type == "string")
		type = "String";
	
	return KCodeExampleBase.prototype.codeVarDefine.apply(this, arguments);
};

KCodeExampleJava.prototype.codeHeader = function (){

	this.addCode(this.codePackage("com.kaltura.code.example"));
	
	this.jqImports = jQuery("<div class=\"code-java-imports\"/>");
	this.jqEntity.append(this.jqImports);

	this.addCode(this.codeImport("com.kaltura.client.KalturaApiException"), this.jqImports);
	this.addCode(this.codeImport("com.kaltura.client.KalturaClient"), this.jqImports);
	this.addCode(this.codeImport("com.kaltura.client.KalturaConfiguration"), this.jqImports);

	this.jqAction = jQuery("<div class=\"code-action\"/>");
	
	var jqBody = jQuery("<div/>");
	var jqConfigObject = this.codeVar("config");
	var jqConfigObjectDeclare = this.codeVarDefine(jqConfigObject, "KalturaConfiguration");
	var jqConfigObjectInit = this.codeAssign(jqConfigObjectDeclare.clone(), this.codeNewInstance("KalturaConfiguration"));
	this.addCode(jqConfigObjectInit, jqBody);
	
	var jqConfigUrl = this.codeObjectAttribute(jqConfigObject.clone(), "serviceUrl");
	var jqSetServiceUrl = this.codeAssign(jqConfigUrl, this.codeString(location.hostname));
	this.addCode(jqSetServiceUrl, jqBody);

	var jqClientDeclare = this.codeVarDefine(this.jqClientObject.clone(), "KalturaClient");
	var jqClientInit = this.codeAssign(jqClientDeclare, this.codeNewInstance("KalturaClient", [jqConfigObject.clone()]));
	this.addCode(jqClientInit, jqBody);
	
	jqBody.append(this.jqAction);

	var jqExceptionObject = this.codeVar("e");
	var jqExceptionObjectDeclare = this.codeVarDefine(jqExceptionObject.clone(), "KalturaApiException");
	var jqTraceFunction = this.codeUserFunction("printStackTrace");
	var jqTrace = this.codeObjectMethod(jqExceptionObject.clone(), jqTraceFunction);
	
	var jqTry = jQuery("<div/>");
	jqTry.addClass("indent");
	jqTry.append(jqBody);
	
	var jqCatch = jQuery("<div/>");
	jqCatch.addClass("indent");
	this.addCode(jqTrace, jqCatch);
	
	var jqTryCatch = this.codeTryCatch(jqTry, jqExceptionObjectDeclare.clone(), jqCatch);
	jqTryCatch.addClass("indent");
	
	var jqArgsDeclare = this.codeVarDefine("args", "String[]");
	var jqMain = this.codeFunctionDeclare("main", jqTryCatch, ["public", "static"], [jqArgsDeclare], this.getVoid());
	jqMain.addClass("indent");
	
	var jqClass = this.codeClassDeclare("CodeExample", jqMain);
		
	this.jqEntity.append(jqClass);
};

function switchToCodeGenerator(type, generator){
	kTestMe.initCodeExample(generator);
	jQuery(".code-menu").removeClass("active");
	jQuery(".code-menu-" + type).addClass("active");
}

function switchToPHP(){
	switchToCodeGenerator('php', new KCodeExamplePHP(jQuery("#example")));
}

function switchToJava(){
	switchToCodeGenerator('php', new KCodeExampleJava(jQuery("#example")));
}
