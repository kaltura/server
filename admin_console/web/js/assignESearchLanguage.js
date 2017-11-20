function createESearchlanguagesTable()
{
	if(jQuery('#eSearchLanguagesTable'))
		jQuery('#eSearchLanguagesTable').remove();

	var tbl = document.createElement('table');
	createESearchLanguagesTitles(tbl);

	addRow(tbl);

	jQuery('#editESearchLanguages').before(tbl);
	$(tbl).attr('id', 'eSearchLanguagesTable');

}

function addRow(tbl) {
	var languagesStr = jQuery("#e_search_languages").val();
	var languagesJson = jQuery.parseJSON(languagesStr);
	for(language in languagesJson) {
		addLanguageRow(tbl, languagesJson[language]);
	}
}


function createESearchLanguagesTitles(tbl) {
	var row = document.createElement('tr');
	var tdLanguage = document.createElement('td');

	tdLanguage.innerHTML = "<b>Language</b>";
	var tdRemove = document.createElement('td');
	$(row).append(tdLanguage).append(tdRemove);
	$(tbl).append(row);
}

function addLanguageRow(tbl, language)
{
	var row = document.createElement('tr');
	var tdLanguage = document.createElement('td');
	tdLanguage.innerHTML = language;
	var tdRemove = document.createElement('td');
	tdRemove.innerHTML = '<button onclick="removeLanguage(\'' +language+'\');">Remove</button>';

	$(row).append(tdLanguage).append(tdRemove);
	$(tbl).append(row);
}

function addESearchLanguage() {
	assignSearchLanguage();
}

function removeLanguage(language) {
	var languageStr = $("#e_search_languages")[0].value;
	var languageObj = jQuery.parseJSON(languageStr);
	delete languageObj[language];
	$("#e_search_languages")[0].value = JSON.stringify(languageObj);
	updatedElasticUI();
}

function okSearchLanguageButtonPressed() {

	var selectedValues = {};
	$("#selectedValues option").each(function() {
		selectedValues[this.value] = this.value;});

	var languagesObj = jQuery.parseJSON("{}");

		for (language in selectedValues)
			languagesObj[language] = language;

	$("#e_search_languages")[0].value = JSON.stringify(languagesObj);
	updatedElasticUI();
}

function updatedElasticUI() {
	createESearchlanguagesTable();
}