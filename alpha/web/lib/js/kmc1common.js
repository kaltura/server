// cookie functions
function getCookie (name) {
  var arg = name + "=";
  var alen = arg.length;
  var clen = document.cookie.length;
  var i = 0;
  while (i < clen) {
    var j = i + alen;
    if (document.cookie.substring(i, j) == arg)
      return getCookieVal (j);
    i = document.cookie.indexOf(" ", i) + 1;
    if (i == 0) break; 
  }
  return null;
}

function getCookieVal (offset) {
	  var endstr = document.cookie.indexOf (";", offset);
	  if (endstr == -1)
	    endstr = document.cookie.length;
	  return unescape(document.cookie.substring(offset, endstr));
	}

function setCookie (name,value,expiry_in_seconds,path,domain,secure) {
	if ( expiry_in_seconds )
	{
		// set time, it's in milliseconds
		var today = new Date();
		today.setTime( today.getTime() );

		var expires = new Date( today.getTime() + (expiry_in_seconds*1000) ); // milliseconds
	}
	else
	{
		expires = null;
	}
  document.cookie = name + "=" + escape (value) +
    ((expires) ? "; expires=" + expires.toGMTString() : "") +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    ((secure) ? "; secure" : "");
}

function deleteCookie (name,path,domain) {

  if (getCookie(name)) {
  	cookie_name = "" + document.cookie;
  	setCookie ( name , "" , 0 , path , domain , ""  );
  }
}

function empty(str)
{
return (str==null || str == "" );	
}


function createEmbedCode ( src , div_id , width , height , flashvars_string )
{
	
}

/* copy to clipboard */
function copyToClipboard(inElement){
	//var inElement = document.getElementById(inElement);
	inElement.select();
	if (inElement.createTextRange) {
		var range = inElement.createTextRange();
		if (range)
			range.execCommand('Copy');
	} else {
		var flashcopier = 'flashcopier';
		if(!document.getElementById(flashcopier))
			//jQuery("body").log("body").append("<div id='flashcopier'></div>");
			jQuery("body").append("<div id='flashcopier'></div>");
		document.getElementById(flashcopier).innerHTML = '';
		var divinfo = '<embed src="/lib/flash/_clipboard.swf" FlashVars="clipboard='+encodeURIComponent(inElement.value)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
		document.getElementById(flashcopier).innerHTML = divinfo;
	}
};
