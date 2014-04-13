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

// Prevent the page from being contained inside a frame
if ( top != window ) { top.location = window.location; }
