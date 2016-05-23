
/**
 * Class that represents a single request field, as action argument or as object attribute.
 * @class kField
 */
function kField(jqContainer){
	
	if(jqContainer)
		this.load(jqContainer);
}

kField.prototype = {
	className: 'kField',
	enabled: true,
	param: null,
	name: null,
	inputName: null,
	parentName: null,
	jqContainer: null,
	jqCheckBox: null,
	jqLabel: null,
	jqInput: null,
	jqHelp: null,
	dialogOpenCallback: null,
	dialogCloseCallback: null,
	valueChangeCallback: null,
	currentValueChangeCallback: null,

	load: function(jqContainer){
		this.jqContainer = jqContainer;
		this.jqLabel = jqContainer.find('label');
		this.name = this.jqLabel.attr('for');
		this.jqInput = jqContainer.find('input[name=' + this.name + '],select[name=' + this.name + ']');
		this.jqCheckBox = jqContainer.find('input:checkbox');

		this.initListeners();
		this.disable();
	},
	
	init: function(param, jqParentContainer){
		
		this.param = param;
		this.name = param.name;
		
		if(!this.jqInput)
			kTestMe.log.error('[kField::init] jqInput is not defined');

		if(!this.jqLabel)
			this.jqLabel = jQuery('<label for="' + this.param.name + '">' + this.param.name + ' (' + this.param.type + '):</label>');
		
		this.jqCheckBox = jQuery('<input type="checkbox" tabindex="-1" />');
		
		if(!this.jqHelp){
			var helpText = this.param.name;
			if(this.param.description.length)
				helpText += ' - ' + this.param.description;
			
			this.jqHelp = jQuery('<div><img src="images/help.png" class="help" title="' + helpText + '" /></div>');
		}
			
		this.jqContainer = jQuery('<div class="param param-' + this.param.type + '">');
		this.jqContainer.append(this.jqLabel);
		this.jqContainer.append(this.jqInput);
		this.jqContainer.append(this.jqCheckBox);
		this.jqContainer.append(this.jqHelp);
		
		jqParentContainer.append(this.jqContainer);

		this.initListeners();
		this.disable();
	},

	initListeners: function() {
		this.jqCheckBox.click(delegate(this, this.checkBoxClicked));
	},

	remove: function(callback) {
		this.jqContainer.remove();
	},

	dialogOpen: function(callback) {
		this.dialogOpenCallback = callback;
	},

	dialogClose: function(callback) {
		this.dialogCloseCallback = callback;
	},

	onDialogOpen: function(dialog) {
		if(this.dialogOpenCallback != null)
			this.dialogOpenCallback.apply(this, [dialog]);
	},

	onDialogClose: function(dialog) {
		if(this.dialogCloseCallback != null)
			this.dialogCloseCallback.apply(this, [dialog]);
	},

	valueChange: function(callback) {
		this.valueChangeCallback = callback;
	},

	currentValueChange: function(callback) {
		this.currentValueChangeCallback = callback;
	},

	onValueChange: function(field){
		kTestMe.log.debug("[" + this.className + ".onValueChange] Value changed [" + this.name + "]");
		if(this.valueChangeCallback)
			this.valueChangeCallback.apply(this, [this]);
	},

	onCurrentValueChange: function(){
		kTestMe.log.debug("[" + this.className + ".onCurrentValueChange] Current value changed [" + this.name + "]");
		this.onValueChange(this);
		if(this.currentValueChangeCallback)
			this.currentValueChangeCallback.apply(this, [this]);
	},

	setParentName: function(name) {
		this.parentName  = name;
		this.setInputName(this.getFullName());
	},

	setInputName: function(inputName) {
		this.inputName = inputName;
		if(this.enabled)
			this.jqInput.attr('name', this.inputName);
	},

	getFullName: function() {
		return (this.parentName ? this.parentName + ':' : '') + this.name;
	},

	getType: function() {
		if(this.param && this.param.type)
			return this.param.type;
		
		return null;
	},

	getValue: function() {
		if(this.enabled)
			return this.jqInput.val();
		
		return null;
	},
	
	setValue: function(value) {
		this.jqInput.val(value);
		this.enable();
	},
	
	unsetValue: function() {
		this.disable();
		this.jqInput.val('');
	},

	enable: function() {
		if(this.enabled)
			return;

		this.jqInput.attr('name', this.inputName);	
		this.jqInput.removeAttr('readonly').removeClass('disabled');
		this.jqCheckBox.attr('checked', true);
		this.enabled = true;
		this.onCurrentValueChange(this);
	},

	disable: function() {
		if(!this.enabled || this.jqCheckBox.hasClass('alwaysEnabled'))
			return;
		
		this.jqInput.attr('readonly', true).removeAttr('name');
		this.jqInput.addClass('disabled');
		
		this.jqCheckBox.attr('checked', false);
		this.enabled = false;
		this.onCurrentValueChange();
	},
	
	checkBoxClicked: function(){
		if(this.jqCheckBox.attr('checked'))
			this.enable();
		else
			this.disable();
	},
	
	removeRequest: function(){
	}
};


/**
 * Class that represents simple request attribute, string, int, boolean or float.
 * @class kSimpleField
 */
function kSimpleField(param, jqParentContainer){
	
	if(!param)
		return this;
	
	this.jqInput = jQuery('<input type="text" name="' + param.name + '" class="disabled" />');
	this.init(param, jqParentContainer);
}

kSimpleField.prototype = new kField();
kSimpleField.prototype.className = 'kSimpleField';

kSimpleField.prototype.initListeners = function() {
	kField.prototype.initListeners.apply(this, arguments);

	this.jqInput.click(delegate(this, this.enable));
	this.jqInput.keypress(delegate(this, this.inputEdited));
	this.jqInput.keyup(delegate(this, this.onCurrentValueChange));
};

kSimpleField.prototype.inputEdited = function(e) {
	if (!e.target)
		return;
	
	if (e.keyCode == 9) // ignore tab key
		return;

	this.enable();
};


/**
 * Class that represents request attribute of file type.
 * @class kFileField
 */
function kFileField(param, jqParentContainer){
	this.jqInput = jQuery('<input type="file" name="' + param.name + '" class="disabled" />');
	this.init(param, jqParentContainer);
}

kFileField.prototype = new kField();
kFileField.prototype.className = 'kFileField';

kFileField.prototype.initListeners = function() {
	kField.prototype.initListeners.apply(this, arguments);

	this.jqInput.click(delegate(this, this.inputEdited));
	this.jqInput.keypress(delegate(this, this.inputEdited));
	this.jqInput.keyup(delegate(this, this.onCurrentValueChange));
	this.jqInput.change(delegate(this, this.onCurrentValueChange));
};

kFileField.prototype.getType = function() {
	return 'file';
};

kFileField.prototype.inputEdited = function(e) {
	if (!e.target)
		return;
	
	if (e.keyCode == 9) // ignore tab key
		return;
	
	this.enable();
};

kFileField.prototype.setValue = function(value) {
	this.enable();
};

kFileField.prototype.unsetValue = function() {
	this.disable();
};


/**
 * Class that represents request object with all its attributes.
 * @class kObjectField
 */
function kObjectField(param, jqParentContainer, level){
	kTestMe.registerClass(param);
	this.level = level;
	this.jqInput = jQuery("<select class=\"disabled object-type\">");
	if(!param.isAbstract){
		this.jqInput.append("<option>" + param.type + "</option>");
	}
	else{
		this.jqInput.append("<option>Select Type</option>");
	}

	var jqObjectName = jQuery('<span class="object-name">' + param.name + ' (' + param.type + '):</span>');
	jqObjectName.click(delegate(this, this.click));
	
	var jqEdit = jQuery('<input type="button" class="edit-button button" value="Edit" />');
	jqEdit.click(delegate(this, this.click));

	this.jqLabel = jQuery('<label for="' + param.name + '" />');
	this.jqLabel.append(jqObjectName);
	this.jqLabel.append(jqEdit);
	
	this.init(param, jqParentContainer);

	if(kTestMe.subClassesLoaded(param.type)){
		this.loadSubClasses();
		return this;
	}
	
	jQuery.getJSON(
		'json/' + param.type + '-subclasses.json',
		delegate(this, this.onSubClassesLoad)
	);
}

kObjectField.prototype = new kField();
kObjectField.prototype.className = 'kObjectField';
kObjectField.prototype.level = null;
kObjectField.prototype.dialog = null;
kObjectField.prototype.isDialogOpen = false;
kObjectField.prototype.object = null;

kObjectField.prototype.initListeners = function() {
	kField.prototype.initListeners.apply(this, arguments);

	this.jqInput.focus(delegate(this, this.enable));
	this.jqInput.change(delegate(this, this.onTypeChange));
};

kObjectField.prototype.remove = function(){
	this.close(true);
	kField.prototype.remove.apply(this, arguments);
};

kObjectField.prototype.removeRequest = function(removeSubRequestAction){
	if(this.dialog != null)
		this.dialog.removeRequest(removeSubRequestAction);
};

kObjectField.prototype.getLevel = function(){
	return this.level;
};

kObjectField.prototype.onDialogValueChange = function(dialog){
	this.object = dialog.getValue();
	this.object['objectType'] = this.jqInput.val();
	
	this.onValueChange(this);
};

kObjectField.prototype.open = function(){
	if(this.dialog != null){
		this.dialog.open();
		this.onDialogOpen(this.dialog);
		return;
	}
	
	if(!this.param)
		return;
	
	this.dialog = new kObjectDialog(this);
	this.dialog.setParameters(this.param.properties);
	this.dialog.loadFields();
	this.dialog.loadValues(this.object);
	this.dialog.dialogValueChange(delegate(this, this.onDialogValueChange));
	this.dialog.dialogClose(delegate(this, this.onDialogClose));
	this.enable();

	this.onDialogOpen(this.dialog);
};

kObjectField.prototype.onDialogClose = function(){
	kField.prototype.onDialogClose.apply(this, arguments);
	this.isDialogOpen = false;
};

kObjectField.prototype.onDialogOpen = function(dialog){
	kField.prototype.onDialogOpen.apply(this, arguments);
	this.isDialogOpen = true;
};

kObjectField.prototype.enable = function(){
	if(this.enabled)
		return;

	if(this.object == null){
		this.object = {
			objectType: this.jqInput.val()
		};
	}

	this.jqInput.attr('name', this.inputName);	
	this.jqInput.removeAttr('readonly').removeClass('disabled');
	this.jqCheckBox.attr('checked', true);
	this.enabled = true;
	this.onCurrentValueChange();
};

kObjectField.prototype.disable = function(){
	this.object = null;
	kField.prototype.disable.apply(this, arguments);
};

kObjectField.prototype.close = function(remove){
	if(!this.dialog)
		return;
	
	this.dialog.close(remove);
	if(remove)
		this.dialog = null;
};

kObjectField.prototype.reload = function(){
	var reopen = this.isOpen();
	this.close(true);
	if(reopen)
		this.open();
};

kObjectField.prototype.isOpen = function(){
	return this.isDialogOpen;
};

kObjectField.prototype.click = function(){
	if(this.isOpen())
		this.close(false);
	else
		this.open();
};

kObjectField.prototype.onTypeChange = function(){
	this.param = kTestMe.getClass(this.jqInput.val());
	
	var newObject = {
		objectType: this.jqInput.val()
	};
	if(this.object != null && this.param != null && this.param.properties != null){
		for(var i = 0; i < this.param.properties.length; i++){
			var propertyName = this.param.properties[i].name;
			if(this.object[propertyName])
				newObject[propertyName] = this.object[propertyName];
		}
	}
	
	this.object = newObject;
	this.reload();
	this.onCurrentValueChange();
};

kObjectField.prototype.getType = function() {
	return this.jqInput.val();
};

kObjectField.prototype.onSubClassesLoad = function(subTypes){
	if(this.param == null)
		return;
	
	kTestMe.registerSubClasses(this.param.type, subTypes);
	this.loadSubClasses();
};

kObjectField.prototype.loadSubClasses = function(){
	if(this.param == null)
		return;
	
	var subTypes = kTestMe.getSubClasses(this.param.type);
	for(var i = 0; i < subTypes.length; i++){
		if(!subTypes[i].isAbstract)
			this.jqInput.append("<option>" + subTypes[i].type + "</option>");
	}
	
	if(this.object != null && this.object.objectType && this.jqInput.val() != this.object.objectType){
		this.jqInput.val(this.object.objectType);
		this.onTypeChange();
	}
};

kObjectField.prototype.getValue = function() {
	return this.object;
};

kObjectField.prototype.setValue = function(value) {
	this.enable();
	this.object = value;
	
	if(this.object != null && this.object.objectType){
		this.jqInput.val(this.object.objectType);
		this.onTypeChange();
	}
};

kObjectField.prototype.unsetValue = function() {
	this.disable();
	this.object = null;
};

kObjectField.prototype.setInputName = function(inputName) {
	this.inputName = inputName + ':objectType';
	if(this.enabled)
		this.jqInput.attr('name', this.inputName);
};



/**
 * Class that represents array of request attributes.
 * @class kArrayField
 */
function kArrayField(param, jqParentContainer, level){
	this.fields = {};
	this.level = level;
	this.jqParamsContainer = jqParentContainer;
	this.jqInput = jQuery('<input type="button" class="add-button button" value="Add" />');
	this.jqLabel = jQuery('<label for="' + param.name + '">' + param.name + ' array (' + param.arrayType.type + '):</label>');
	this.init(param, jqParentContainer);
	this.jqCheckBox.remove();
}

kArrayField.prototype = new kField();
kArrayField.prototype.className = 'kArrayField';
kArrayField.prototype.jqParamsContainer = null;
kArrayField.prototype.fieldsCount = 0;
kArrayField.prototype.fields = null;
kArrayField.prototype.level = null;
kArrayField.prototype.childDialog = null;
kArrayField.prototype.childFieldAddCallback = null;

kArrayField.prototype.initListeners = function() {
	kField.prototype.initListeners.apply(this, arguments);

	this.jqInput.click(delegate(this, this.add));
};

kArrayField.prototype.getType = function() {
	return 'array';
};

kArrayField.prototype.remove = function(){
	if(this.childDialog)
		this.childDialog.close(true);
	
	kField.prototype.remove.apply(this, arguments);
};

kArrayField.prototype.removeRequest = function(removeSubRequestAction){
	for(var item in this.fields)
	{
		var field = this.fields[item];
		if (field !== undefined || field !== null) {
			field.removeRequest(removeSubRequestAction);
		}
	}
};

kArrayField.prototype.getLevel = function(){
	return this.level;
};

kArrayField.prototype.add = function() {
	var field;
	var param = this.param.arrayType;
	param.name = 'item' + this.fieldsCount;
	this.fieldsCount++;
	this.enable();
	
	if (param.isComplexType)
	{
		if (param.isEnum || param.isStringEnum)
			field = new kEnumField(param, this.jqParamsContainer);
		else if (param.isArray)
			field = new kArrayField(param, this.jqParamsContainer, this.getLevel());
		else{
			field = new kObjectField(param, this.jqParamsContainer, this.getLevel());
		}
	}
	else if (param.isFile)
	{
		field = new kFileField(param, this.jqParamsContainer);
	}
	else
	{
		field = new kSimpleField(param, this.jqParamsContainer);
	}
	field.setParentName(this.getFullName());

	var jqRemove = jQuery('<input type="button" class="remove-button button" value="Remove" />');
	field.jqLabel.append(jqRemove);

	field.dialogOpen(delegate(this, this.onDialogOpen));
	field.dialogClose(delegate(this, this.onDialogClose));
	field.valueChange(delegate(this, this.onValueChange));
	
	this.fields[field.name] = field;
	
	var scope = this;
	jqRemove.click(function(){
		scope.removeChildField(field);
		field = null;
	});
	
	this.jqContainer.scrollTo(field.jqContainer, 1);
	this.onChildFieldAdd(field);
	return field;
};

kArrayField.prototype.childFieldAdd = function(callback) {
	kTestMe.log.debug("[" + this.className + ".childFieldAdd] Child field add event consumer registered [" + this.name + "]");
	this.childFieldAddCallback = callback;
};

kArrayField.prototype.onChildFieldAdd = function(field) {
	kTestMe.log.debug("[" + this.className + ".onChildFieldAdd] Child field added [" + this.name + "][" + field.name + "]");
	
	if(this.childFieldAddCallback)
		this.childFieldAddCallback.apply(this, [this, field]);
};

kArrayField.prototype.removeChildField = function(field) {
	field.remove();
	this.fields[field.name] = null;
};

kArrayField.prototype.onDialogOpen = function(dialog) {
	kTestMe.log.debug("[kArrayField.onDialogOpen] Dialog opened [" + dialog.name + "]");
	if(this.childDialog && this.childDialog != dialog){
		this.childDialog.close(false);
	}
		
	this.childDialog = dialog;
	kField.prototype.onDialogOpen.apply(this, arguments);
};

kArrayField.prototype.onDialogClose = function(dialog) {
	kTestMe.log.debug("[kArrayField.onDialogClose] Dialog closed [" + dialog.name + "]");
	this.childDialog = null;
	kField.prototype.onDialogClose.apply(this, arguments);
};

kArrayField.prototype.getValue = function() {
	var ret = new Array();
	for(var item in this.fields)
		if(this.fields[item] != null)
			ret.push(this.fields[item].getValue());
		
	return ret;
};

kArrayField.prototype.setValue = function(value) {
	this.enable();

	if(typeof(value) != 'object' || !(value instanceof Array))
		return;
	
	this.empty();
	
	for(var i = 0; i < value.length; i++){
		var field = this.add();
		field.setValue(value[i]);
	}
};

kArrayField.prototype.empty = function() {
	for(var item in this.fields)
		this.remove(this.fields[item]);
};

kArrayField.prototype.unsetValue = function() {
	this.disable();
	this.empty();
};


/**
 * Class that represents enum request attribute, string or int.
 * @class kEnumField
 */
function kEnumField(param, jqParentContainer){
	
	if(!param)
		return this;
	
	var jqInput = jQuery('<select name="' + param.name + '" class="disabled"></select>');
	jQuery.each(param.constants, function(i, constant) {
		jqInput.append('<option value="' + constant.defaultValue + '">' + constant.name + '</option>');
	});
	
	this.jqInput = jqInput;
	this.init(param, jqParentContainer);
}

kEnumField.prototype = new kField();
kEnumField.prototype.className = 'kEnumField';

kEnumField.prototype.getValueName = function() {
	return this.jqInput.find("option:selected").text();
}

kEnumField.prototype.initListeners = function() {
	kField.prototype.initListeners.apply(this, arguments);

	this.jqInput.focus(delegate(this, this.enable));
	this.jqInput.change(delegate(this, this.onCurrentValueChange));
};



/**
 * Class that represents link to request dialog.
 * @class kCallLink
 */
function kCallLink(jqParentContainer){
	var jqEdit = jQuery('<input type="button" class="edit-request-button button" value="Edit" />');
	jqEdit.click(delegate(this, this.click));

	this.jqLabel = jQuery('<span />');
	this.jqLabel.append(jqEdit);
	
	this.jqInput = jQuery('<input type="hidden"\>');
	this.jqHelp = jQuery('<span\>');
	
	var param = {
		name: kCallLink.index++,
		description: 'Single API request'
	};
	this.init(param, jqParentContainer);
	
	this.jqCheckBox.remove();
}

kCallLink.index = 1;
kCallLink.prototype = new kField();
kCallLink.prototype.className = 'kCallLink';
kCallLink.prototype.dialog = null;
kCallLink.prototype.isDialogOpen = false;
kCallLink.prototype.call = null;

kCallLink.prototype.remove = function(){
	this.close(true);
	kField.prototype.remove.apply(this, arguments);
};

kCallLink.prototype.removeRequest = function(removeSubRequestAction){
	if(this.dialog != null)
		this.dialog.removeRequest(removeSubRequestAction);
};

kCallLink.prototype.getLevel = function(){
	return 1;
};

kCallLink.prototype.onDialogValueChange = function(dialog){
	this.call = dialog.getValue();
	this.onValueChange(this);
};

kCallLink.prototype.open = function(){
	if(this.dialog == null){
		this.dialog = new kCall(this, this.name);
		this.dialog.loadFields();
		this.dialog.loadValues(this.call);
		this.dialog.dialogValueChange(delegate(this, this.onDialogValueChange));
		this.dialog.dialogClose(delegate(this, this.onDialogClose));
	}
	this.dialog.open();
	this.enable();

	this.onDialogOpen(this.dialog);
};

kCallLink.prototype.onDialogClose = function(){
	kField.prototype.onDialogClose.apply(this, arguments);
	this.isDialogOpen = false;
};

kCallLink.prototype.onDialogOpen = function(dialog){
	kField.prototype.onDialogOpen.apply(this, arguments);
	this.isDialogOpen = true;
};

kCallLink.prototype.enable = function(){
	if(this.enabled)
		return;
	
	if(!this.call){
		this.call = {};
	}

	this.enabled = true;
	this.onCurrentValueChange();
};

kCallLink.prototype.disable = function(){
	this.call = null;
	kField.prototype.disable.apply(this, arguments);
};

kCallLink.prototype.close = function(remove){
	if(this.dialog == null)
		return;
	
	this.dialog.close(remove);
	if(remove)
		this.dialog = null;
};

kCallLink.prototype.isOpen = function(){
	return this.isDialogOpen;
};

kCallLink.prototype.click = function(){
	if(this.isOpen())
		this.close(false);
	else
		this.open();
};

kCallLink.prototype.getValue = function() {
	return this.call;
};

kCallLink.prototype.setValue = function(value) {
	this.enable();
	this.call = value;
};

kCallLink.prototype.unsetValue = function() {
	this.disable();
	this.call = null;
};
