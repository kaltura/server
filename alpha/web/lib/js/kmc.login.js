// jQuery Cookie plugin
// https://github.com/carhartl/jquery-cookie
(function(e,h,m){var n=/\+/g;function o(c){return c}function p(c){return decodeURIComponent(c.replace(n,' '))}var d=e.cookie=function(c,b,a){if(b!==m){a=e.extend({},d.defaults,a);if(b===null){a.expires=-1}if(typeof a.expires==='number'){var q=a.expires,i=a.expires=new Date();i.setDate(i.getDate()+q)}b=d.json?JSON.stringify(b):String(b);return(h.cookie=[encodeURIComponent(c),'=',d.raw?b:encodeURIComponent(b),a.expires?'; expires='+a.expires.toUTCString():'',a.path?'; path='+a.path:'',a.domain?'; domain='+a.domain:'',a.secure?'; secure':''].join(''))}var j=d.raw?o:p;var k=h.cookie.split('; ');for(var f=0,g;(g=k[f]&&k[f].split('='));f++){if(j(g.shift())===c){var l=j(g.join('='));return d.json?JSON.parse(l):l}}return null};d.defaults={};e.removeCookie=function(c,b){if(e.cookie(c,b)!==null){e.cookie(c,null,b);return true}return false}})(jQuery,document);

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

function loginSuccess() {
	var state = location.hash || "" ;
	window.location = options.service_url + "/index.php/kmc/kmc2" + state;	
}

// If we have ks & partner_id cookies, redirect to kmc
if( $.cookie('kmcks') && $.cookie('pid') ) {
	loginSuccess();
} else {
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
}