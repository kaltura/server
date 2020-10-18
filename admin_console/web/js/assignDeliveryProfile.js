function addDeliveryProfileFormats() {

	currentFormats = getPlaybackProtocols();
	jQuery('#deliveryFormat').empty();

	var dpIdsStr = jQuery(getTag('VOD')).val();
	var dpIdsJsonVOD = jQuery.parseJSON(dpIdsStr);
	dpIdsStr = jQuery(getTag('Live')).val();
	var dpIdsJsonLive = jQuery.parseJSON(dpIdsStr);


	for(format in dpIdsJsonVOD) {
		if(currentFormats[format] && dpIdsJsonLive && dpIdsJsonLive.hasOwnProperty(format))
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
	playbackDict["mpegdash"] = "MPEG_DASH";
	playbackDict["download"] = "DOWNLOAD";
	return playbackDict;
}

function createDeliveryProfilesTable()
{
	if(jQuery('#deliveryProfilesTable'))
		jQuery('#deliveryProfilesTable').remove();

	var tbl = document.createElement('table');
	createTitles(tbl);

	addRowWithType(tbl, 'VOD');
	addRowWithType(tbl,'Live');

	jQuery('#delivery_profile_ids').after(tbl);
	$(tbl).attr('id', 'deliveryProfilesTable');
}

function addRowWithType(tbl, type) {
	var dpIdsStr = jQuery(getTag(type)).val();
	var dpIdsJson = jQuery.parseJSON(dpIdsStr);
	for(format in dpIdsJson) {
		addFormatRow(tbl, format, dpIdsJson[format], type);
	}
}


function createTitles(tbl) {
	var row = document.createElement('tr');
	var tdFormat = document.createElement('td');
	tdFormat.innerHTML = "<b>Format</b>";
	var tdType = document.createElement('td');
	tdType.innerHTML = "<b>Type</b>";
	var tdIds = document.createElement('td');
	tdIds.innerHTML = "<b>Delivery profiles</b>";
	var tdEdit = document.createElement('td');
	var tdRemove = document.createElement('td');
	$(row).append(tdFormat).append(tdType).append(tdIds).append(tdEdit).append(tdRemove);
	$(tbl).append(row);
}

function addFormatRow(tbl, format, deliveryProfileIds, type)
{
	var row = document.createElement('tr');
	var tdFormat = document.createElement('td');
	tdFormat.innerHTML = format;
	var tdType = document.createElement('td');
	tdType.innerHTML = type;
	var tdDPIds = document.createElement('td');
	tdDPIds.innerHTML = deliveryProfileIds;
	var tdEdit = document.createElement('td');
	tdEdit.innerHTML = '<button onclick="assignDeliveryProfile(\'' +format+'\',[' + deliveryProfileIds+ '], \'' +type+'\');">Edit</button>';
	var tdRemove = document.createElement('td');
	tdRemove.innerHTML = '<button onclick="removeFormat(\'' +format+'\', \'' +type+'\');">Remove</button>';

	$(row).append(tdFormat).append(tdType).append(tdDPIds).append(tdEdit).append(tdRemove);
	$(tbl).append(row);
}

function addDeliveryProfile() {
	var deliveryFormat = jQuery('#deliveryFormat').val();
	var deliveryType = jQuery('#delivery_profile_type').val();
	assignDeliveryProfile(deliveryFormat, null, deliveryType);
}

function removeFormat(format, type) {
	var dpIdsStr = $(getTag(type))[0].value;
	var dpIdsObj = jQuery.parseJSON(dpIdsStr);
	delete dpIdsObj[format];
	$(getTag(type))[0].value = JSON.stringify(dpIdsObj);
	updatedUI();
}

function okPressed(format, type) {

	var selectedValues = [];
	$("#selectedValues option").each(function() {selectedValues.push(parseInt(this.value));});
	if(!selectedValues.length)
		return;
	
	if(type == null)
		type = "VOD";
	
	var dpIdsStr = $(getTag(type))[0].value;
	var dpIdsObj = jQuery.parseJSON(dpIdsStr);

	if(dpIdsObj == null)
		dpIdsObj = jQuery.parseJSON("{}");

	dpIdsObj[format] = selectedValues;

	$(getTag(type))[0].value = JSON.stringify(dpIdsObj);
	updatedUI();
}

function updatedUI() {
	createDeliveryProfilesTable();
	addDeliveryProfileFormats();
}

function getTag(type) {
	if (type == 'VOD')
		return "#delivery_profile_ids";
	else if (type == 'Live')
		return "#live_delivery_profile_ids";
}