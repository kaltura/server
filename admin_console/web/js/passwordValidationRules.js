function passwordValidationRulesSaveButtonPressed()
{
	let regexObject = jQuery.parseJSON("{}");
	jQuery("fieldset[class=regex-rule]").each(function() {
		let regex = jQuery(this).find("input[id=regex]").val();
		let description = jQuery(this).find("input[id=description]").val();
		regexObject[regex] = description;
	});

	jQuery("input[name=password_structure_validations]").val(JSON.stringify(regexObject));
	createPasswordRegexTable();
}

function createPasswordRegexTable()
{
	if (jQuery('#passwordRegexTable'))
	{
		jQuery('#passwordRegexTable').remove();
	}
	let tbl = document.createElement('table');
	createPasswordRegexTableTitles(tbl);
	addRegexRowsFromJson(tbl);

	jQuery("[id=password_structure_validations_edit]").before(tbl);
	jQuery(tbl).attr('id', 'passwordRegexTable');

}

function addRegexRowsFromJson(tbl)
{
	let passwordStructureValidationsStr = jQuery("#password_structure_validations").val();
	let passwordStructureValidationsJson = jQuery.parseJSON(passwordStructureValidationsStr);
	for (let passwordRegex in passwordStructureValidationsJson)
	{
		addRegexRow(tbl, passwordRegex);
	}
}


function createPasswordRegexTableTitles(tbl)
{
	let tdRegex = document.createElement('td');
	tdRegex.innerHTML = "<b>Regex Rules</b>";

	let row = document.createElement('tr');
	jQuery(row).append(tdRegex);
	jQuery(tbl).append(row);
}

function addRegexRow(tbl, passwordRegex)
{
	let tdRegex = document.createElement('td');
	tdRegex.innerHTML = passwordRegex;
	if (!passwordRegex)
	{
		jQuery(tdRegex).attr('style', 'height: 15px;');
	}

	let row = document.createElement('tr');
	jQuery(row).append(tdRegex);
	jQuery(tbl).append(row);
}
