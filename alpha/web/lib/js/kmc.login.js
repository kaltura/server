// jQuery Cookie plugin
// https://github.com/carhartl/jquery-cookie
(function(e,h,m){var n=/\+/g;function o(c){return c}function p(c){return decodeURIComponent(c.replace(n,' '))}var d=e.cookie=function(c,b,a){if(b!==m){a=e.extend({},d.defaults,a);if(b===null){a.expires=-1}if(typeof a.expires==='number'){var q=a.expires,i=a.expires=new Date();i.setDate(i.getDate()+q)}b=d.json?JSON.stringify(b):String(b);return(h.cookie=[encodeURIComponent(c),'=',d.raw?b:encodeURIComponent(b),a.expires?'; expires='+a.expires.toUTCString():'',a.path?'; path='+a.path:'',a.domain?'; domain='+a.domain:'',a.secure?'; secure':''].join(''))}var j=d.raw?o:p;var k=h.cookie.split('; ');for(var f=0,g;(g=k[f]&&k[f].split('='));f++){if(j(g.shift())===c){var l=j(g.join('='));return d.json?JSON.parse(l):l}}return null};d.defaults={};e.removeCookie=function(c,b){if(e.cookie(c,b)!==null){e.cookie(c,null,b);return true}return false}})(jQuery,document);

// KMC Login page

function empty( val ) {
	if( val === null )
		return true;
	return false;
}

function loginF( remMe, partner_id, subp_id, uid, ks , screen_name, email ) {

	var has_cookie = false;

	if ( partner_id == null ) {
		partner_id = $.cookie( "pid" );
		subp_id = $.cookie( "subpid" );
		uid = $.cookie( "uid" );
		ks = $.cookie( "kmcks" );
		screen_name = $.cookie( "screen_name" );
		email = $.cookie( "email" );
		// if any of the required params is null - return false and the login page will be displayed
		if ( empty(partner_id) || empty(subp_id) || empty(uid) || empty(ks) )
			return false;

		has_cookie = true;
	}

	// Set cookie options
	var options = {
		// Save data as raw
		raw: true,
		// Set path
		path: "/",
		// Set expiration time for cookie ( Number - days )
		expires: ( remMe ) ? 30 : 1,
		// Set secure cookie flag based on domain protocol
		secure: ( window.location.protocol === "https:" ) ? true : false
	};

	if ( ! has_cookie ) {
		$.cookie("kmcks", ks, options);
		/*
		$.cookie("pid", partner_id, options);
		$.cookie("subpid", subp_id, options);
		$.cookie("uid", uid , options);
		$.cookie("screen_name", screen_name, options);
		$.cookie("email", email, options);
		*/
	}

	var state = location.hash || "" ;
	window.location = service_url + "/index.php/kmc/kmc2" + state;

	// TODO - send by post using form1
	return true;			
}

function closeLoginF() {}

function gotoSignup() {
	window.location = service_url + "/index.php/kmc/signup";
}