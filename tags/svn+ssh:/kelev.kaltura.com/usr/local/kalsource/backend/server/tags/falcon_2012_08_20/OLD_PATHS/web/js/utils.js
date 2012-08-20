//------------------------------
// LAYOUT RELATED STUFF
//------------------------------
ieflashfix = function(){
	//the code only executes on browsers which support getElementsByTagName and outerHTML (Internet Explorer, Opera and Safari - but not Firefox).
	if (document.getElementsByTagName && document.body.outerHTML) {
    // repeat code for each affected tag
    var tags = ['object','embed','applet'];

	    for (var i in tags) {
	        // get all elements with tag
	        var objs = document.getElementsByTagName(tags[i]);
	
	        for (var j=0;j < objs.length;j++) {
	            var obj = objs.item(j);

	            // find param tags within object
	            var params = obj.getElementsByTagName('param');
	            var inner = '';
	
	            // if there are params, but param tags can't be found within innerHTML
	            if (params.length && !/<param/i.test(obj.innerHTML))
	                // add all param tags to 'inner' string
	                for (var x=0;x < params.length;x++)
	                    inner += params.item(x).outerHTML;
	            // put 'inner' string with param tags in the middle of the outerHTML
	            obj.outerHTML = obj.outerHTML.replace('>', '>' + inner);
	        }
	    }
	}
}

/**
  *  @name    jQuery Logging plugin
  *  @author  Dominic Mitchell
  *  @url     http://happygiraffe.net/blog/archives/2007/09/26/jquery-logging
  *  Example: $(root).find('input:checkbox').log("sources to uncheck").removeAttr("checked");
  */
	jQuery.fn.log = function(msg){
		if (window.console || console.firebug){
			msg = msg || '';
			if(msg !== '') msg += ': ';
			console.log("%s%o", msg, this);
		}
		return this;
	};


detectMacXFF = function () {
  var userAgent = navigator.userAgent.toLowerCase();
  if (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox')!=-1) {
    return true;
  }
}

function copy(inElement){
	var inElement = $(inElement);
	inElement.select();
	if (inElement.createTextRange) {
		var range = inElement.createTextRange();
		if (range)
			range.execCommand('Copy');
	} else {
		var flashcopier = 'flashcopier';
		if(!$(flashcopier))
			jQuery("body").append("<div id='flashcopier'></div>");
		$(flashcopier).innerHTML = '';
		var divinfo = '<embed src="/images/flash/_clipboard.swf" FlashVars="clipboard='+encodeURIComponent(inElement.value)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
		$(flashcopier).innerHTML = divinfo;
  }
}

var defSearchInputValue;

jQuery.noConflict();
jQuery(document).ready(function(){	

jQuery('.btt').click(function(){jQuery('#wrap').ScrollTo(700); return false; });  // each link which has the .btt class, scrolls nicely to page top. (#wrap)

/* scrollTo jQuery extention */
eval(function(p,a,c,k,e,d){e=function(c){return(c<a?"":e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('3.R=6(e){7 l=0;7 t=0;7 w=3.a(3.X(e,\'1e\'));7 h=3.a(3.X(e,\'1f\'));7 m=e.L;7 B=e.F;1a(e.S){l+=e.T+(e.8?3.a(e.8.W):0);t+=e.V+(e.8?3.a(e.8.10):0);e=e.S}l+=e.T+(e.8?3.a(e.8.W):0);t+=e.V+(e.8?3.a(e.8.10):0);c{x:l,y:t,w:w,h:h,m:m,B:B}};3.1d=6(e){b(e){w=e.k;h=e.C}f{w=(d.Y)?d.Y:(1.4&&1.4.k)?1.4.k:1.9.L;h=(d.H)?d.H:(1.4&&1.4.C)?1.4.C:1.9.F}c{w:w,h:h}};3.U=6(e){b(e){t=e.i;l=e.A;w=e.r;h=e.D}f{b(1.4&&1.4.i){t=1.4.i;l=1.4.A;w=1.4.r;h=1.4.D}f b(1.9){t=1.9.i;l=1.9.A;w=1.9.r;h=1.9.D}}c{t:t,l:l,w:w,h:h}};3.a=6(v){v=12(v);c 14(v)?0:v};3.16.E=6(s){o=3.17(s);c u.18(6(){n 3.P.E(u,o)})};3.P.E=6(e,o){7 z=u;z.o=o;z.e=e;z.p=3.R(e);z.s=3.U();z.J=6(){1b(z.j);z.j=1c};z.t=(n N).Z();z.M=6(){7 t=(n N).Z();7 p=(t-z.t)/z.o.I;b(t>=z.o.I+z.t){z.J();11(6(){z.q(z.p.y,z.p.x)},13)}f{G=((-g.O(p*g.Q)/2)+0.5)*(z.p.y-z.s.t)+z.s.t;K=((-g.O(p*g.Q)/2)+0.5)*(z.p.x-z.s.l)+z.s.l;z.q(G,K)}};z.q=6(t,l){d.19(l,t)};z.j=15(6(){z.M()},13)};',62,78,'|document||jQuery|documentElement||function|var|currentStyle|body|intval|if|return|window||else|Math||scrollTop|timer|clientWidth||wb|new|||scroll|scrollWidth|||this||||||scrollLeft|hb|clientHeight|scrollHeight|ScrollTo|offsetHeight|st|innerHeight|duration|clear|sl|offsetWidth|step|Date|cos|fx|PI|getPos|offsetParent|offsetLeft|getScroll|offsetTop|borderLeftWidth|css|innerWidth|getTime|borderTopWidth|setTimeout|parseInt||isNaN|setInterval|fn|speed|each|scrollTo|while|clearInterval|null|getClient|width|height'.split('|'),0,{}))

jQuery("#navBarSearchInput")  // handles to Header search feild.
	.keydown(function(e){
		if (e.keyCode == 13){
			var keywords = this.value;
			keywords =  ( keywords == this.defaultValue ? '' : '?keywords=' + keywords );
			location = MODULE_SEARCH + keywords;
		}
	})
	.focus(function(){ 
		if( this.value == this.defaultValue )
			this.value ='';
	})
	.blur(function(){
		if( this.value == '' )
			this.value = this.defaultValue;
	});
	
jQuery("#navBarSearcGo").click(function(){  // bind the "GO" button
	var keywords = this.value;
	keywords =  ( keywords == this.defaultValue ? '' : '?keywords=' + keywords );
	location = MODULE_SEARCH + keywords;
});


/*--- cutomized alert message ---*/
/*
alert = function(x){
	updateStatusBar ( x , 4 );
}
*/

});

function onClickNavBarSignIn()
{
	new WizardDialog(MODULE_LOGIN + '/signinAjaxShowForm', {'width':'410px', 'height':'480px', 'autoStart' : false});
}

function onClickNavBarSignOut()
{
	document.location = MODULE_LOGIN + '/signout';
}

function onClickNavBarRegister()
{
	document.location = MODULE_LOGIN + '/register';
}

function onClickNavBarStatic( action_name, newWindow)
{
	if (newWindow)
		window.open(MODULE_STATIC + '/'+ action_name);
	else
		document.location = MODULE_STATIC + '/'+ action_name ;
}

function onClickForum()
{
	document.location = MODULE_FORUM ;
}

function onClickHelp()
{
	document.location = MODULE_FORUM + '/viewForum?id=1';
}

function onClickNavBarMyKaltura()
{
	location = MODULE_MYKALTURA;
}

function onClickNavBarBrowse()
{
	document.location = MODULE_HOME;
}

function onClickNavBarTour()
{
	document.location = MODULE_TOUR + "#why";
}

function onClickNavBarCreate()
{
	location = MODULE_BROWSE + '?kshow_id=1';
}


function onClickSandbox()
{
	location = MODULE_EDIT + '?kshow_id=2';
}


function onClickUserScreenName( a_screenname )
{
	document.location = MODULE_MYKALTURA + "/viewprofile?screenname=" + jQuery(a_screenname).text();
	return false;
}

//------------------------------
// GENERAL STUFF
//------------------------------


function $(element) {
	return (typeof element == 'string') ? document.getElementById(element) : element;
}

Array.from = function(iterable) {
	if (!iterable) return [];
	if (iterable.toArray) {
		return iterable.toArray();
	} else {
		var results = [];
		for (var i = 0, length = iterable.length; i < length; i++)
			results.push(iterable[i]);
		return results;
	}
}

Function.prototype.bind = function() {
  var __method = this, args = Array.from(arguments), object = args.shift();
  return function() {
    return __method.apply(object, args.concat(Array.from(arguments)));
  }
}

var Class = {
  create: function() {
    return function() {
      this.initialize.apply(this, arguments);
    }
  }
}

Object.extend = function(destination, source) {
  for (var property in source) {
    destination[property] = source[property];
  }
  return destination;
}

if (!Array.prototype.map)
{
  Array.prototype.map = function(fun /*, thisp*/)
  {
    var len = this.length;
    if (typeof fun != "function")
      throw new TypeError();

    var res = new Array(len);
    var thisp = arguments[1];
    for (var i = 0; i < len; i++)
    {
      if (i in this)
        res[i] = fun.call(thisp, this[i], i, this);
    }

    return res;
  };
}
	
String.prototype.ScriptFragment = '(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)';

String.prototype.extractScripts = function() {
    var matchAll = new RegExp(String.prototype.ScriptFragment, 'img');
    var matchOne = new RegExp(String.prototype.ScriptFragment, 'im');
    return (this.match(matchAll) || []).map(function(scriptTag) { 
      return (scriptTag.match(matchOne) || ['', ''])[1];
    });
  }

String.prototype.evalScripts = function() {
    return this.extractScripts().map(function(script) { return eval(script) });
  }


Array.prototype.remove=function(s){
  for(i=0;i<this .length;i++){
    if(s==this[i]) this.splice(i, 1);
  }
}
Array.prototype.findIndex = function(value, defValue){
	for (var i=0; i < this.length; i++) {
		// use === to check for Matches. ie., identical (===), ;
		if (this[i] == value)
			return i;
	}
	
	return (defValue === undefined) ? -1 : defValue;
};



//
// http://beppu.lbox.org/articles/2006/09/06/actsasaspect
// enables wrapping of object function with pre, post and around (pre->original->post) functions
//
function actsAsAspect(object) {
  object.yield = null;
  object.rv    = { };
  object.before  = function(method, f) {
    var original = eval("this." + method);
    this[method] = function() {
      f.apply(this, arguments);
      return original.apply(this, arguments);
    };
  };
  object.after   = function(method, f) {
    var original = eval("this." + method);
    this[method] = function() {
      this.rv[method] = original.apply(this, arguments);
      return f.apply(this, arguments);
    }
  };
  object.around  = function(method, f) {
    var original = eval("this." + method);
    this[method] = function() {
      this.yield = original;
      return f.apply(this, arguments);
    }
  };
}

var browserIE = navigator.appVersion.match(/\bMSIE\b/);

//
// http://www.webreference.com/dhtml/diner/realpos4/9.html
//
function DL_GetElementLeft(eElement)
{
   if (!eElement && this)                    // if argument is invalid
   {                                         // (not specified, is null or is 0)
      eElement = this;                       // and function is a method
   }                                         // identify the element as the method owner

   var DL_bIE = document.all ? true : false; // initialize var to identify IE

   var nLeftPos = eElement.offsetLeft;       // initialize var to store calculations
   var eParElement = eElement.offsetParent;  // identify first offset parent element

   while (eParElement != null)
   {                                         // move up through element hierarchy

      if(DL_bIE)                             // if browser is IE, then...
      {
         if( (eParElement.tagName != "TABLE") && (eParElement.tagName != "BODY") )
         {                                   // if parent is not a table or the body, then...
            nLeftPos += eParElement.clientLeft; // append cell border width to calcs
         }
      }
      else                                   // if browser is Gecko, then...
      {
         if(eParElement.tagName == "TABLE")  // if parent is a table, then...
         {                                   // get its border as a number
            var nParBorder = parseInt(eParElement.border);
            if(isNaN(nParBorder))            // if no valid border attribute, then...
            {                                // check the table's frame attribute
               var nParFrame = eParElement.getAttribute('frame');
               if(nParFrame != null)         // if frame has ANY value, then...
               {
                  nLeftPos += 1;             // append one pixel to counter
               }
            }
            else if(nParBorder > 0)          // if a border width is specified, then...
            {
               nLeftPos += nParBorder;       // append the border width to counter
            }
         }
      }
      nLeftPos += eParElement.offsetLeft;    // append left offset of parent
      eParElement = eParElement.offsetParent; // and move up the element hierarchy
   }                                         // until no more offset parents exist
   return nLeftPos;                          // return the number calculated
}

function DL_GetElementTop(eElement)
{
   if (!eElement && this)                    // if argument is invalid
   {                                         // (not specified, is null or is 0)
      eElement = this;                       // and function is a method
   }                                         // identify the element as the method owner

   var DL_bIE = document.all ? true : false; // initialize var to identify IE

   var nTopPos = eElement.offsetTop;         // initialize var to store calculations
   var eParElement = eElement.offsetParent;  // identify first offset parent element

   while (eParElement != null)
   {                                         // move up through element hierarchy
      if(DL_bIE)                             // if browser is IE, then...
      {
         if( (eParElement.tagName != "TABLE") && (eParElement.tagName != "BODY") )
         {                                   // if parent a table cell, then...
            nTopPos += eParElement.clientTop; // append cell border width to calcs
         }
      }
      else                                   // if browser is Gecko, then...
      {
         if(eParElement.tagName == "TABLE")  // if parent is a table, then...
         {                                   // get its border as a number
            var nParBorder = parseInt(eParElement.border);
            if(isNaN(nParBorder))            // if no valid border attribute, then...
            {                                // check the table's frame attribute
               var nParFrame = eParElement.getAttribute('frame');
               if(nParFrame != null)         // if frame has ANY value, then...
               {
                  nTopPos += 1;              // append one pixel to counter
               }
            }
            else if(nParBorder > 0)          // if a border width is specified, then...
            {
               nTopPos += nParBorder;        // append the border width to counter
            }
         }
      }

      nTopPos += eParElement.offsetTop;      // append top offset of parent
      eParElement = eParElement.offsetParent; // and move up the element hierarchy
   }                                         // until no more offset parents exist
   return nTopPos;                           // return the number calculated
}

/** Helper function to handle the json response
The result is a map of name-values of 3 possible formats:
1. starting with '_' - variables that should be evaluated with thier values:
	eval(name1=value1) 
	where name1 is hte name of the variable after removing the '_' prefix
2. starting with '.' - variables that should be evaluated in the given context: 
	context[name] = eval(value);
3. the rest - assumed to be names of elements that should be set with the new value 
	if the element is of type 'IMG' - the src will be set.
	for all the rest - innerHtml will be used
*/
function updateJSON(request, json, context)
{
	if (json == null)
		json = eval('(' + request.responseText + ')')
		
	var obj = eval(json);
	for(var s in obj)
	{
		var data = obj[s];
//		alert ( "updateJSON [" + s + "]=[" + data + "]" );

		if (s.substr(0, 1) == '_') // variables will start with an underscore
		{
			var value = eval(data);
			eval(s.substr(1) + ' = value');
			//eval(s.substr(1) + '=' + data);
		}
		else
		{
//			if ( typeof js_debug != "undefined" ) js_debug.log ( s + "=" + data );
			var pos = s.indexOf('.');
			if (pos != -1)
			{
				var parts = s.split('.');
				
				if (pos == 0)
					context[s.substr(1)] = eval(data);
				else
				{
					is_object = ( typeof data == "object" );
					if ( is_object )
					{
						eval_str = '$("' + parts[0] + '").' + parts[1] + ' = data;';
						eval(eval_str);
					}
					else
					{
						elem = $(parts[0]);
						elem.setAttribute ( parts[1] , data );
					}
				}
			}
			else
			{
				var element = $(s);
				if ( element != null )
				{
					if (element.tagName == "IMG") element.src = data;
					else if ( data != null ) jQuery(element).html(data);
				}
				else
				{
					alert ( "error! element: '"+s+"' doesn't exist" );
				}
			}
		}
	}
}

var ObjectPager = Class.create();

ObjectPager.prototype = {

	currentPage : 1,
	maxPage : 0,
	objectsInPage : 0,
	totalObjects : 0,
	pageSize : 0,
	defaultObjectId : 0,
	btnFirst : null,
	btnPrev : null,
	btnNext : null,
	btnLast : null,
	elementCurrentPage : null,
	elementMaxPage : null,
	requestObjectsFunc : null,

	initialize: function(btnPrefix, pageSize, requestObjectsFunc) {
		this.btnFirst = $(btnPrefix + 'First');
		this.btnPrev = $(btnPrefix + 'Prev');
		this.btnNext = $(btnPrefix + 'Next');
		this.btnLast = $(btnPrefix + 'Last');
		this.elementCurrentPage = $(btnPrefix + 'CurrentPage');
		this.elementMaxPage = $(btnPrefix + 'MaxPage');
		this.pageSize = pageSize;
		this.requestObjectsFunc = requestObjectsFunc;

	},
	
	reset: function()
	{
		this.currentPage = 1;
		this.objectsInPage = 0;
		this.maxPage = 1;
		this.updateButtons();
	},
	
	requestObjects: function(page, defaultObject)
	{
		if ( this.requestObjectsFunc != null )	this.requestObjectsFunc(this, page, defaultObject);
	},
	
	updateJSON: function(request, json, updateFunc)
	{
		if (updateFunc == undefined)
			updateJSON(request, json, this);
		else
			updateFunc(request, json, this);
			
		this.updateButtons();
	},
	
	setDisabled: function(element, disabled)
	{
		element.disabled = disabled;
		if (element.ondisabled)
			element.ondisabled();
	},
	
	updateButtons: function()
	{
		return;
		this.setDisabled(this.btnFirst, this.currentPage == 1);
		this.setDisabled(this.btnPrev, this.currentPage == 1);
		this.setDisabled(this.btnNext, this.currentPage >= this.maxPage);
		this.setDisabled(this.btnLast, this.currentPage >= this.maxPage);
		if (this.elementCurrentPage) this.elementCurrentPage.innerHTML = this.currentPage;
		if (this.elementMaxPage) this.elementMaxPage.innerHTML = this.maxPage;
	},

	onClickFirst: function() {
		if (this.btnFirst.disabled)
			return;
			
		this.requestObjects(1);
	},
	
	onClickPrev: function()	{
		if (this.btnPrev.disabled)
			return;
				
		this.requestObjects(this.currentPage - 1);
	},
	
	onClickNext: function()	{
		if (this.btnNext.disabled)
			return;
			
		this.requestObjects(this.currentPage + 1);
	},
	
	onClickLast: function()	{
		if (this.btnLast.disabled)
			return;
			
		this.requestObjects(this.maxPage);
	}
};


// generic pager - 
// TODO - encapsulate in js class

/*
 *  assume there in an element - most probably a UL that has the id of pager_name
 */
updatePagerAndRebind = function ( pager_id , pager_html , callback_func  )
{
	//alert ( pager_id  + ":\n" + pager_html );
	pager_elem = jQuery("#" + pager_id );

	if ( pager_html != null )
		pager_elem.html(pager_html);
	
	pager_elem.children("li")
		.unbind()
		.not(".disabled")
		.not(".active")
		.click(function(){ callback_func ( this, this.value )} );
	//pager_elem.children("li.disabled").unbind(); // don't navigate disabled
	//pager_elem.children("li.active").unbind(); // don't navigate active
}


var simpleMenu = Class.create();

simpleMenu.prototype = {

	initialize: function(trigger, menu) {

		this.trigger = trigger;
		this.menu = menu;
		
		this.closeTimeout = 0;
		this.visible = false;
	
		Event.observe(this.trigger, 'mouseover', this.onMouseOver.bindAsEventListener(this), false);
		Event.observe(this.trigger, 'mouseout', this.onMouseOut.bindAsEventListener(this), false);
		Event.observe(this.menu, 'mouseover', this.onMouseOver.bindAsEventListener(this), false);
		Event.observe(this.menu, 'mouseout', this.onMouseOut.bindAsEventListener(this), false);
		Event.observe(this.menu, 'click', this.onClick.bindAsEventListener(this), false);
	},

	closeMenu: function() {
		this.visible = false;
		this.menu.style.display = 'none';
	},

	onMouseOver: function(e) {
		if (!this.visible)
		{
			this.menu.style.top = (this.trigger.clientHeight + DL_GetElementTop(this.trigger)) + 'px';
			this.menu.style.left = DL_GetElementLeft(this.trigger) + 'px';
			this.menu.style.display = 'block';
			this.visible = true;
		}
	
		if (this.closeTimeout)
		{
			clearTimeout(this.closeTimeout);
			this.closeTimeout = 0;
		}
	},

	onMouseOut: function(e) {
		if (!this.closeTimeout)
		{
			this.closeTimeout = setTimeout(this.closeMenu.bind(this), 100);
		}
	},
	
	onClick: function(e) {
		this.closeMenu();
	}
}


function WizardDialog(url, args) { this.init(url, args); }

var activeWizard = null;
var autoCloseWizard = null;

WizardDialog.prototype = {
	
	firstTime : true,
	
	waitForDOM : function(e,callback,firstTime)
	{
		activeWizard.waitForScript = $(e) == null;
			
		if (activeWizard.waitForScript)
		{
			setTimeout(WizardDialog.prototype.waitForDOM.bind(this, e, callback, false), 100);
		}
		else
		{
			callback();
		}
	},

	init : function(url, args)
	{
		if (activeWizard)
			return;
			
		activeWizard = this;
		this.visible = false;
		this.waitForScript = true;
		this.wizardPages = new Object();
		this.wizardPages.length = 0;
		
		this.currentPage = null;
		this.onCloseData = null;
		
		args = args || [];
		this.args = args;
		this.data = new Object();
		
//		if (flashMovie)
//			flashMovie.StopAllMedia();
			
		if (WizardDialog.prototype.firstTime)
		{
			WizardDialog.prototype.firstTime = false;
			var image = new Image();
			image.src = "http://www.kaltura.com/images/wizard/wiz_bg_middle.png";		
		}
		
		var dialog;
		dialog = this.dialog = jQuery(
			'<div class="jqmWindow wizard">' +
				'<button class="close"><\/button>' +
				'<div class="content">' +
					'<div class="top"><h1><\/h1><div class="preloader"><\/div><\/div>' +
					'<div class="middle"><div class="bg"><\/div><\/div>' +
					'<div class="bottom">' +
						'<button class="btn1 next"><div>Next</div><\/button>' +
						'<button class="left cancel"><div>Cancel</div><\/button>' +
						'<button class="left back"><div>Back</div><\/button>' +
					'<\/div>' +
				'<\/div>' +
				'<div class="master_bg"><div class="top"><\/div><div class="middle"><\/div><div class="bottom"><\/div><\/div>' +
			'<\/div>');
			
		dialog.css("display", "none");
			
		dialog.appendTo('body');

		this.dialogTop = dialog.find("div.content .top h1");
		this.dialogContent = dialog.find("div.content .middle");
		this.button1 = dialog.find("div.bottom .cancel").click(this.onClickButton1.bind(this));
		this.button2 = dialog.find("div.bottom .back").click(this.onClickButton2.bind(this));
		this.button3 = dialog.find("div.bottom .next").click(this.onClickButton3.bind(this));
		dialog.find(".close").click(this.onClickButton1.bind(this));
		
		this.button2.css("visibility", "hidden");
		this.button3.css("visibility", "hidden");

		var hide = function(hash) { hash.w.fadeOut('2000',function(){ hash.o.remove(); }); };
		
		dialog.jqm({modal:true, onHide: hide});
		
		this.loadPage(args.firstPage || null, url);
	},
	
	triggerPreLoader : function(show)
	{
		this.dialog.find(".content .top .preloader").css('visibility', show ? 'visible' : 'hidden');
	},
	
	enableButton : function(i, enable)
	{
		var button = i == 1 ? this.button1 : i == 2 ? this.button2 : this.button3;
		
		if ((button.attr("disabled") != "disabled") != enable)
		{
			if (enable)
				button.removeClass("disabled").removeAttr("disabled");
			else
				button.addClass("disabled").attr("disabled", "disabled");
		}
	},
	
	setButton : function(i, title)
	{
		var button = i == 1 ? this.button1 : i == 2 ? this.button2 : this.button3;
		if (title)
		{
			if (title != true)
				button.find("div").text(title);
			button.css("visibility", "visible");
		}
		else
			button.css("visibility", "hidden");
	},

	show : function()
	{
		this.visible = true;
		
		if (this.args.hideElement)
		{
			var e = this.args.hideElement;
			//var leftPos = DL_GetElementLeft(e[0]) + 'px';
			var topPos = DL_GetElementTop(e[0]) + 'px';
			//this.args.hideElement.css('visibility', 'hidden');
			//this.dialog.css({position: 'absolute', top : topPos});
		}
		
		showKplayer(false);
		this.dialog.jqm().jqmShow(); 
		//this.dialog.css('z-index', parseInt(this.dialog.css('z-index')) + 1);
		//this.dialog.css("display", "block");
//		this.dialog.fadeIn("normal");
	},
	
	gotoPrevPage : function()
	{
		this.changeCurrentPage(this.currentPage.prevPage, true);
	},
		
	onClickButton1 : function()
	{
		var pageObj = this.getPageObj(this.currentPage);

		if (pageObj.onClickButton1)
		{
			var pageId = this.currentPage.id;
			var page = jQuery(this.currentPage);

			pageObj.onClickButton1(this, pageId, page);
		}
		else
			this.close();
	},
	
	onClickButton2 : function()
	{
		var pageObj = this.getPageObj(this.currentPage);

		if (pageObj.onClickButton2)
		{
			var pageId = this.currentPage.id;
			var page = jQuery(this.currentPage);

			pageObj.onClickButton2(this, pageId, page);
		}
		else
			this.gotoPrevPage();
	},
	
	onClickButton3 : function()
	{
		var pageObj = this.getPageObj(this.currentPage);

		if (pageObj.onClickButton3)
		{
			var pageId = this.currentPage.id;
			var page = jQuery(this.currentPage);

			pageObj.onClickButton3(this, pageId, page);
		}
	},
	
	gotoPage : function(pageName, url, backButton)
	{
		if (pageName == null)
		{
			this.loadPage(pageName, url);
			return;
		}
		
		if (pageName.substr(0, 1) != '_')
			pageName = '_' + pageName;

		var page = this.wizardPages[pageName];
		if (page == undefined)
		{
			this.loadPage(pageName, url);
		}
		else if (page.pageObj && page.pageObj.reload)
		{
			jQuery(page).remove();
			this.wizardPages[pageName] = undefined;
			this.wizardPages.length--;
			this.loadPage(pageName, url);
		}
		else
		{
			this.changeCurrentPage(page, backButton);
		}
	},

	getPageObj : function(page)
	{
		return page.pageObj || this.args.wizardLogic;
	},
	
	changeCurrentPage : function(page, backButton)
	{
		var pageObj = this.getPageObj(page);
		
		if (this.currentPage)
		{
			this.currentPage.style.display = 'none';

			var curPageObj = this.currentPage.pageObj || this.args.wizardLogic;
			
			if (curPageObj.onLeavePage)
				curPageObj.onLeavePage(this, this.currentPage.id, jQuery(this.currentPage));

			if (!backButton)
				page.prevPage = this.currentPage;
		}
		
		this.currentPage = page;

		var jPage = jQuery(page);

		this.dialogTop.html(jQuery(' .top2_hint', page).html());

		this.button2.css('visibility', jPage.attr('noBackButton') ? "hidden" : "visible");
		this.button3.css('visibility', jPage.attr('noNextButton') ? "hidden" : "visible");

		if (pageObj.onEnterPage)
			pageObj.onEnterPage(this, page.id, jPage);

		page.style.display = 'block';

		if (pageObj.onPostEnterPage)
			pageObj.onPostEnterPage(this, page.id, jPage);

		if (pageObj.focusedElement)
			pageObj.focusedElement.focus();
		else
			if (jPage.attr('autoFocus'))
				setTimeout(function() { jPage.find(":input:first").focus(); }, 0); // IE hack - needs timeout
	},
	
	// Begin Ajax request based off of the href of the clicked linked
	loadPage: function(pageName, url) {
		autoCloseWizard = false;
		jQuery.post(url, null, this.processPage.bind(this, pageName));
	},

	// Display Ajax response
	processPage: function(pageName, data){
		var pageDiv = document.createElement('div');
		
		pageDiv.style.display = 'none';
		pageDiv.innerHTML = data;
		//jQuery(pageDiv).html(data);

		this.dialogContent.append(pageDiv);

		if (browserIE)
		{
			try {
				data.evalScripts();
			}
			catch(e) {
				alert(e.message);
			}
		}

		if (autoCloseWizard)
		{
			setTimeout(this.close().bind(this), 100)
			return;
		}			
		
		this.processPageSync(pageName, pageDiv);
	},
	
	processPageSync: function(pageName, pageDiv){
		if (activeWizard.waitForScript)
			setTimeout(this.processPageSync.bind(this, pageName, pageDiv), 100);
		else
			this.postProcessPage(pageName, pageDiv);
	},
	
	postProcessPage: function(pageName, pageDiv){
		var firstPage = this.wizardPages.length == 0;
		
		firstPageName = this.createWizardPages ( null , pageDiv , 0 );

		pageDiv.style.display = 'block';

		// special case in which we have to display the dialog for the first time
		if (firstPage)
			this.show();
			
		if (pageName == null || this.wizardPages['_' + pageName] == undefined)
			pageName = firstPageName;
			
		setTimeout(function () {this.gotoPage(pageName)}.bind(this), 100); // page is loaded we can goto again
	},
	
	createWizardPages : function ( pageName , htmlElem , recDepth )
	{	
		if ( recDepth > 1 ) return pageName;
		
		for (var i = 0, length = htmlElem.childNodes.length; i < length; i++)
		{
			var node = htmlElem.childNodes[i];

			if (node.tagName == "FORM")
			{
				pageName = this.createWizardPages ( pageName , node , recDepth+1);
			}
			else if (node.tagName == "DIV")
			{
				var div_class = node.className;
				if (div_class != null && div_class.indexOf ( "NOWIZARD" ) >= 0 )
					continue;
					
				if (pageName == null)
					pageName = node.id;
					
				this.wizardPages[node.id] = node;
				this.wizardPages.length++;
				node.style.display = 'none';

				if (node.pageObj && node.pageObj.setup)
					node.pageObj.setup(this, node.id, jQuery(node));
			}
		}
		return pageName;
	} ,
	
	
	onClickClose : function(evt)
	{
		this.close();
	},
	
	close : function()
	{
		if (this.currentPage)
		{
			var pageObj = this.getPageObj(this.currentPage);
			if (pageObj.onCanClose)
			if (!pageObj.onCanClose(this))
				return;
				
			if (pageObj.onLeavePage)
				pageObj.onLeavePage(this);
		}
				
		this.dialog.jqm().jqmHide(); 
		
		this.dialog.remove();
		
		if (this.args.hideElement)
			this.args.hideElement.css('visibility', 'visible');
			
		if (this.visible)
			showKplayer(true);

		activeWizard = null;
		
		if (this.args.onClose)
			this.args.onClose(this.onCloseData);
	}
};


var ImagesLoader = Class.create();

ImagesLoader.prototype = {

	initialize : function(urls, args)
	{
		this.stopLoading = false;
		this.urls = urls;

		this.args = args || [];
		this.maxConcurrent = args.maxConcurrent || 4;
		this.images = new Array();
	},

	onLoadImage : function(image)
	{
		this.imagesLoading--;
		this.args.onLoadImage(image, this.imagesLoaded);
		this.imagesLoaded++;

		// we use setTimeout to prevent recursion in case the current image was cached
		setTimeout(this.loadImage.bind(this, image), 0);
	},
	
	onErrorImage : function(image)
	{
		this.imagesLoading--;

		// we use setTimeout to prevent recursion in case the current image was cached
		setTimeout(this.loadImage.bind(this, image), 0);
	},
	
	loadImage : function(image)
	{
		if(this.stopLoading)
			return false;

		if (this.imagesLoading == this.maxConcurrent) // dont load more than this.maxConcurrent images in parallel
			return false;

		if (this.imagesCount == this.urls.length) // stop when we reached our last image
			return false;

		if (image == undefined || image == null) // reuse given image (in case of onLoad and onError)
		{
			image = new Image();
			image.onload = this.onLoadImage.bind(this, image);
			image.onerror = this.onErrorImage.bind(this, image);
			this.images.push(image)
		}

		var urlPair = this.urls[this.imagesCount];
		
		this.imagesCount++;
		this.imagesLoading++;

		image.src = urlPair.url;
		image.data = urlPair.data;

		return true;
	},

	start : function()
	{
		this.imagesCount = 0;
		this.imagesLoading = 0;
		this.imagesLoaded = 0;

		while(this.loadImage(null));
	},

	stop : function()
	{
		this.stopLoading = true;
		this.images.each(function(image) { image.src = '/images/main/empty.gif'; });
	}
};

// TODO - fix the binding of the methods also when there are previously defined  !!
var MyAjaxRequest = Class.create();

MyAjaxRequest.prototype = {
		
	indicator : null ,
	
	initialize : function ( url , ajax_container )
	{
//		this.createAjaxIndicator();
//		this.startedLoading();
		
		// the sequence of the events fired in Ajax requests is as follows:
		// onLoading
		// OnComplete
		// onSuccess / OnFailure
/*		
		if ( typeof ajax_container.onLoading == "undefined" )
		{
			ajax_container.onLoading = this.startedLoading.bind(this, null ) ;
		}
		if ( typeof ajax_container.onSuccess == "undefined" )
		{
			ajax_container.onSuccess = this.endedLoading.bind(this, null ) ;
		}
		if ( typeof ajax_container.OnFailure == "undefined" )
		{
			ajax_container.OnFailure = this.endedLoading.bind(this, null ) ;
		}
*/
		// if we want the indicator to follow the mouse cursor
//		Event.observe(window, 'mousemove', this._duringMove.bindAsEventListener(this), false);					

		jQuery.ajax({
			url: url,
			async: true , 
			complete: this.onSuccess.bind(this, ajax_container)
		} );
		//Event.observe(window, 'mousemove', null , false);					
	} , 

	onSuccess : function(ajax_container, xhr, result)
	{	
//		this.endedLoading();
		if (result == 'success')
		{
			var json = null;
			
			try {
				json = xhr.getResponseHeader('X-JSON');
				json =  json ? eval('(' + json + ')') : null;
			} catch (e) { }
			
			ajax_container.onComplete(xhr, json);
		}
	},
	
	createAjaxIndicator :function ( )
	{
		if ( MyAjaxRequest.prototype.indicator != null ) return;
		MyAjaxRequest.prototype.indicator = $("_ajax_indicator");
  		if ( MyAjaxRequest.prototype.indicator != null ) return;
		
		var bod = document.getElementsByTagName('body')[0];
		
		str = '<img src="/images/indicator.gif" >' ;

		var div = document.createElement('div');
		div.id = '_ajax_indicator';
		div.style.position = 'absolute';
		div.style.zIndex = '32123';
		div.style.display = 'none';
		div.innerHTML = str;
		
		bod.appendChild(div);
		
		MyAjaxRequest.prototype.indicator = div;
	} ,
	
	startedLoading : function ()
	{
		document.body.style.cursor = 'wait'
		MyAjaxRequest.prototype.indicator.style.display = 'block';
	},
	
	endedLoading : function ()
	{
		document.body.style.cursor = 'default'
		MyAjaxRequest.prototype.indicator.style.display = 'none';
	} ,
	
	_duringMove : function ( event )
	{
		var posx = event.clientX - 20 ;
		var posy = event.clientY - 20 ;
		
		MyAjaxRequest.prototype.indicator.style["top"] = posy + "px"
		MyAjaxRequest.prototype.indicator.style["left"] = posx + "px"
	}
}

/*
// the following class registers ajax request grouped by names. when a request completes it's removed from the list.
// the Ajax.removeCarouselRequests aborts a given group pending requests
Ajax.carouselRequests = {};

Ajax.Responders.register({
	onCreate: function(request) {
		if (request.options.carouselName) {
			var a = Ajax.carouselRequests[request.options.carouselName];
			if (a == null)
				Ajax.carouselRequests[request.options.carouselName] = a = new Array();

			a.push(request);
		}
	},
	onComplete: function(request) {
		if (request.options.carouselName) {
			var a = Ajax.carouselRequests[request.options.carouselName];
			if (a != null)
				a.remove(request);
		}
	}
});

// the Ajax.removeCarouselRequests aborts a given group pending requests
Ajax.removeCarouselRequests = function(carouselName)
{
	var a = Ajax.carouselRequests[carouselName];
	if (!a)
		return;

	for(var i = 0; i < a.length; i++)
		try { a[i].transport.abort(); } catch(e) {}

	Ajax.carouselRequests[carouselName] = null;
}
*/

function CarouselWrapper(e, o) {

	this.options = o;
	this.element = e.get(0);
	o.loadItemHandler = this.loadItemHandler.bind(this);
	e.jcarousel(o);
	this.carousel = this.element.carousel;
	this.carouselName = o.carouselName;
	this.ajaxRequests = new Array();
	this.page = -1;
	this.pageSize = o.itemVisible * (o.pagesPerRequest ? o.pagesPerRequest : 2);
	this.pendingLast = 0;
	this.completedLast = 0;
}

CarouselWrapper.prototype = {
	clear : function()
	{
		//Ajax.removeCarouselRequests(this.carouselName);
		this.page = -1;
		this.pendingLast = 0;
		this.completedLast = 0;
		this.carousel.clear();
	},
	
	setPagesPerRequest : function(pagesPerRequest)
	{
		this.options.pagesPerRequest = pagesPerRequest;
		this.pageSize = this.options.itemVisible * (pagesPerRequest ? pagesPerRequest : 2);
	},
	
	reset : function() {
		this.clear();
		this.carousel.reset();
	},

	getObject : function(i)
	{
		return this.carousel.get(i)[0].objectInfo;
	},
	
	add : function(i, html) {
		return this.carousel.add(i, html);
	},

	loaded : function()
	{
		return this.carousel.loaded();
	},

	loadItemHandler : function(carousel, first, last, available)
	{
		if (!available)
		{
			// skip AJAX if a previous request will return the requests range
			if (last > this.pendingLast)
			{
				var page = Math.floor(first / this.pageSize) + 1;
				var pendingLast = page * this.pageSize;

				// end of data reached if the request is for less than a full page
				if (pendingLast - this.completedLast == this.pageSize)
				{
					this.page = page;
					this.pendingLast = pendingLast;
					var obj = this.options.ajaxUrl(this);
					if (typeof obj == 'string')
					{
						jQuery.getJSON(obj, null, this.onLoadItemComplete.bind(this, first, last));
					}
					else
					{
						setTimeout(this.onLoadItemComplete.bind(this, first, last, obj), 100);
					}
					
					return;
				}
			}
		}
		
		this.carousel.loaded();
	},

	onLoadItemComplete : function(first, last, json)
	{
		this.completedLast = first + json.objects.length - 1;

		this.options.ajaxComplete(json, this.carousel, first, Math.max(last, this.pendingLast));
	}
};

function updateSIFRElement(cont, t, fontPath, sColor)
{
	jQuery(cont).html('<h1 class="sIFR">' + t + '<\/h1>');
	if(typeof sIFR == "function"){
		sIFR.replaceElement(cont + " h1.sIFR", named({sFlashSrc: '/fonts/' + fontPath, sColor: (sColor || "#ffffff"), sWmode: 'transparent', sFlashVars: 'textalign=center' }));
	};
}

function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		/*while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}*/
	}
	return [curleft,curtop];
}

jQuery.fn.resetField = function(t, keepOnFocus) {
	this.each(function() {
		var textarea = jQuery.nodeName(this, "textarea");
		var o = jQuery(this);
		if (textarea && o.text() == '' || (!textarea && o.val() == ''))
		{
			var defValue = t || jQuery(this).attr("defValue");
			textarea ? o.text(defValue) : o.val(defValue);
		}
	});

	this.focus(function(){
		var defValue = t || jQuery(this).attr("defValue");
		if (!keepOnFocus && jQuery(this).val() == defValue) jQuery(this).val("");
	}).blur(function(){
		var defValue = t || jQuery(this).attr("defValue");
		if (jQuery(this).val() == "") jQuery(this).val(defValue);
	});
		
	return this;
}



function resetAllFields ( selector )
{
	jQuery ( selector ).find("[@defValue]").resetField();
}

jQuery.fn.emptyDefValue = function(t) {
	this.each(function() {
		jQuery (this).val ( ignoreDefValue ( this ));
	});
}


function emptyAllDefValues ( selector )
{
	jQuery ( selector ).find("[@defValue]").emptyDefValue();
}


function ignoreDefValue(selector)
{
	var e = jQuery(selector);
	var val = e.val();
	return e.attr('defValue') == val ? "" : val;
}


function updateStatusBar( message, seconds_to_display )
{
	if ( seconds_to_display == 0 || seconds_to_display == 'undefined' ) seconds_to_display = 5;
	
	jQuery('#statusBar').show();
	$('statusBar').firstChild.innerHTML = message;
	setTimeout( "jQuery('#statusBar').hide();" , seconds_to_display * 1000 );
}

function addTimeToUrl()
{
	var t = new Date();
	return "?t=" + t.getTime();
}

/*
 * @param str - string to search
 * @param list - an extra parameter to match str. 
 * any other parameter send will be considered a value in the list 
 */
function inArray ( str , list /* ... */)
{
	list = inArray.arguments;
	var len = list.length;
	// strat the serach with arg
	for ( var x = 1 ; x <= len ; x++ ) {
		if ( list[x] == str ) return true;
	}
	return false;
}

uploadSwf = {

	modal : false,
	
	browse : function(args)
	{
		if (this.modal)
			return;

		this.modal = true;			
		
		this.cancelUpload = false;
	
		this.fileUploadStatus = args.fileUploadStatus || null;
		this.fileUploadStart = args.onStart || null;
		this.fileUploadProgress = args.onProgress || null;
		this.flleUploadComplete = args.onComplete || null;
		this.fileUploadCancel = args.onCancel || null;
		
		var flashGallery = new FlashTag("/swf/upload.swf", 1, 1);
		flashGallery.setFlashvars(
			"uploadHash=" + escape(args.uploadHash) +
			"&uploadBackend=" + MODULE_UPLOAD +
			"&allowedFiletypes=" + (args.fileTypes || '*.*'));
		jQuery("#SWFUpload").html(flashGallery.toString());
		jQuery("#SWFUpload").css('visibility', 'visible');
	},
	
	cancel : function()
	{
		this.cancelUpload = true;
	},

	uploadCancelCallback : function()
	{
		jQuery("#SWFUpload").css('visibility', 'hidden');
		this.modal = false;
		if (this.fileUploadCancel)
			this.fileUploadCancel();
	},
	
	uploadStartCallback : function(fileObj)
	{
		//jQuery("#SWFUpload").css('visibility', 'hidden');
		this.modal = false;
		
		if (this.fileUploadStart)
		{
			this.fileUploadStart();
			return;
		}
			
		if (this.fileUploadStatus)
			$(this.fileUploadStatus).innerHTML = fileObj.name + ' : 0/' + Math.floor(fileObj.size / 1024) + 'Kb (0%)';
	},
	
	uploadErrorCallback : function(errcode, file, msg)
	{
		switch(errcode) {
			
			case -10:	// HTTP error
				alert(errcode + ", " + file + ", " + msg);
				break;
			
			case -20:	// No backend file specified
				alert(errcode + ", " + file + ", " + msg);
				break;
			
			case -30:	// IOError
				alert(errcode + ", " + file + ", " + msg);
				break;
			
			case -40:	// Security error
				alert(errcode + ", " + file + ", " + msg);
				break;
	
			case -50:	// Filesize too big
				alert(errcode + ", " + file + ", " + msg);
				break;
		
		}
	},
	
	uploadProgressCallback : function(fileObj, bytesLoaded)
	{
		if (this.cancelUpload)
			return 1;
			
		if (this.fileUploadProgress)
		{
			var percent = fileObj.size ? Math.floor((bytesLoaded / fileObj.size) * 100) : 0;
			return this.fileUploadProgress(
				Math.floor(bytesLoaded / 1024) + '/' +
				Math.floor(fileObj.size / 1024) + 'Kb (' +
				percent + '%)', percent);
		}
		
		if (fileObj.element)
		{
			var e = $(fileObj.element);
			if (e && e.getAttribute('cancelUpload') == 1)
				return 1;
		}
		
		if (this.fileUploadStatus)
		{
			var percent = Math.floor((bytesLoaded / fileObj.size) * 100)
			$(this.fileUploadStatus).innerHTML = fileObj.name + ' : ' + 
				Math.floor(bytesLoaded / 1024) + '/' +
				Math.floor(fileObj.size / 1024) + 'Kb (' +
				percent + '%)';
		}
			
		return 0;
	},
	
	uploadCompleteCallback : function(fileObj)
	{
		if (this.cancelUpload)
			return 1;
			
		if (this.flleUploadComplete)
		{
			this.flleUploadComplete(fileObj.name.toLowerCase());
			return;
		}
		
		if (fileObj.element)
		{
			var e = $(fileObj.element);
			if (e)
			{
				e.value = fileObj.name.toLowerCase();
				s = e.getAttribute('onComplete');
				if (s)
				{
					eval(s);
				}
			}
		}
		
		if (this.fileUploadStatus)
		{
			$(this.fileUploadStatus).innerHTML = fileObj.name + ' : ' + 
				Math.floor(fileObj.size / 1024) + 'Kb (100%)';
		}
	}
}

function getFlashMovieObject(movieName)
{
  if (window.document[movieName]) 
  {
    return window.document[movieName];
  }
  if (navigator.appName.indexOf("Microsoft Internet")==-1)
  {
    if (document.embeds && document.embeds[movieName])
      return document.embeds[movieName]; 
  }
  else // if (navigator.appName.indexOf("Microsoft Internet")!=-1)
  {
    return document.getElementById(movieName);
  }
}

var flashMovie = null;
var hideKplayerCount = 0;

function showKplayer(show)
{
	hideKplayerCount += show ? 1 : -1;
	
	if (!show)
	{
		if (flashMovie && flashMovie.PauseMedia != undefined)
		{
			flashMovie.PauseMedia();
		}
	}
	
	if (detectMacXFF)
		jQuery('#kplayer').css('visibility', hideKplayerCount ? 'hidden' : 'visible');  // was #kplayer_cont
}

insertMediaArgs = null;
flashMovieTimeout = null;

function insertMedia( aKshowId, aEntryId, aAutoStart )
{
	if (arguments.length)
	{
		if( aAutoStart == undefined ) arguments[2] = true;
		insertMediaArgs = arguments;
	}
	
	flashMovie=getFlashMovieObject("kplayer");
	if( flashMovie.InsertEntry != undefined && flashMovie.PauseMedia != undefined)
	{
		if (flashMovieTimeout)
		{
			clearInterval(flashMovieTimeout);
			flashMovieTimeout = null;
		}
		
		flashMovie.InsertEntry(insertMediaArgs[0], insertMediaArgs[1], hideKplayerCount == 0 && insertMediaArgs[2]);
	}
	else if (!flashMovieTimeout)
	{
		flashMovie = null;
		flashMovieTimeout = setInterval( function(){insertMedia()}, 500 );
	}
}

function forceGotoShow( kshowid )
{
	document.location.href = MODULE_BROWSE + '?kshow_id=' + kshowid 
}

// cmd is an optional paramter that indicates the command to send to the show
function gotoShow( kshowid  , cmd )
{
	if (typeof(browsePage) != 'undefined')
	{
		parseBrowseCmd(cmd);
		return;
	}
	
	if ( arguments.length > 1 )
		setCookie('browseCmd', cmd);

	document.location.href = MODULE_BROWSE + '?kshow_id=' + kshowid ;
}

function gotoUserProfile( screenname )
{
	document.location = MODULE_MYKALTURA + "/viewprofile?screenname=" + screenname;
}


function searchTag ( tag )
{
	document.location = MODULE_SEARCH + "?mode=ALL&sort=&keywords=" + tag;
}

/**
 * Generates a browser-specific Flash tag. Create a new instance, set whatever
 * properties you need, then call either toString() to get the tag as a string, or
 * call write() to write the tag out.
 */

/**
 * Creates a new instance of the FlashTag.
 * src: The path to the SWF file.
 * width: The width of your Flash content.
 * height: the height of your Flash content.
 */
function FlashTag(src, width, height)
{
    this.src       = src;
    this.width     = width;
    this.height    = height;
    this.version   = '7,0,14,0';
    this.id        = null;
    this.bgcolor   = 'ffffff';
    this.flashVars = null;
}

/**
 * Sets the Flash version used in the Flash tag.
 */
FlashTag.prototype.setVersion = function(v)
{
    this.version = v;
}

/**
 * Sets the ID used in the Flash tag.
 */
FlashTag.prototype.setId = function(id)
{
    this.id = id;
}

/**
 * Sets the background color used in the Flash tag.
 */
FlashTag.prototype.setBgcolor = function(bgc)
{
    this.bgcolor = bgc;
}

/**
 * Sets any variables to be passed into the Flash content. 
 */
FlashTag.prototype.setFlashvars = function(fv)
{
    this.flashVars = fv;
}

/**
 * Sets any variables to be passed into the Flash content. 
 */
FlashTag.prototype.setWMode = function(wmode)
{
    this.wmode = wmode;
}

/**
 * Get the Flash tag as a string. 
 */
FlashTag.prototype.toString = function()
{
    var ie = (navigator.appName.indexOf ("Microsoft") != -1) ? 1 : 0;
    var flashTag = new String();
    if (ie)
    {
        flashTag += '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
        if (this.id != null)
        {
            flashTag += 'id="'+this.id+'" ';
        }
        flashTag += 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab" ';//#version='+this.version+'" ';
        flashTag += 'width="'+this.width+'" ';
        flashTag += 'height="'+this.height+'">';
        flashTag += '<param name="allowScriptAccess" value="sameDomain"/>';
        flashTag += '<param name="allowFullScreen" value="true"/>';
        flashTag += '<param name="wmode" value="' + (this.wmode || 'opaque') +'"/>';
        flashTag += '<param name="movie" value="'+this.src+'"/>';
        flashTag += '<param name="quality" value="high"/>';
        flashTag += '<param name="bgcolor" value="#'+this.bgcolor+'"/>';
        if (this.flashVars != null)
        {
            flashTag += '<param name="flashvars" value="'+this.flashVars+'"/>';
        }
        flashTag += '</object>';
    }
    else
    {
        flashTag += '<embed src="'+this.src+'" ';
        flashTag += 'wmode="' + (this.wmode || 'opaque') +'" ';
        flashTag += 'wmode="opaque" '; 
        flashTag += 'quality="high" '; 
        flashTag += 'bgcolor="#'+this.bgcolor+'" ';
        flashTag += 'width="'+this.width+'" ';
        flashTag += 'height="'+this.height+'" ';
        flashTag += 'type="application/x-shockwave-flash" ';
        flashTag += 'allowScriptAccess="sameDomain" ';
        flashTag += 'allowFullScreen="true" ';
        if (this.flashVars != null)
        {
            flashTag += 'flashvars="'+this.flashVars+'" ';
        }
        if (this.id != null)
        {
            flashTag += 'name="'+this.id+'" ';
        }
        flashTag += 'pluginspage="http://www.macromedia.com/go/getflashplayer">';
        flashTag += '</embed>';
    }
    return flashTag;
}

function getCookieVal (offset) {
  var endstr = document.cookie.indexOf (";", offset);
  if (endstr == -1)
    endstr = document.cookie.length;
  return unescape(document.cookie.substring(offset, endstr));
}

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

function setCookie (name,value,expires,path,domain,secure) {
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

// Fisher-Yates in JavaScript - from http://sedition.com/perl/javascript-fy.html
function suffleArray ( myArray ) {
  var i = myArray.length;
  if ( i == 0 ) return false;
  while ( --i ) {
     var j = Math.floor( Math.random() * ( i + 1 ) );
     var tempi = myArray[i];
     var tempj = myArray[j];
     myArray[i] = tempj;
     myArray[j] = tempi;
   }
}


function createDynamicScript( script_src ) 
{	
	script = document.createElement('script');	
	script.src = script_src;	
	script.type = 'text/javascript';	
	script.defer = false;	
	jQuery ( script ).appendTo ( 'head' );
}


function imposeMaxLength( event , Object, MaxLen)
{
	keycode = "?";
	if (window.event) keycode = window.event.keyCode;
	else if (event) keycode = event.which;
		else return true;

	if ( keycode == 0 || keycode == 8 ) return true; // navigation keys or delete
	return (Object.value.length <= MaxLen);
}