
function KCodeExampleBase(){}

KCodeExampleBase.instance = null;
KCodeExampleBase.prototype.init = function(entity, codeLanguage)
{
	KCodeExampleBase.instance = this;
	
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
	return "null";
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

KCodeExampleBase.prototype.onParamsChange = function (){
	var scope = KCodeExampleBase.instance;
	
	if(scope.jqParams)
		scope.jqParams.empty();
	
	var params = [];
	jQuery(".param").each(function(i, item) {
		var jqItem = jQuery(item);
		if (jqItem.find("input:checkbox:checked").size() > 0)
		{
			var name = jQuery(item).find("input:text,select").attr("name");
			var jqField = jQuery(item).find("input:text,select");
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
	if(this.jqAction)
		this.jqAction.remove();

	var jqInitParams = jQuery("<div class=\"code-action-init-params\"/>");
	this.jqAction = jQuery("<div class=\"code-action\"/>");
	this.jqParams = jQuery("<div class=\"code-action-params\"/>");
	this.jqAction.append(jqInitParams);
	this.jqAction.append(this.jqParams);
	this.jqEntity.append(this.jqAction);

	var jqActionArgs = new Array();
	
	if(params){
		for(var i = 0; i < params.length; i++){
			var jqParam = this.codeVar(params[i].name);
			var jqAssignNull = this.codeAssign(jqParam.clone());
			this.addCode(jqAssignNull, jqInitParams);
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

KCodeExamplePHP.prototype.codeVar = function (name){
	name = "$" + name;
	return KCodeExampleBase.prototype.codeVar.apply(this, arguments);
};

KCodeExamplePHP.prototype.codeString = function (str){
	return jQuery("<span class=\"code-" + this.lang + "-str\">'" + str + "'</span>");
};

