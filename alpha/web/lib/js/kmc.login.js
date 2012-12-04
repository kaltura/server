// KMC Login page
if( typeof $ == 'undefined' ) $ = jQuery;

function loginF( remMe, partner_id, subp_id, uid, ks , screen_name, email ) {

	// Extlogin URL
	var url = options.service_url + '/index.php/kmc/extlogin';
	// URL Protocol
	var service_url_protocol = options.service_url.split("://")[0];

	// If login needs to be secured, change extlogin url to https	
	if( options.secure_login && service_url_protocol == 'http' ) {
		url = url.replace(/http:/g, "https:");
	}

	// Setup input fields
	var ks_input = $('<input />').attr({
		'type': 'hidden',
		'name': 'ks',
		'value': ks
	});
	var partner_id_input = $('<input />').attr({
		'type': 'hidden',
		'name': 'partner_id',
		'value': partner_id // grab the selected partner id
	});

	var $form = $('<form />')
				.attr({
					'action': url, 
					'method': 'post',
					'style': 'display: none'
				})
				.append( ks_input, partner_id_input );

	// Submit the form
	$('body').append( $form );
	$form[0].submit();	
}

function gotoSignup() {
	window.location = options.service_url + "/index.php/kmc/signup";
}

// Show login form
var flashVars = {
	loginF: "loginF",
	closeF: "closeLoginF",
	urchinNumber: "UA-12055206-1",
	srvurl: "api_v3/index.php"
}

$.extend( flashVars, options.flashVars );

var params = {
	allowscriptaccess: "always",
	allownetworking: "all",
	bgcolor: "#272929",
	quality: "high",
	wmode: "window" ,
	movie: options.swf_url
};
swfobject.embedSWF(options.swf_url, "login_swf", "384", "350", "10.0.0", options.service_url + "/expressInstall.swf", flashVars, params);