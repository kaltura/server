$(function() {
	if ($('iframe.auto-height') && $('iframe.auto-height').size() > 0) {
		autoSizeIframe();
		$(window).resize(autoSizeIframe);
	}
});

function autoSizeIframe(){
	var availableHeight = $(window).height();
	var takenHeight =  $('#kmcHeader').outerHeight(true) + $('#sub-header').outerHeight(true);
	var height = availableHeight - takenHeight;
	$('iframe.auto-height').height(height);
	$('#wrapper').height(height); // fixes weird scrolling
}

function supressFormSubmit(e){

	if(window.event) // IE
	{
		keynum = e.keyCode;
	}
	else if(e.which) // Netscape/Firefox/Opera
	{
		keynum = e.which;
	}

	if(keynum == 13)
		return false;
		
	return true;
}