// jQuery Cookie plugin
// https://github.com/carhartl/jquery-cookie
(function(e,h,m){var n=/\+/g;function o(c){return c}function p(c){return decodeURIComponent(c.replace(n,' '))}var d=e.cookie=function(c,b,a){if(b!==m){a=e.extend({},d.defaults,a);if(b===null){a.expires=-1}if(typeof a.expires==='number'){var q=a.expires,i=a.expires=new Date();i.setDate(i.getDate()+q)}b=d.json?JSON.stringify(b):String(b);return(h.cookie=[encodeURIComponent(c),'=',d.raw?b:encodeURIComponent(b),a.expires?'; expires='+a.expires.toUTCString():'',a.path?'; path='+a.path:'',a.domain?'; domain='+a.domain:'',a.secure?'; secure':''].join(''))}var j=d.raw?o:p;var k=h.cookie.split('; ');for(var f=0,g;(g=k[f]&&k[f].split('='));f++){if(j(g.shift())===c){var l=j(g.join('='));return d.json?JSON.parse(l):l}}return null};d.defaults={};e.removeCookie=function(c,b){if(e.cookie(c,b)!==null){e.cookie(c,null,b);return true}return false}})(jQuery,document);

// KMC Login page
if( typeof $ == 'undefined' ) $ = jQuery;

function loginF( remMe, partner_id, subp_id, uid, ks , screen_name, email ) {

	// Set cookie options
	var options = {
		// Save data as raw
		raw: true,
		// Set path
		path: "/",
		// Set expiration time to 1 day
		expires: 1,
		// Set secure cookie flag based on domain protocol
		secure: ( window.location.protocol === "https:" ) ? true : false
	};

	$.cookie("kmcks", ks, options);
	$.cookie("pid", partner_id, options);
	$.cookie("subpid", subp_id, options);

	loginSuccess();
}

function gotoSignup() {
	window.location = options.service_url + "/index.php/kmc/signup";
}

function loginSuccess() {
	var state = location.hash || "" ;
	window.location = '/kmc/index.php/index'
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