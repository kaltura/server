/*  
	WResize is the jQuery plugin for fixing the IE window resize bug
 	http://noteslog.com/ 
*/
(function($){$.fn.wresize=function(f){version='1.1';wresize={fired:false,width:0};function resizeOnce(){if($.browser.msie){if(!wresize.fired){wresize.fired=true}else{var version=parseInt($.browser.version,10);wresize.fired=false;if(version<7){return false}else if(version==7){var width=$(window).width();if(width!=wresize.width){wresize.width=width;return false}}}}return true}function handleWResize(e){if(resizeOnce()){return f.apply(this,[e])}}this.each(function(){if(this==window){$(this).resize(handleWResize)}else{$(this).resize(f)}});return this}})(jQuery);



/* scrollTo jQuery extention */
;(function(h){var m=h.scrollTo=function(b,c,g){h(window).scrollTo(b,c,g)};m.defaults={axis:'y',duration:1};m.window=function(b){return h(window).scrollable()};h.fn.scrollable=function(){return this.map(function(){var b=this.parentWindow||this.defaultView,c=this.nodeName=='#document'?b.frameElement||b:this,g=c.contentDocument||(c.contentWindow||c).document,i=c.setInterval;return c.nodeName=='IFRAME'||i&&h.browser.safari?g.body:i?g.documentElement:this})};h.fn.scrollTo=function(r,j,a){if(typeof j=='object'){a=j;j=0}if(typeof a=='function')a={onAfter:a};a=h.extend({},m.defaults,a);j=j||a.speed||a.duration;a.queue=a.queue&&a.axis.length>1;if(a.queue)j/=2;a.offset=n(a.offset);a.over=n(a.over);return this.scrollable().each(function(){var k=this,o=h(k),d=r,l,e={},p=o.is('html,body');switch(typeof d){case'number':case'string':if(/^([+-]=)?\d+(px)?$/.test(d)){d=n(d);break}d=h(d,this);case'object':if(d.is||d.style)l=(d=h(d)).offset()}h.each(a.axis.split(''),function(b,c){var g=c=='x'?'Left':'Top',i=g.toLowerCase(),f='scroll'+g,s=k[f],t=c=='x'?'Width':'Height',v=t.toLowerCase();if(l){e[f]=l[i]+(p?0:s-o.offset()[i]);if(a.margin){e[f]-=parseInt(d.css('margin'+g))||0;e[f]-=parseInt(d.css('border'+g+'Width'))||0}e[f]+=a.offset[i]||0;if(a.over[i])e[f]+=d[v]()*a.over[i]}else e[f]=d[i];if(/^\d+$/.test(e[f]))e[f]=e[f]<=0?0:Math.min(e[f],u(t));if(!b&&a.queue){if(s!=e[f])q(a.onAfterFirst);delete e[f]}});q(a.onAfter);function q(b){o.animate(e,j,a.easing,b&&function(){b.call(this,r,a)})};function u(b){var c='scroll'+b,g=k.ownerDocument;return p?Math.max(g.documentElement[c],g.body[c]):k[c]}}).end()};function n(b){return typeof b=='object'?b:{top:b,left:b}}})(jQuery);



function content_resize(){
   var w = $( window );
   var H = w.height(); 
   var W = w.width(); 
   $( '#flash_wrap' ).height(H-38);
   $('#server_wrap iframe').height(H-38);
}

/* copy to clipboard */
/*
function copyToClipboard(inElement){
	var inElement = document.getElementById(inElement);
	inElement.select();
	if (inElement.createTextRange) {
		var range = inElement.createTextRange();
		if (range)
			range.execCommand('Copy');
	} else {
		var flashcopier = 'flashcopier';
		if(!document.getElementById(flashcopier))
			$("body").append("<div id='flashcopier'></div>");
		document.getElementById(flashcopier).innerHTML = '';
		var divinfo = '<embed src="_clipboard.swf" FlashVars="clipboard='+encodeURIComponent(inElement.value)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
 	}
}; */

function copyToClipboard(inElementId){
	
	var inElement = document.getElementById(inElementId);
	inElement.select();
	if (inElement.createTextRange) {
		var range = inElement.createTextRange();
		if (range)
			range.execCommand('Copy');
	} else {
		var flashcopier = 'flashcopier';
		if(!document.getElementById(flashcopier))
			$("body").append("<div id='flashcopier'></div>");
		document.getElementById(flashcopier).innerHTML = '';
 	}
};

function onTabChange()
{
	kalturaCloseModalBox();
	loadModule(next_module, partner_id, subpid, user_id, ks, screen_name, email);
}
    
$(document).ready(function(){
    $("a#dashboard").addClass('active');
    /* KMC tabs */
    $("#kmcHeader ul li a").click(function(){
    	$("#kmcHeader ul li a").removeClass('active');
//    	var bolds = $("#header ul.main_tabs li").find('b');
//    	$(bolds).remove(); 
		kalturaCloseModalBox();
		selected_uiconfId = null;
		next_module = this.id;
    	$(this).addClass('active');
//    	$(li).addClass('active');
//    	$(li).prepend('<b></b>');
		if(this.id != 'server' && this.id != 'developer')
		{
		  $('#server_wrap').hide();
		  $('#flash_wrap').show();
		  loadModule(this.id, partner_id, subpid, user_id, ks, screen_name, email);
		  //document.getElementById("kcms").saveAndClose();
		  return false;
		}
	    	else
		{
		  $('#server_wrap iframe').attr('src', this.href);
		  $('#flash_wrap').hide();
		  $('#server_wrap').show();
		  return false;
		}
		return false;
    });
    
	flashMovieTimeout = setInterval( function(){checkIfFlashLoaded()}, 200 );
		function checkIfFlashLoaded(){
	  	if( $( '#flash_wrap object' ).length || $( '#flash_wrap embed' ).length){
	   	content_resize();
	   	clearInterval(flashMovieTimeout);
	   	flashMovieTimeout = null;
	  	}
	}
    $( window ).wresize( content_resize ); 
 
    content_resize();
/*	
	var flashvars = {};
	flashvars.name = "kmc";
	var params = {};
	if (!jQuery.browser.msie)
		params.wmode = "opaque";
	params.bgcolor = "0d0c25";
	params.allowfullscreen = "true";
	var attributes = {};
	swfobject.embedSWF("flash/background.swf", "kcms", "100%", "100%", "9.0.0", "flash/expressInstall.swf", flashvars, params, attributes);
	*/
	
	$('#main div.content div.contwrap div.dataContent div.dataTitle a').click(function(){ $.scrollTo( '#main', 800 ); return false; });
	
	$("#contentSection dl a").each(function(){
		$(this).attr("href", "#contentSection" + $(this).prev("span").text() );
	}).click(function(){
		var target = $(this).attr("href");
		target = target.replace(/\./g, "");
		$.scrollTo( target, 800,{onAfter:function(){ }});
		return false;
	});
});


