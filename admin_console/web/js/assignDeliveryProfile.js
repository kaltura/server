function addDeliveryProfileFormats() {

	currentFormats = getPlaybackProtocols();
	jQuery('#deliveryFormat').empty();
	
	var dpIdsStr = jQuery('#delivery_profile_ids').val();
	var dpIdsJson = jQuery.parseJSON(dpIdsStr);

	for(format in dpIdsJson) {
		if(currentFormats[format])
			delete currentFormats[format];
	}

	for(format in currentFormats) {
		jQuery('#deliveryFormat').append(new Option(currentFormats[format], format));
	}
}

function getPlaybackProtocols() {
	var playbackDict = {};
	playbackDict["http"] = "HTTP";
	playbackDict["rtmp"] = "RTMP";
	playbackDict["sl"] = "SILVER_LIGHT";
	playbackDict["applehttp"] = "APPLE_HTTP";
	playbackDict["rtsp"] = "RTSP";
	playbackDict["hds"] = "HDS";
	playbackDict["hls"] = "HLS";
	playbackDict["hdnetworkmanifest"] = "AKAMAI_HDS";
	playbackDict["hdnetwork"] = "AKAMAI_HD";
	return playbackDict;
}

function createDeliveryProfilesTable() 
{
	if(jQuery('#deliveryProfilesTable'))
		jQuery('#deliveryProfilesTable').remove();
	
	var dpIdsStr = jQuery('#delivery_profile_ids').val();
	var dpIdsJson = jQuery.parseJSON(dpIdsStr);
	var tbl = document.createElement('table');

	createTitles(tbl);
	for(format in dpIdsJson)
		addFormatRow(tbl, format, dpIdsJson[format]);

	jQuery('#delivery_profile_ids').after(tbl);
	$(tbl).attr('id', 'deliveryProfilesTable');
}

function createTitles(tbl) {
	var row = document.createElement('tr');	
	var tdFormat = document.createElement('td');
	tdFormat.innerHTML = "<b>Format</b>";
	var tdIds = document.createElement('td');
	tdIds.innerHTML = "<b>Delivery profiles</b>";
	var tdEdit = document.createElement('td');
	var tdRemove = document.createElement('td');
	$(row).append(tdFormat).append(tdIds).append(tdEdit).append(tdRemove);
	$(tbl).append(row);
}

function addFormatRow(tbl, format, deliveryProfileIds) 
{
	var row = document.createElement('tr');	
	var tdFormat = document.createElement('td');
	tdFormat.innerHTML = format;
	var tdDPIds = document.createElement('td');
	tdDPIds.innerHTML = deliveryProfileIds;
	var tdEdit = document.createElement('td');
	tdEdit.innerHTML = '<button onclick="editDeliveryProfile(\'' +format+'\',[' + deliveryProfileIds+ ']);">Edit</button>';
	var tdRemove = document.createElement('td');
	tdRemove.innerHTML = '<button onclick="removeFormat(\'' +format+'\');">Remove</button>';

	$(row).append(tdFormat).append(tdDPIds).append(tdEdit).append(tdRemove);
	$(tbl).append(row);
}

function addDeliveryProfile() {
	var deliveryFormat = jQuery('#deliveryFormat').val();
	editDeliveryProfile(deliveryFormat, null);
}

function removeFormat(format) {
	var dpIdsStr = $("#delivery_profile_ids")[0].value;
	var dpIdsObj = jQuery.parseJSON(dpIdsStr);

	delete dpIdsObj[format];

	$("#delivery_profile_ids")[0].value = JSON.stringify(dpIdsObj);
	updatedUI();
}

function okPressed(format) {

	var selectedValues = [];
	$("#selectedValues option").each(function() {selectedValues.push(parseInt(this.value));});
	
	var dpIdsStr = $("#delivery_profile_ids")[0].value;
	var dpIdsObj = jQuery.parseJSON(dpIdsStr);

	dpIdsObj[format] = selectedValues;
	$("#delivery_profile_ids")[0].value = JSON.stringify(dpIdsObj);

	updatedUI();
}

function updatedUI() {
	createDeliveryProfilesTable();
	addDeliveryProfileFormats();
}

