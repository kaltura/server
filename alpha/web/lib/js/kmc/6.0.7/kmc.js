/*! KMC - v6.0.7 - 2013-07-18
* https://github.com/kaltura/KMC_V2
* Copyright (c) 2013 Ran Yefet; Licensed GNU */
/*! Kaltura Embed Code Generator - v1.0.6 - 2013-02-28
* https://github.com/kaltura/EmbedCodeGenerator
* Copyright (c) 2013 Ran Yefet; Licensed MIT */

// lib/handlebars/base.js

/*jshint eqnull:true*/
this.Handlebars = {};

(function(Handlebars) {

Handlebars.VERSION = "1.0.rc.2";

Handlebars.helpers  = {};
Handlebars.partials = {};

Handlebars.registerHelper = function(name, fn, inverse) {
  if(inverse) { fn.not = inverse; }
  this.helpers[name] = fn;
};

Handlebars.registerPartial = function(name, str) {
  this.partials[name] = str;
};

Handlebars.registerHelper('helperMissing', function(arg) {
  if(arguments.length === 2) {
    return undefined;
  } else {
    throw new Error("Could not find property '" + arg + "'");
  }
});

var toString = Object.prototype.toString, functionType = "[object Function]";

Handlebars.registerHelper('blockHelperMissing', function(context, options) {
  var inverse = options.inverse || function() {}, fn = options.fn;


  var ret = "";
  var type = toString.call(context);

  if(type === functionType) { context = context.call(this); }

  if(context === true) {
    return fn(this);
  } else if(context === false || context == null) {
    return inverse(this);
  } else if(type === "[object Array]") {
    if(context.length > 0) {
      return Handlebars.helpers.each(context, options);
    } else {
      return inverse(this);
    }
  } else {
    return fn(context);
  }
});

Handlebars.K = function() {};

Handlebars.createFrame = Object.create || function(object) {
  Handlebars.K.prototype = object;
  var obj = new Handlebars.K();
  Handlebars.K.prototype = null;
  return obj;
};

Handlebars.logger = {
  DEBUG: 0, INFO: 1, WARN: 2, ERROR: 3, level: 3,

  methodMap: {0: 'debug', 1: 'info', 2: 'warn', 3: 'error'},

  // can be overridden in the host environment
  log: function(level, obj) {
    if (Handlebars.logger.level <= level) {
      var method = Handlebars.logger.methodMap[level];
      if (typeof console !== 'undefined' && console[method]) {
        console[method].call(console, obj);
      }
    }
  }
};

Handlebars.log = function(level, obj) { Handlebars.logger.log(level, obj); };

Handlebars.registerHelper('each', function(context, options) {
  var fn = options.fn, inverse = options.inverse;
  var i = 0, ret = "", data;

  if (options.data) {
    data = Handlebars.createFrame(options.data);
  }

  if(context && typeof context === 'object') {
    if(context instanceof Array){
      for(var j = context.length; i<j; i++) {
        if (data) { data.index = i; }
        ret = ret + fn(context[i], { data: data });
      }
    } else {
      for(var key in context) {
        if(context.hasOwnProperty(key)) {
          if(data) { data.key = key; }
          ret = ret + fn(context[key], {data: data});
          i++;
        }
      }
    }
  }

  if(i === 0){
    ret = inverse(this);
  }

  return ret;
});

Handlebars.registerHelper('if', function(context, options) {
  var type = toString.call(context);
  if(type === functionType) { context = context.call(this); }

  if(!context || Handlebars.Utils.isEmpty(context)) {
    return options.inverse(this);
  } else {
    return options.fn(this);
  }
});

Handlebars.registerHelper('unless', function(context, options) {
  var fn = options.fn, inverse = options.inverse;
  options.fn = inverse;
  options.inverse = fn;

  return Handlebars.helpers['if'].call(this, context, options);
});

Handlebars.registerHelper('with', function(context, options) {
  return options.fn(context);
});

Handlebars.registerHelper('log', function(context, options) {
  var level = options.data && options.data.level != null ? parseInt(options.data.level, 10) : 1;
  Handlebars.log(level, context);
});

}(this.Handlebars));
;
// lib/handlebars/utils.js

var errorProps = ['description', 'fileName', 'lineNumber', 'message', 'name', 'number', 'stack'];

Handlebars.Exception = function(message) {
  var tmp = Error.prototype.constructor.apply(this, arguments);

  // Unfortunately errors are not enumerable in Chrome (at least), so `for prop in tmp` doesn't work.
  for (var idx = 0; idx < errorProps.length; idx++) {
    this[errorProps[idx]] = tmp[errorProps[idx]];
  }
};
Handlebars.Exception.prototype = new Error();

// Build out our basic SafeString type
Handlebars.SafeString = function(string) {
  this.string = string;
};
Handlebars.SafeString.prototype.toString = function() {
  return this.string.toString();
};

(function() {
  var escape = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#x27;",
    "`": "&#x60;"
  };

  var badChars = /[&<>"'`]/g;
  var possible = /[&<>"'`]/;

  var escapeChar = function(chr) {
    return escape[chr] || "&amp;";
  };

  Handlebars.Utils = {
    escapeExpression: function(string) {
      // don't escape SafeStrings, since they're already safe
      if (string instanceof Handlebars.SafeString) {
        return string.toString();
      } else if (string == null || string === false) {
        return "";
      }

      if(!possible.test(string)) { return string; }
      return string.replace(badChars, escapeChar);
    },

    isEmpty: function(value) {
      if (!value && value !== 0) {
        return true;
      } else if(Object.prototype.toString.call(value) === "[object Array]" && value.length === 0) {
        return true;
      } else {
        return false;
      }
    }
  };
})();;
// lib/handlebars/runtime.js
Handlebars.VM = {
  template: function(templateSpec) {
    // Just add water
    var container = {
      escapeExpression: Handlebars.Utils.escapeExpression,
      invokePartial: Handlebars.VM.invokePartial,
      programs: [],
      program: function(i, fn, data) {
        var programWrapper = this.programs[i];
        if(data) {
          return Handlebars.VM.program(fn, data);
        } else if(programWrapper) {
          return programWrapper;
        } else {
          programWrapper = this.programs[i] = Handlebars.VM.program(fn);
          return programWrapper;
        }
      },
      programWithDepth: Handlebars.VM.programWithDepth,
      noop: Handlebars.VM.noop
    };

    return function(context, options) {
      options = options || {};
      return templateSpec.call(container, Handlebars, context, options.helpers, options.partials, options.data);
    };
  },

  programWithDepth: function(fn, data, $depth) {
    var args = Array.prototype.slice.call(arguments, 2);

    return function(context, options) {
      options = options || {};

      return fn.apply(this, [context, options.data || data].concat(args));
    };
  },
  program: function(fn, data) {
    return function(context, options) {
      options = options || {};

      return fn(context, options.data || data);
    };
  },
  noop: function() { return ""; },
  invokePartial: function(partial, name, context, helpers, partials, data) {
    var options = { helpers: helpers, partials: partials, data: data };

    if(partial === undefined) {
      throw new Handlebars.Exception("The partial " + name + " could not be found");
    } else if(partial instanceof Function) {
      return partial(context, options);
    } else if (!Handlebars.compile) {
      throw new Handlebars.Exception("The partial " + name + " could not be compiled when running in runtime-only mode");
    } else {
      partials[name] = Handlebars.compile(partial, {data: data !== undefined});
      return partials[name](context, options);
    }
  }
};

Handlebars.template = Handlebars.VM.template;
;

(function (window, Handlebars, undefined ) {
/**
* Transforms flashVars object into a string for Url or Flashvars string.
*
* @method flashVarsToUrl
* @param {Object} flashVarsObject A flashvars object
* @param {String} paramName The name parameter to add to url
* @return {String} Returns flashVars string like: &foo=bar or &param[foo]=bar
*/
var flashVarsToUrl = function( flashVarsObject, paramName ) {
	 var params = '';

	 var paramPrefix = (paramName) ? paramName + '[' : '';
	 var paramSuffix = (paramName) ? ']' : '';

	 for( var i in flashVarsObject ){
		 // check for object representation of plugin config:
		 if( typeof flashVarsObject[i] == 'object' ){
			 for( var j in flashVarsObject[i] ){
				 params+= '&' + paramPrefix + encodeURIComponent( i ) +
				 	'.' + encodeURIComponent( j ) + paramSuffix + 
				 	'=' + encodeURIComponent( flashVarsObject[i][j] );
			 }
		 } else {
			 params+= '&' + paramPrefix + encodeURIComponent( i ) + paramSuffix + '=' + encodeURIComponent( flashVarsObject[i] );
		 }
	 }
	 return params;
};

// Setup handlebars helpers
Handlebars.registerHelper('flashVarsUrl', function(flashVars) {
	return flashVarsToUrl(flashVars, 'flashvars');
});
Handlebars.registerHelper('flashVarsString', function(flashVars) {
	return flashVarsToUrl(flashVars);
});
Handlebars.registerHelper('elAttributes', function( attributes ) {
	var str = '';
	for( var i in attributes ) {
		str += ' ' + i + '="' + attributes[i] + '"';
	}
	return str;
});
// Include kaltura links
Handlebars.registerHelper('kalturaLinks', function() {
	if( ! this.includeKalturaLinks ) {
		return '';
	}
	var template = Handlebars.templates['kaltura_links'];
	return template();
});

Handlebars.registerHelper('seoMetadata', function() {
	var template = Handlebars.templates['seo_metadata'];
	return template(this);
});

})(this, this.Handlebars);
(function(){var a=Handlebars.template,b=Handlebars.templates=Handlebars.templates||{};b.auto=a(function(a,b,c,d,e){function p(a,b){var d="",e,f;d+='<div id="',i=c.playerId,e=i||a.playerId,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"playerId",{hash:{}})),d+=o(e)+'"',i=c.attributes,e=i||a.attributes,i=c.elAttributes,f=i||a.elAttributes,typeof f===l?e=f.call(a,e,{hash:{}}):f===n?e=m.call(a,"elAttributes",e,{hash:{}}):e=f;if(e||e===0)d+=e;d+=">",i=c.seoMetadata,e=i||a.seoMetadata,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"seoMetadata",{hash:{}}));if(e||e===0)d+=e;i=c.kalturaLinks,e=i||a.kalturaLinks,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"kalturaLinks",{hash:{}}));if(e||e===0)d+=e;return d+="</div>\n",d}function q(a,b){var d="",e;return d+="&entry_id=",i=c.entryId,e=i||a.entryId,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"entryId",{hash:{}})),d+=o(e),d}function r(a,b){var d="",e;return d+="&cache_st=",i=c.cacheSt,e=i||a.cacheSt,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"cacheSt",{hash:{}})),d+=o(e),d}c=c||a.helpers;var f="",g,h,i,j,k=this,l="function",m=c.helperMissing,n=void 0,o=this.escapeExpression;i=c.includeSeoMetadata,g=i||b.includeSeoMetadata,h=c["if"],j=k.program(1,p,e),j.hash={},j.fn=j,j.inverse=k.noop,g=h.call(b,g,j);if(g||g===0)f+=g;f+='<script src="',i=c.scriptUrl,g=i||b.scriptUrl,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"scriptUrl",{hash:{}})),f+=o(g)+"?autoembed=true",i=c.entryId,g=i||b.entryId,h=c["if"],j=k.program(3,q,e),j.hash={},j.fn=j,j.inverse=k.noop,g=h.call(b,g,j);if(g||g===0)f+=g;f+="&playerId=",i=c.playerId,g=i||b.playerId,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"playerId",{hash:{}})),f+=o(g),i=c.cacheSt,g=i||b.cacheSt,h=c["if"],j=k.program(5,r,e),j.hash={},j.fn=j,j.inverse=k.noop,g=h.call(b,g,j);if(g||g===0)f+=g;f+="&width=",i=c.width,g=i||b.width,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"width",{hash:{}})),f+=o(g)+"&height=",i=c.height,g=i||b.height,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"height",{hash:{}})),f+=o(g),i=c.flashVars,g=i||b.flashVars,i=c.flashVarsUrl,h=i||b.flashVarsUrl,typeof h===l?g=h.call(b,g,{hash:{}}):h===n?g=m.call(b,"flashVarsUrl",g,{hash:{}}):g=h;if(g||g===0)f+=g;return f+='"></script>',f}),b.dynamic=a(function(a,b,c,d,e){c=c||a.helpers;var f="",g,h,i,j=this,k="function",l=c.helperMissing,m=void 0,n=this.escapeExpression;f+='<script src="',i=c.scriptUrl,g=i||b.scriptUrl,typeof g===k?g=g.call(b,{hash:{}}):g===m&&(g=l.call(b,"scriptUrl",{hash:{}})),f+=n(g)+'"></script>\n<div id="',i=c.playerId,g=i||b.playerId,typeof g===k?g=g.call(b,{hash:{}}):g===m&&(g=l.call(b,"playerId",{hash:{}})),f+=n(g)+'"',i=c.attributes,g=i||b.attributes,i=c.elAttributes,h=i||b.elAttributes,typeof h===k?g=h.call(b,g,{hash:{}}):h===m?g=l.call(b,"elAttributes",g,{hash:{}}):g=h;if(g||g===0)f+=g;f+=">",i=c.seoMetadata,g=i||b.seoMetadata,typeof g===k?g=g.call(b,{hash:{}}):g===m&&(g=l.call(b,"seoMetadata",{hash:{}}));if(g||g===0)f+=g;i=c.kalturaLinks,g=i||b.kalturaLinks,typeof g===k?g=g.call(b,{hash:{}}):g===m&&(g=l.call(b,"kalturaLinks",{hash:{}}));if(g||g===0)f+=g;f+="</div>\n<script>\nkWidget.",i=c.embedMethod,g=i||b.embedMethod,typeof g===k?g=g.call(b,{hash:{}}):g===m&&(g=l.call(b,"embedMethod",{hash:{}})),f+=n(g)+"(",i=c.kWidgetObject,g=i||b.kWidgetObject,typeof g===k?g=g.call(b,{hash:{}}):g===m&&(g=l.call(b,"kWidgetObject",{hash:{}}));if(g||g===0)f+=g;return f+=");\n</script>",f}),b.iframe=a(function(a,b,c,d,e){function p(a,b){var d="",e;return d+="&entry_id=",i=c.entryId,e=i||a.entryId,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"entryId",{hash:{}})),d+=o(e),d}c=c||a.helpers;var f="",g,h,i,j,k=this,l="function",m=c.helperMissing,n=void 0,o=this.escapeExpression;f+='<iframe src="',i=c.protocol,g=i||b.protocol,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"protocol",{hash:{}})),f+=o(g)+"://",i=c.host,g=i||b.host,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"host",{hash:{}})),f+=o(g)+"/p/",i=c.partnerId,g=i||b.partnerId,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"partnerId",{hash:{}})),f+=o(g)+"/sp/",i=c.partnerId,g=i||b.partnerId,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"partnerId",{hash:{}})),f+=o(g)+"00/embedIframeJs/uiconf_id/",i=c.uiConfId,g=i||b.uiConfId,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"uiConfId",{hash:{}})),f+=o(g)+"/partner_id/",i=c.partnerId,g=i||b.partnerId,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"partnerId",{hash:{}})),f+=o(g)+"?iframeembed=true&playerId=",i=c.playerId,g=i||b.playerId,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"playerId",{hash:{}})),f+=o(g),i=c.entryId,g=i||b.entryId,h=c["if"],j=k.program(1,p,e),j.hash={},j.fn=j,j.inverse=k.noop,g=h.call(b,g,j);if(g||g===0)f+=g;i=c.flashVars,g=i||b.flashVars,i=c.flashVarsUrl,h=i||b.flashVarsUrl,typeof h===l?g=h.call(b,g,{hash:{}}):h===n?g=m.call(b,"flashVarsUrl",g,{hash:{}}):g=h;if(g||g===0)f+=g;f+='" width="',i=c.width,g=i||b.width,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"width",{hash:{}})),f+=o(g)+'" height="',i=c.height,g=i||b.height,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"height",{hash:{}})),f+=o(g)+'" allowfullscreen webkitallowfullscreen mozAllowFullScreen frameborder="0"',i=c.attributes,g=i||b.attributes,i=c.elAttributes,h=i||b.elAttributes,typeof h===l?g=h.call(b,g,{hash:{}}):h===n?g=m.call(b,"elAttributes",g,{hash:{}}):g=h;if(g||g===0)f+=g;f+=">",i=c.seoMetadata,g=i||b.seoMetadata,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"seoMetadata",{hash:{}}));if(g||g===0)f+=g;i=c.kalturaLinks,g=i||b.kalturaLinks,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"kalturaLinks",{hash:{}}));if(g||g===0)f+=g;return f+="</iframe>",f}),b.kaltura_links=a(function(a,b,c,d,e){c=c||a.helpers;var f,g=this;return'<a href="http://corp.kaltura.com/products/video-platform-features">Video Platform</a>\n<a href="http://corp.kaltura.com/Products/Features/Video-Management">Video Management</a> \n<a href="http://corp.kaltura.com/Video-Solutions">Video Solutions</a>\n<a href="http://corp.kaltura.com/Products/Features/Video-Player">Video Player</a>'}),b.legacy=a(function(a,b,c,d,e){function p(a,b){var d="",e;return d+='<script src="',i=c.scriptUrl,e=i||a.scriptUrl,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"scriptUrl",{hash:{}})),d+=o(e)+'"></script>\n',d}function q(a,b){var d="",e;return d+='\n	<a rel="media:thumbnail" href="',i=c.entryMeta,e=i||a.entryMeta,e=e===null||e===undefined||e===!1?e:e.thumbnailUrl,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"entryMeta.thumbnailUrl",{hash:{}})),d+=o(e)+'"></a>\n	<span property="dc:description" content="',i=c.entryMeta,e=i||a.entryMeta,e=e===null||e===undefined||e===!1?e:e.description,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"entryMeta.description",{hash:{}})),d+=o(e)+'"></span>\n	<span property="media:title" content="',i=c.entryMeta,e=i||a.entryMeta,e=e===null||e===undefined||e===!1?e:e.name,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"entryMeta.name",{hash:{}})),d+=o(e)+'"></span>\n	<span property="media:width" content="',i=c.width,e=i||a.width,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"width",{hash:{}})),d+=o(e)+'"></span>\n	<span property="media:height" content="',i=c.height,e=i||a.height,typeof e===l?e=e.call(a,{hash:{}}):e===n&&(e=m.call(a,"height",{hash:{}})),d+=o(e)+'"></span>\n	<span property="media:type" content="application/x-shockwave-flash"></span>	\n	',d}c=c||a.helpers;var f="",g,h,i,j,k=this,l="function",m=c.helperMissing,n=void 0,o=this.escapeExpression;i=c.includeHtml5Library,g=i||b.includeHtml5Library,h=c["if"],j=k.program(1,p,e),j.hash={},j.fn=j,j.inverse=k.noop,g=h.call(b,g,j);if(g||g===0)f+=g;f+='<object id="',i=c.playerId,g=i||b.playerId,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"playerId",{hash:{}})),f+=o(g)+'" name="',i=c.playerId,g=i||b.playerId,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"playerId",{hash:{}})),f+=o(g)+'" type="application/x-shockwave-flash" allowFullScreen="true" allowNetworking="all" allowScriptAccess="always" height="',i=c.height,g=i||b.height,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"height",{hash:{}})),f+=o(g)+'" width="',i=c.width,g=i||b.width,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"width",{hash:{}})),f+=o(g)+'" bgcolor="#000000"',i=c.attributes,g=i||b.attributes,i=c.elAttributes,h=i||b.elAttributes,typeof h===l?g=h.call(b,g,{hash:{}}):h===n?g=m.call(b,"elAttributes",g,{hash:{}}):g=h;if(g||g===0)f+=g;f+=' data="',i=c.swfUrl,g=i||b.swfUrl,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"swfUrl",{hash:{}})),f+=o(g)+'">\n	<param name="allowFullScreen" value="true" />\n	<param name="allowNetworking" value="all" />\n	<param name="allowScriptAccess" value="always" />\n	<param name="bgcolor" value="#000000" />\n	<param name="flashVars" value="',i=c.flashVars,g=i||b.flashVars,i=c.flashVarsString,h=i||b.flashVarsString,typeof h===l?g=h.call(b,g,{hash:{}}):h===n?g=m.call(b,"flashVarsString",g,{hash:{}}):g=h;if(g||g===0)f+=g;f+='" />\n	<param name="movie" value="',i=c.swfUrl,g=i||b.swfUrl,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"swfUrl",{hash:{}})),f+=o(g)+'" />\n	',i=c.includeSeoMetadata,g=i||b.includeSeoMetadata,h=c["if"],j=k.program(3,q,e),j.hash={},j.fn=j,j.inverse=k.noop,g=h.call(b,g,j);if(g||g===0)f+=g;i=c.kalturaLinks,g=i||b.kalturaLinks,typeof g===l?g=g.call(b,{hash:{}}):g===n&&(g=m.call(b,"kalturaLinks",{hash:{}}));if(g||g===0)f+=g;return f+="\n</object>",f}),b.seo_metadata=a(function(a,b,c,d,e){function o(a,b){var d="",e;return d+='\n<span itemprop="name" content="',h=c.entryMeta,e=h||a.entryMeta,e=e===null||e===undefined||e===!1?e:e.name,typeof e===k?e=e.call(a,{hash:{}}):e===m&&(e=l.call(a,"entryMeta.name",{hash:{}})),d+=n(e)+'"></span>\n<span itemprop="description" content="',h=c.entryMeta,e=h||a.entryMeta,e=e===null||e===undefined||e===!1?e:e.description,typeof e===k?e=e.call(a,{hash:{}}):e===m&&(e=l.call(a,"entryMeta.description",{hash:{}})),d+=n(e)+'"></span>\n<span itemprop="duration" content="',h=c.entryMeta,e=h||a.entryMeta,e=e===null||e===undefined||e===!1?e:e.duration,typeof e===k?e=e.call(a,{hash:{}}):e===m&&(e=l.call(a,"entryMeta.duration",{hash:{}})),d+=n(e)+'"></span>\n<span itemprop="thumbnail" content="',h=c.entryMeta,e=h||a.entryMeta,e=e===null||e===undefined||e===!1?e:e.thumbnailUrl,typeof e===k?e=e.call(a,{hash:{}}):e===m&&(e=l.call(a,"entryMeta.thumbnailUrl",{hash:{}})),d+=n(e)+'"></span>\n<span itemprop="width" content="',h=c.width,e=h||a.width,typeof e===k?e=e.call(a,{hash:{}}):e===m&&(e=l.call(a,"width",{hash:{}})),d+=n(e)+'"></span>\n<span itemprop="height" content="',h=c.height,e=h||a.height,typeof e===k?e=e.call(a,{hash:{}}):e===m&&(e=l.call(a,"height",{hash:{}})),d+=n(e)+'"></span>\n',d}c=c||a.helpers;var f,g,h,i,j=this,k="function",l=c.helperMissing,m=void 0,n=this.escapeExpression;return h=c.includeSeoMetadata,f=h||b.includeSeoMetadata,g=c["if"],i=j.program(1,o,e),i.hash={},i.fn=i,i.inverse=j.noop,f=g.call(b,f,i),f||f===0?f:""})})()
// Add indexOf to array object
if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (searchElement /*, fromIndex */ ) {
        "use strict";
        if (this == null) {
            throw new TypeError();
        }
        var t = Object(this);
        var len = t.length >>> 0;
        if (len === 0) {
            return -1;
        }
        var n = 0;
        if (arguments.length > 1) {
            n = Number(arguments[1]);
            if (n !== n) { // shortcut for verifying if it's NaN
                n = 0;
            } else if (n !== 0 && n !== Infinity && n !== -Infinity) {
                n = (n > 0 || -1) * Math.floor(Math.abs(n));
            }
        }
        if (n >= len) {
            return -1;
        }
        var k = n >= 0 ? n : Math.max(len - Math.abs(n), 0);
        for (; k < len; k++) {
            if (k in t && t[k] === searchElement) {
                return k;
            }
        }
        return -1;
    };
}
// Add keys for Object
if (!Object.keys) {
  Object.keys = (function () {
    var hasOwnProperty = Object.prototype.hasOwnProperty,
        hasDontEnumBug = !({toString: null}).propertyIsEnumerable('toString'),
        dontEnums = [
          'toString',
          'toLocaleString',
          'valueOf',
          'hasOwnProperty',
          'isPrototypeOf',
          'propertyIsEnumerable',
          'constructor'
        ],
        dontEnumsLength = dontEnums.length;
 
    return function (obj) {
      if (typeof obj !== 'object' && typeof obj !== 'function' || obj === null) {
        throw new TypeError('Object.keys called on non-object');
      }
 
      var result = [];
 
      for (var prop in obj) {
        if (hasOwnProperty.call(obj, prop)) {
          result.push(prop);
        }
      }
 
      if (hasDontEnumBug) {
        for (var i=0; i < dontEnumsLength; i++) {
          if (hasOwnProperty.call(obj, dontEnums[i])) {
            result.push(dontEnums[i]);
          } 
        }
      }
      return result;
    };
  })();
}
(function( window, undefined ) {
/**
* Kaltura Embed Code Generator
* Used to generate different type of embed codes
* Depended on Handlebars ( http://handlebarsjs.com/ )
* 
* @class EmbedCodeGenerator
* @constructor
*/
var EmbedCodeGenerator = function( options ) {
	this.init( options );
};

EmbedCodeGenerator.prototype = {

	types: ['auto', 'dynamic', 'thumb', 'iframe', 'legacy'],
	required: ['widgetId', 'partnerId', 'uiConfId'],

	defaults: {
		/**
		* Embed code type to generate
		* Can we one of: ['auto', 'dynamic', 'thumb', 'iframe', 'legacy']
		* 
		* @property embedType
		* @type {String}
		* @default "auto"
		*/		
		embedType: 'auto',
		/**
		* The Player element Id / Name that will be used for embed code
		* 
		* @property playerId
		* @type {String}
		* @default "kaltura_player"
		*/			
		playerId: 'kaltura_player',
		/**
		* Embed HTTP protocol to use
		* Can we one of: ['http', 'https']
		* 
		* @property protocol
		* @type {String}
		* @default "http"
		*/			
		protocol: 'http',
		/**
		* Host for loading html5 library & kdp swf
		* 
		* @property host
		* @type {String}
		* @default "www.kaltura.com"
		*/			
		host: 'www.kaltura.com',
		/**
		* Secured host for loading html5 library & kdp swf
		* Used if protocol is: 'https'
		* 
		* @property securedHost
		* @type {String}
		* @default "www.kaltura.com"
		*/		
		securedHost: 'www.kaltura.com',
		/**
		* Kaltura Widget Id
		* 
		* @property widgetId
		* @type {String}
		* @default "_{partnerId}"
		*/
		widgetId: null,
		/**
		* Kaltura Partner Id
		* 
		* @property partnerId
		* @type {Number}
		* @default null,
		*/
		partnerId: null,
		/**
		* Add cacheSt parameter to bust cache
		* Should be unix timestamp of future time
		* 
		* @property cacheSt
		* @type {Number}
		* @default null,
		*/		
		cacheSt: null,
		/**
		* Kaltura UiConf Id
		* 
		* @property uiConfId
		* @type {Number}
		* @default null,
		*/		
		uiConfId: null,
		/**
		* Kaltura Entry Id
		* 
		* @property entryId
		* @type {String}
		* @default null,
		*/		
		entryId: null,		
		/**
		* Entry Object similar to:
		* {
		*	name: 'Foo', 
		*	description: 'Bar', 
		*	thumbUrl: 'http://cdnbakmi.kaltura.com/thumbnail/...'
		* }
		* 
		* @property entryMeta
		* @type {Object}
		* @default {},
		*/		
		entryMeta: {},
		/**
		* Sets Player Width
		* 
		* @property width
		* @type {Number}
		* @default 400,
		*/		
		width: 400,
		/**
		* Sets Player Height
		* 
		* @property height
		* @type {Number}
		* @default 330,
		*/		
		height: 330,
		/**
		* Adds additonal attributes to embed code.
		* Example:
		* {
		*	"class": "player"
		* }
		* 
		* @property attributes
		* @type {Object}
		* @default {},
		*/	
		attributes: {},
		/**
		* Adds flashVars to player
		* Example:
		* {
		*	"autoPlay": "true"
		* }
		* 
		* @property flashVars
		* @type {Object}
		* @default {},
		*/			
		flashVars: {},
		/**
		* Include Kaltura SEO links to embed code
		* 
		* @property includeKalturaLinks
		* @type {Boolean}
		* @default true,
		*/
		includeKalturaLinks: true,
		/**
		* Include Entry Seo Metadata
		* Metadata is taken from {entryMeta} object
		* 
		* @property includeSeoMetadata
		* @type {Boolean}
		* @default false,
		*/
		includeSeoMetadata: false,
		/**
		* Include HTML5 library script
		* 
		* @property includeHtml5Library
		* @type {Boolean}
		* @default true,
		*/
		includeHtml5Library: true
	},
	/**
	* Merge two object together
	*
	* @method extend
	* @param {Object} destination object to merge into
	* @param {Object} sourece object to merge from
	* @return {Object} Merged object
	*/
	extend: function(destination, source) {
	    for (var property in source) {
	        if (source.hasOwnProperty(property) && !destination.hasOwnProperty(property)) {
	            destination[property] = source[property];
	        }
	    }
	    return destination;
	},
	/**
	* Check if property is null
	*
	* @method isNull
	* @param {Any} property some var
	* @return {Boolean}
	*/
	isNull: function( property ) {
		if (property.length && property.length > 0) {
			return false;
		}
	    if (property.length && property.length === 0) {
	    	return true;
	    }
		if( typeof property === 'object' ) {
			return (Object.keys(property).length > 0) ? false : true;
		}
		return !property;
	},
	/**
	* Set default options to EmbedCodeGenerator instance
	*
	* @method init
	* @param {Object} options Configuration object based on defaults object
	* @return {Object} Returns the current instance
	*/
	init: function( options ) {

		options = options || {}; 

		var defaults = this.defaults;

		// Make sure Handlebars is available
		if( typeof Handlebars === undefined ) {
			throw 'Handlebars is not defined, please include Handlebars.js before this script';
		}

		// Merge options with defaults
		if( typeof options === 'object' ) {
			this.options = this.extend(options, this.defaults);
		}
		// Set widgetId to partnerId if not defined
		if( ! this.config('widgetId') && this.config('partnerId') ) {
			this.config('widgetId', '_' + this.config('partnerId'));
		}

		return this;
	},
	/**
	* Get or Set default configuration
	*
	* @method config
	* @param {String} key configuration property name
	* @param {Any} value to set
	* @return {Mixed} Return the value for the key, configuration object or null
	*/
	config: function( key, val ) {
		// Used as getter
		if( val === undefined && typeof key === 'string' && this.options.hasOwnProperty(key) ) {
			return this.options[ key ];
		}
		// Get all options
		if( key === undefined && val === undefined ) {
			return this.options;
		}
		// Used as setter
		if( typeof key === 'string' && val !== undefined ) {
			this.options[ key ] = val;
		}
		return null;
	},
	/**
	* Check if required parameters are missing
	*
	* @method checkRequiredParams
	* @param {Object} Configuration object
	* @return throws exception if missing parameters
	*/
	checkRequiredParams: function( params ) {
		var requiredLength = this.required.length,
			i = 0;
		// Check for required configuration
		for(i; i<requiredLength; i++) {
			if( this.isNull(params[this.required[i]]) ) {
				throw 'Missing required parameter: ' + this.required[i];
			}
		}
	},	
	/**
	* Check if embed type is part of types array
	*
	* @method checkValidType
	* @param {String} type - One of config embed types
	* @return throws exception if not valid
	*/
	checkValidType: function( type ) {
		var valid = (this.types.indexOf(type) !== -1) ? true : false;;
		if( !valid ) {
			throw 'Embed type: ' + type + ' is not valid. Available types: ' + this.types.join(",");
		}
	},
	/**
	* Get Handlebars template based on embed type
	*
	* @method getTemplate
	* @param {String} type - One of config embed types
	* @return {Mixed} If found returns Handlebars template function, else null
	*/
	getTemplate: function( type ) {
		// Dynamic embed and Thumb embed has the same template
		type = (type == 'thumb') ? 'dynamic' : type;		
		return ( type && Handlebars.templates && Handlebars.templates[ type ] ) ? Handlebars.templates[ type ] : null;
	},
	/**
	* Check if embed type is using kWidget embed
	*
	* @method isKWidgetEmbed
	* @param {String} type - One of config embed types
	* @return {Boolean} true / false
	*/
	isKWidgetEmbed: function( type ) {
		return ( type == 'dynamic' || type == 'thumb' ) ? true : false;
	},
	/**
	* Get embed host based on protocol
	*
	* @method getHost
	* @param {Object} params Configuration object
	* @return {String} Embed host
	*/
	getHost: function( params ) {
		return (params.protocol === 'http') ? params.host : params.securedHost;
	},
	/**
	* Generate HTML5 library script url
	*
	* @method getScriptUrl
	* @param {Object} params Configuration object
	* @return {String} HTML5 library script Url
	*/
	getScriptUrl: function( params ) {
		return params.protocol + '://' + this.getHost(params) + '/p/' + params.partnerId + '/sp/' + params.partnerId + '00/embedIframeJs/uiconf_id/' + params.uiConfId + '/partner_id/' + params.partnerId;
	},
	/**
	* Generate Flash SWF url
	*
	* @method getSwfUrl
	* @param {Object} params Configuration object
	* @return {String} Flash player SWF url
	*/
	getSwfUrl: function( params ) {
		var cacheSt = (params.cacheSt) ? '/cache_st/' + params.cacheSt : '';
		var entryId = (params.entryId) ? '/entry_id/' + params.entryId : '';
		return params.protocol + '://' + this.getHost(params) + '/index.php/kwidget' + cacheSt + 
				'/wid/' + params.widgetId + '/uiconf_id/' + params.uiConfId + entryId;
	},
	/**
	* Generate attributes object based on configuration
	*
	* @method getAttributes
	* @param {Object} params Configuration object
	* @return {Object} Attributes object
	*/
	getAttributes: function( params ) {
		var attrs = {};

		// Add style attribute for dynamic / thumb embeds
		// Or if includeSeoMetadata is true
		if( this.isKWidgetEmbed( params.embedType ) || params.includeSeoMetadata ) {
			attrs['style'] = 'width: ' + params.width + 'px; height: ' + params.height + 'px;';
		}

		// Add Seo attributes
		if( params.includeSeoMetadata ) {
			if( params.embedType == 'legacy' ) {
				attrs["xmlns:dc"] = "http://purl.org/dc/terms/";
				attrs["xmlns:media"] = "http://search.yahoo.com/searchmonkey/media/";
				attrs["rel"] = "media:video";
				attrs["resource"] = this.getSwfUrl( params );
			} else {
				attrs['itemprop'] = 'video'; 
				attrs['itemscope itemtype'] = 'http://schema.org/VideoObject';
			}
		}

		return attrs;
	},
	/**
	* Generate kWidget object for HTML5 library
	*
	* @method getEmbedObject
	* @param {Object} params Configuration object
	* @return {Object} kWidget object
	*/
	getEmbedObject: function( params ) {
		// Used by kWidget.embed
		var embedObject = {
			targetId: params.playerId,		
			wid: params.widgetId,
			uiconf_id: params.uiConfId,
			flashvars: params.flashVars
		};
		// Add cacheSt
		if( params.cacheSt ) {
			embedObject['cache_st'] = params.cacheSt;
		}
		// Add entryId
		if( params.entryId ) {
			embedObject['entry_id'] = params.entryId;
		}
		// Transform object into a string
		return JSON.stringify(embedObject, null, 2);
	},
	/**
	* Generate Final Embed Code
	*
	* @method getCode
	* @param {Object} params Configuration object
	* @return {String} HTML embed code
	*/
	getCode: function( localParams ) {
		// Set default for params
		var params = (localParams === undefined) ? {} : this.extend({}, localParams);
		// Merge with options
		params = this.extend( params, this.config() );
		// Set widgetId to partnerId if undefined
		if( ! params.widgetId && params.partnerId ) {
			params.widgetId = '_' + params.partnerId;
		}

		this.checkRequiredParams(params); // Check for missing params
		this.checkValidType(params.embedType); // Check if embed type is valid

		// Check if we have a template
		var template = this.getTemplate(params.embedType);
		if( ! template ) {
			throw 'Template: ' + params.embedType + ' is not defined as Handlebars template';
		}

		// Add basic attributes for all embed codes
		var data = {
			host: this.getHost( params ),
			scriptUrl: this.getScriptUrl( params ),
			attributes: this.getAttributes( params )
		};
		// Add SWF Url for flash embeds
		if( params.embedType === 'legacy' ) {
			data['swfUrl'] = this.getSwfUrl( params );
		}
		// Add embed method and embed object for dynamic embeds
		if( this.isKWidgetEmbed( params.embedType ) ) {
			data['embedMethod'] = (params.embedType == 'dynamic') ? 'embed' : 'thumbEmbed';
			data['kWidgetObject'] = this.getEmbedObject( params );
		}

		data = this.extend( data, params );
		return template( data );
	}
};

// Export module to window object
window.kEmbedCodeGenerator = EmbedCodeGenerator;

 })(this);

(function( $ ){
	$.fn.qrcode = function(options) {
//---------------------------------------------------------------------
// QRCode for JavaScript
//
// Copyright (c) 2009 Kazuhiko Arase
//
// URL: http://www.d-project.com/
//
// Licensed under the MIT license:
//   http://www.opensource.org/licenses/mit-license.php
//
// The word "QR Code" is registered trademark of 
// DENSO WAVE INCORPORATED
//   http://www.denso-wave.com/qrcode/faqpatent-e.html
//
//---------------------------------------------------------------------

//---------------------------------------------------------------------
// QR8bitByte
//---------------------------------------------------------------------

function QR8bitByte(data) {
	this.mode = QRMode.MODE_8BIT_BYTE;
	this.data = data;
}

QR8bitByte.prototype = {

	getLength : function(buffer) {
		return this.data.length;
	},
	
	write : function(buffer) {
		for (var i = 0; i < this.data.length; i++) {
			// not JIS ...
			buffer.put(this.data.charCodeAt(i), 8);
		}
	}
};

//---------------------------------------------------------------------
// QRCode
//---------------------------------------------------------------------

function QRCode(typeNumber, errorCorrectLevel) {
	this.typeNumber = typeNumber;
	this.errorCorrectLevel = errorCorrectLevel;
	this.modules = null;
	this.moduleCount = 0;
	this.dataCache = null;
	this.dataList = new Array();
}

QRCode.prototype = {
	
	addData : function(data) {
		var newData = new QR8bitByte(data);
		this.dataList.push(newData);
		this.dataCache = null;
	},
	
	isDark : function(row, col) {
		if (row < 0 || this.moduleCount <= row || col < 0 || this.moduleCount <= col) {
			throw new Error(row + "," + col);
		}
		return this.modules[row][col];
	},

	getModuleCount : function() {
		return this.moduleCount;
	},
	
	make : function() {
		// Calculate automatically typeNumber if provided is < 1
		if (this.typeNumber < 1 ){
			var typeNumber = 1;
			for (typeNumber = 1; typeNumber < 40; typeNumber++) {
				var rsBlocks = QRRSBlock.getRSBlocks(typeNumber, this.errorCorrectLevel);

				var buffer = new QRBitBuffer();
				var totalDataCount = 0;
				for (var i = 0; i < rsBlocks.length; i++) {
					totalDataCount += rsBlocks[i].dataCount;
				}

				for (var i = 0; i < this.dataList.length; i++) {
					var data = this.dataList[i];
					buffer.put(data.mode, 4);
					buffer.put(data.getLength(), QRUtil.getLengthInBits(data.mode, typeNumber) );
					data.write(buffer);
				}
				if (buffer.getLengthInBits() <= totalDataCount * 8)
					break;
			}
			this.typeNumber = typeNumber;
		}
		this.makeImpl(false, this.getBestMaskPattern() );
	},
	
	makeImpl : function(test, maskPattern) {
		
		this.moduleCount = this.typeNumber * 4 + 17;
		this.modules = new Array(this.moduleCount);
		
		for (var row = 0; row < this.moduleCount; row++) {
			
			this.modules[row] = new Array(this.moduleCount);
			
			for (var col = 0; col < this.moduleCount; col++) {
				this.modules[row][col] = null;//(col + row) % 3;
			}
		}
	
		this.setupPositionProbePattern(0, 0);
		this.setupPositionProbePattern(this.moduleCount - 7, 0);
		this.setupPositionProbePattern(0, this.moduleCount - 7);
		this.setupPositionAdjustPattern();
		this.setupTimingPattern();
		this.setupTypeInfo(test, maskPattern);
		
		if (this.typeNumber >= 7) {
			this.setupTypeNumber(test);
		}
	
		if (this.dataCache == null) {
			this.dataCache = QRCode.createData(this.typeNumber, this.errorCorrectLevel, this.dataList);
		}
	
		this.mapData(this.dataCache, maskPattern);
	},

	setupPositionProbePattern : function(row, col)  {
		
		for (var r = -1; r <= 7; r++) {
			
			if (row + r <= -1 || this.moduleCount <= row + r) continue;
			
			for (var c = -1; c <= 7; c++) {
				
				if (col + c <= -1 || this.moduleCount <= col + c) continue;
				
				if ( (0 <= r && r <= 6 && (c == 0 || c == 6) )
						|| (0 <= c && c <= 6 && (r == 0 || r == 6) )
						|| (2 <= r && r <= 4 && 2 <= c && c <= 4) ) {
					this.modules[row + r][col + c] = true;
				} else {
					this.modules[row + r][col + c] = false;
				}
			}		
		}		
	},
	
	getBestMaskPattern : function() {
	
		var minLostPoint = 0;
		var pattern = 0;
	
		for (var i = 0; i < 8; i++) {
			
			this.makeImpl(true, i);
	
			var lostPoint = QRUtil.getLostPoint(this);
	
			if (i == 0 || minLostPoint >  lostPoint) {
				minLostPoint = lostPoint;
				pattern = i;
			}
		}
	
		return pattern;
	},
	
	createMovieClip : function(target_mc, instance_name, depth) {
	
		var qr_mc = target_mc.createEmptyMovieClip(instance_name, depth);
		var cs = 1;
	
		this.make();

		for (var row = 0; row < this.modules.length; row++) {
			
			var y = row * cs;
			
			for (var col = 0; col < this.modules[row].length; col++) {
	
				var x = col * cs;
				var dark = this.modules[row][col];
			
				if (dark) {
					qr_mc.beginFill(0, 100);
					qr_mc.moveTo(x, y);
					qr_mc.lineTo(x + cs, y);
					qr_mc.lineTo(x + cs, y + cs);
					qr_mc.lineTo(x, y + cs);
					qr_mc.endFill();
				}
			}
		}
		
		return qr_mc;
	},

	setupTimingPattern : function() {
		
		for (var r = 8; r < this.moduleCount - 8; r++) {
			if (this.modules[r][6] != null) {
				continue;
			}
			this.modules[r][6] = (r % 2 == 0);
		}
	
		for (var c = 8; c < this.moduleCount - 8; c++) {
			if (this.modules[6][c] != null) {
				continue;
			}
			this.modules[6][c] = (c % 2 == 0);
		}
	},
	
	setupPositionAdjustPattern : function() {
	
		var pos = QRUtil.getPatternPosition(this.typeNumber);
		
		for (var i = 0; i < pos.length; i++) {
		
			for (var j = 0; j < pos.length; j++) {
			
				var row = pos[i];
				var col = pos[j];
				
				if (this.modules[row][col] != null) {
					continue;
				}
				
				for (var r = -2; r <= 2; r++) {
				
					for (var c = -2; c <= 2; c++) {
					
						if (r == -2 || r == 2 || c == -2 || c == 2 
								|| (r == 0 && c == 0) ) {
							this.modules[row + r][col + c] = true;
						} else {
							this.modules[row + r][col + c] = false;
						}
					}
				}
			}
		}
	},
	
	setupTypeNumber : function(test) {
	
		var bits = QRUtil.getBCHTypeNumber(this.typeNumber);
	
		for (var i = 0; i < 18; i++) {
			var mod = (!test && ( (bits >> i) & 1) == 1);
			this.modules[Math.floor(i / 3)][i % 3 + this.moduleCount - 8 - 3] = mod;
		}
	
		for (var i = 0; i < 18; i++) {
			var mod = (!test && ( (bits >> i) & 1) == 1);
			this.modules[i % 3 + this.moduleCount - 8 - 3][Math.floor(i / 3)] = mod;
		}
	},
	
	setupTypeInfo : function(test, maskPattern) {
	
		var data = (this.errorCorrectLevel << 3) | maskPattern;
		var bits = QRUtil.getBCHTypeInfo(data);
	
		// vertical		
		for (var i = 0; i < 15; i++) {
	
			var mod = (!test && ( (bits >> i) & 1) == 1);
	
			if (i < 6) {
				this.modules[i][8] = mod;
			} else if (i < 8) {
				this.modules[i + 1][8] = mod;
			} else {
				this.modules[this.moduleCount - 15 + i][8] = mod;
			}
		}
	
		// horizontal
		for (var i = 0; i < 15; i++) {
	
			var mod = (!test && ( (bits >> i) & 1) == 1);
			
			if (i < 8) {
				this.modules[8][this.moduleCount - i - 1] = mod;
			} else if (i < 9) {
				this.modules[8][15 - i - 1 + 1] = mod;
			} else {
				this.modules[8][15 - i - 1] = mod;
			}
		}
	
		// fixed module
		this.modules[this.moduleCount - 8][8] = (!test);
	
	},
	
	mapData : function(data, maskPattern) {
		
		var inc = -1;
		var row = this.moduleCount - 1;
		var bitIndex = 7;
		var byteIndex = 0;
		
		for (var col = this.moduleCount - 1; col > 0; col -= 2) {
	
			if (col == 6) col--;
	
			while (true) {
	
				for (var c = 0; c < 2; c++) {
					
					if (this.modules[row][col - c] == null) {
						
						var dark = false;
	
						if (byteIndex < data.length) {
							dark = ( ( (data[byteIndex] >>> bitIndex) & 1) == 1);
						}
	
						var mask = QRUtil.getMask(maskPattern, row, col - c);
	
						if (mask) {
							dark = !dark;
						}
						
						this.modules[row][col - c] = dark;
						bitIndex--;
	
						if (bitIndex == -1) {
							byteIndex++;
							bitIndex = 7;
						}
					}
				}
								
				row += inc;
	
				if (row < 0 || this.moduleCount <= row) {
					row -= inc;
					inc = -inc;
					break;
				}
			}
		}
		
	}

};

QRCode.PAD0 = 0xEC;
QRCode.PAD1 = 0x11;

QRCode.createData = function(typeNumber, errorCorrectLevel, dataList) {
	
	var rsBlocks = QRRSBlock.getRSBlocks(typeNumber, errorCorrectLevel);
	
	var buffer = new QRBitBuffer();
	
	for (var i = 0; i < dataList.length; i++) {
		var data = dataList[i];
		buffer.put(data.mode, 4);
		buffer.put(data.getLength(), QRUtil.getLengthInBits(data.mode, typeNumber) );
		data.write(buffer);
	}

	// calc num max data.
	var totalDataCount = 0;
	for (var i = 0; i < rsBlocks.length; i++) {
		totalDataCount += rsBlocks[i].dataCount;
	}

	if (buffer.getLengthInBits() > totalDataCount * 8) {
		throw new Error("code length overflow. ("
			+ buffer.getLengthInBits()
			+ ">"
			+  totalDataCount * 8
			+ ")");
	}

	// end code
	if (buffer.getLengthInBits() + 4 <= totalDataCount * 8) {
		buffer.put(0, 4);
	}

	// padding
	while (buffer.getLengthInBits() % 8 != 0) {
		buffer.putBit(false);
	}

	// padding
	while (true) {
		
		if (buffer.getLengthInBits() >= totalDataCount * 8) {
			break;
		}
		buffer.put(QRCode.PAD0, 8);
		
		if (buffer.getLengthInBits() >= totalDataCount * 8) {
			break;
		}
		buffer.put(QRCode.PAD1, 8);
	}

	return QRCode.createBytes(buffer, rsBlocks);
}

QRCode.createBytes = function(buffer, rsBlocks) {

	var offset = 0;
	
	var maxDcCount = 0;
	var maxEcCount = 0;
	
	var dcdata = new Array(rsBlocks.length);
	var ecdata = new Array(rsBlocks.length);
	
	for (var r = 0; r < rsBlocks.length; r++) {

		var dcCount = rsBlocks[r].dataCount;
		var ecCount = rsBlocks[r].totalCount - dcCount;

		maxDcCount = Math.max(maxDcCount, dcCount);
		maxEcCount = Math.max(maxEcCount, ecCount);
		
		dcdata[r] = new Array(dcCount);
		
		for (var i = 0; i < dcdata[r].length; i++) {
			dcdata[r][i] = 0xff & buffer.buffer[i + offset];
		}
		offset += dcCount;
		
		var rsPoly = QRUtil.getErrorCorrectPolynomial(ecCount);
		var rawPoly = new QRPolynomial(dcdata[r], rsPoly.getLength() - 1);

		var modPoly = rawPoly.mod(rsPoly);
		ecdata[r] = new Array(rsPoly.getLength() - 1);
		for (var i = 0; i < ecdata[r].length; i++) {
            var modIndex = i + modPoly.getLength() - ecdata[r].length;
			ecdata[r][i] = (modIndex >= 0)? modPoly.get(modIndex) : 0;
		}

	}
	
	var totalCodeCount = 0;
	for (var i = 0; i < rsBlocks.length; i++) {
		totalCodeCount += rsBlocks[i].totalCount;
	}

	var data = new Array(totalCodeCount);
	var index = 0;

	for (var i = 0; i < maxDcCount; i++) {
		for (var r = 0; r < rsBlocks.length; r++) {
			if (i < dcdata[r].length) {
				data[index++] = dcdata[r][i];
			}
		}
	}

	for (var i = 0; i < maxEcCount; i++) {
		for (var r = 0; r < rsBlocks.length; r++) {
			if (i < ecdata[r].length) {
				data[index++] = ecdata[r][i];
			}
		}
	}

	return data;

}

//---------------------------------------------------------------------
// QRMode
//---------------------------------------------------------------------

var QRMode = {
	MODE_NUMBER :		1 << 0,
	MODE_ALPHA_NUM : 	1 << 1,
	MODE_8BIT_BYTE : 	1 << 2,
	MODE_KANJI :		1 << 3
};

//---------------------------------------------------------------------
// QRErrorCorrectLevel
//---------------------------------------------------------------------
 
var QRErrorCorrectLevel = {
	L : 1,
	M : 0,
	Q : 3,
	H : 2
};

//---------------------------------------------------------------------
// QRMaskPattern
//---------------------------------------------------------------------

var QRMaskPattern = {
	PATTERN000 : 0,
	PATTERN001 : 1,
	PATTERN010 : 2,
	PATTERN011 : 3,
	PATTERN100 : 4,
	PATTERN101 : 5,
	PATTERN110 : 6,
	PATTERN111 : 7
};

//---------------------------------------------------------------------
// QRUtil
//---------------------------------------------------------------------
 
var QRUtil = {

    PATTERN_POSITION_TABLE : [
	    [],
	    [6, 18],
	    [6, 22],
	    [6, 26],
	    [6, 30],
	    [6, 34],
	    [6, 22, 38],
	    [6, 24, 42],
	    [6, 26, 46],
	    [6, 28, 50],
	    [6, 30, 54],		
	    [6, 32, 58],
	    [6, 34, 62],
	    [6, 26, 46, 66],
	    [6, 26, 48, 70],
	    [6, 26, 50, 74],
	    [6, 30, 54, 78],
	    [6, 30, 56, 82],
	    [6, 30, 58, 86],
	    [6, 34, 62, 90],
	    [6, 28, 50, 72, 94],
	    [6, 26, 50, 74, 98],
	    [6, 30, 54, 78, 102],
	    [6, 28, 54, 80, 106],
	    [6, 32, 58, 84, 110],
	    [6, 30, 58, 86, 114],
	    [6, 34, 62, 90, 118],
	    [6, 26, 50, 74, 98, 122],
	    [6, 30, 54, 78, 102, 126],
	    [6, 26, 52, 78, 104, 130],
	    [6, 30, 56, 82, 108, 134],
	    [6, 34, 60, 86, 112, 138],
	    [6, 30, 58, 86, 114, 142],
	    [6, 34, 62, 90, 118, 146],
	    [6, 30, 54, 78, 102, 126, 150],
	    [6, 24, 50, 76, 102, 128, 154],
	    [6, 28, 54, 80, 106, 132, 158],
	    [6, 32, 58, 84, 110, 136, 162],
	    [6, 26, 54, 82, 110, 138, 166],
	    [6, 30, 58, 86, 114, 142, 170]
    ],

    G15 : (1 << 10) | (1 << 8) | (1 << 5) | (1 << 4) | (1 << 2) | (1 << 1) | (1 << 0),
    G18 : (1 << 12) | (1 << 11) | (1 << 10) | (1 << 9) | (1 << 8) | (1 << 5) | (1 << 2) | (1 << 0),
    G15_MASK : (1 << 14) | (1 << 12) | (1 << 10)	| (1 << 4) | (1 << 1),

    getBCHTypeInfo : function(data) {
	    var d = data << 10;
	    while (QRUtil.getBCHDigit(d) - QRUtil.getBCHDigit(QRUtil.G15) >= 0) {
		    d ^= (QRUtil.G15 << (QRUtil.getBCHDigit(d) - QRUtil.getBCHDigit(QRUtil.G15) ) ); 	
	    }
	    return ( (data << 10) | d) ^ QRUtil.G15_MASK;
    },

    getBCHTypeNumber : function(data) {
	    var d = data << 12;
	    while (QRUtil.getBCHDigit(d) - QRUtil.getBCHDigit(QRUtil.G18) >= 0) {
		    d ^= (QRUtil.G18 << (QRUtil.getBCHDigit(d) - QRUtil.getBCHDigit(QRUtil.G18) ) ); 	
	    }
	    return (data << 12) | d;
    },

    getBCHDigit : function(data) {

	    var digit = 0;

	    while (data != 0) {
		    digit++;
		    data >>>= 1;
	    }

	    return digit;
    },

    getPatternPosition : function(typeNumber) {
	    return QRUtil.PATTERN_POSITION_TABLE[typeNumber - 1];
    },

    getMask : function(maskPattern, i, j) {
	    
	    switch (maskPattern) {
		    
	    case QRMaskPattern.PATTERN000 : return (i + j) % 2 == 0;
	    case QRMaskPattern.PATTERN001 : return i % 2 == 0;
	    case QRMaskPattern.PATTERN010 : return j % 3 == 0;
	    case QRMaskPattern.PATTERN011 : return (i + j) % 3 == 0;
	    case QRMaskPattern.PATTERN100 : return (Math.floor(i / 2) + Math.floor(j / 3) ) % 2 == 0;
	    case QRMaskPattern.PATTERN101 : return (i * j) % 2 + (i * j) % 3 == 0;
	    case QRMaskPattern.PATTERN110 : return ( (i * j) % 2 + (i * j) % 3) % 2 == 0;
	    case QRMaskPattern.PATTERN111 : return ( (i * j) % 3 + (i + j) % 2) % 2 == 0;

	    default :
		    throw new Error("bad maskPattern:" + maskPattern);
	    }
    },

    getErrorCorrectPolynomial : function(errorCorrectLength) {

	    var a = new QRPolynomial([1], 0);

	    for (var i = 0; i < errorCorrectLength; i++) {
		    a = a.multiply(new QRPolynomial([1, QRMath.gexp(i)], 0) );
	    }

	    return a;
    },

    getLengthInBits : function(mode, type) {

	    if (1 <= type && type < 10) {

		    // 1 - 9

		    switch(mode) {
		    case QRMode.MODE_NUMBER 	: return 10;
		    case QRMode.MODE_ALPHA_NUM 	: return 9;
		    case QRMode.MODE_8BIT_BYTE	: return 8;
		    case QRMode.MODE_KANJI  	: return 8;
		    default :
			    throw new Error("mode:" + mode);
		    }

	    } else if (type < 27) {

		    // 10 - 26

		    switch(mode) {
		    case QRMode.MODE_NUMBER 	: return 12;
		    case QRMode.MODE_ALPHA_NUM 	: return 11;
		    case QRMode.MODE_8BIT_BYTE	: return 16;
		    case QRMode.MODE_KANJI  	: return 10;
		    default :
			    throw new Error("mode:" + mode);
		    }

	    } else if (type < 41) {

		    // 27 - 40

		    switch(mode) {
		    case QRMode.MODE_NUMBER 	: return 14;
		    case QRMode.MODE_ALPHA_NUM	: return 13;
		    case QRMode.MODE_8BIT_BYTE	: return 16;
		    case QRMode.MODE_KANJI  	: return 12;
		    default :
			    throw new Error("mode:" + mode);
		    }

	    } else {
		    throw new Error("type:" + type);
	    }
    },

    getLostPoint : function(qrCode) {
	    
	    var moduleCount = qrCode.getModuleCount();
	    
	    var lostPoint = 0;
	    
	    // LEVEL1
	    
	    for (var row = 0; row < moduleCount; row++) {

		    for (var col = 0; col < moduleCount; col++) {

			    var sameCount = 0;
			    var dark = qrCode.isDark(row, col);

				for (var r = -1; r <= 1; r++) {

				    if (row + r < 0 || moduleCount <= row + r) {
					    continue;
				    }

				    for (var c = -1; c <= 1; c++) {

					    if (col + c < 0 || moduleCount <= col + c) {
						    continue;
					    }

					    if (r == 0 && c == 0) {
						    continue;
					    }

					    if (dark == qrCode.isDark(row + r, col + c) ) {
						    sameCount++;
					    }
				    }
			    }

			    if (sameCount > 5) {
				    lostPoint += (3 + sameCount - 5);
			    }
		    }
	    }

	    // LEVEL2

	    for (var row = 0; row < moduleCount - 1; row++) {
		    for (var col = 0; col < moduleCount - 1; col++) {
			    var count = 0;
			    if (qrCode.isDark(row,     col    ) ) count++;
			    if (qrCode.isDark(row + 1, col    ) ) count++;
			    if (qrCode.isDark(row,     col + 1) ) count++;
			    if (qrCode.isDark(row + 1, col + 1) ) count++;
			    if (count == 0 || count == 4) {
				    lostPoint += 3;
			    }
		    }
	    }

	    // LEVEL3

	    for (var row = 0; row < moduleCount; row++) {
		    for (var col = 0; col < moduleCount - 6; col++) {
			    if (qrCode.isDark(row, col)
					    && !qrCode.isDark(row, col + 1)
					    &&  qrCode.isDark(row, col + 2)
					    &&  qrCode.isDark(row, col + 3)
					    &&  qrCode.isDark(row, col + 4)
					    && !qrCode.isDark(row, col + 5)
					    &&  qrCode.isDark(row, col + 6) ) {
				    lostPoint += 40;
			    }
		    }
	    }

	    for (var col = 0; col < moduleCount; col++) {
		    for (var row = 0; row < moduleCount - 6; row++) {
			    if (qrCode.isDark(row, col)
					    && !qrCode.isDark(row + 1, col)
					    &&  qrCode.isDark(row + 2, col)
					    &&  qrCode.isDark(row + 3, col)
					    &&  qrCode.isDark(row + 4, col)
					    && !qrCode.isDark(row + 5, col)
					    &&  qrCode.isDark(row + 6, col) ) {
				    lostPoint += 40;
			    }
		    }
	    }

	    // LEVEL4
	    
	    var darkCount = 0;

	    for (var col = 0; col < moduleCount; col++) {
		    for (var row = 0; row < moduleCount; row++) {
			    if (qrCode.isDark(row, col) ) {
				    darkCount++;
			    }
		    }
	    }
	    
	    var ratio = Math.abs(100 * darkCount / moduleCount / moduleCount - 50) / 5;
	    lostPoint += ratio * 10;

	    return lostPoint;		
    }

};


//---------------------------------------------------------------------
// QRMath
//---------------------------------------------------------------------

var QRMath = {

	glog : function(n) {
	
		if (n < 1) {
			throw new Error("glog(" + n + ")");
		}
		
		return QRMath.LOG_TABLE[n];
	},
	
	gexp : function(n) {
	
		while (n < 0) {
			n += 255;
		}
	
		while (n >= 256) {
			n -= 255;
		}
	
		return QRMath.EXP_TABLE[n];
	},
	
	EXP_TABLE : new Array(256),
	
	LOG_TABLE : new Array(256)

};
	
for (var i = 0; i < 8; i++) {
	QRMath.EXP_TABLE[i] = 1 << i;
}
for (var i = 8; i < 256; i++) {
	QRMath.EXP_TABLE[i] = QRMath.EXP_TABLE[i - 4]
		^ QRMath.EXP_TABLE[i - 5]
		^ QRMath.EXP_TABLE[i - 6]
		^ QRMath.EXP_TABLE[i - 8];
}
for (var i = 0; i < 255; i++) {
	QRMath.LOG_TABLE[QRMath.EXP_TABLE[i] ] = i;
}

//---------------------------------------------------------------------
// QRPolynomial
//---------------------------------------------------------------------

function QRPolynomial(num, shift) {

	if (num.length == undefined) {
		throw new Error(num.length + "/" + shift);
	}

	var offset = 0;

	while (offset < num.length && num[offset] == 0) {
		offset++;
	}

	this.num = new Array(num.length - offset + shift);
	for (var i = 0; i < num.length - offset; i++) {
		this.num[i] = num[i + offset];
	}
}

QRPolynomial.prototype = {

	get : function(index) {
		return this.num[index];
	},
	
	getLength : function() {
		return this.num.length;
	},
	
	multiply : function(e) {
	
		var num = new Array(this.getLength() + e.getLength() - 1);
	
		for (var i = 0; i < this.getLength(); i++) {
			for (var j = 0; j < e.getLength(); j++) {
				num[i + j] ^= QRMath.gexp(QRMath.glog(this.get(i) ) + QRMath.glog(e.get(j) ) );
			}
		}
	
		return new QRPolynomial(num, 0);
	},
	
	mod : function(e) {
	
		if (this.getLength() - e.getLength() < 0) {
			return this;
		}
	
		var ratio = QRMath.glog(this.get(0) ) - QRMath.glog(e.get(0) );
	
		var num = new Array(this.getLength() );
		
		for (var i = 0; i < this.getLength(); i++) {
			num[i] = this.get(i);
		}
		
		for (var i = 0; i < e.getLength(); i++) {
			num[i] ^= QRMath.gexp(QRMath.glog(e.get(i) ) + ratio);
		}
	
		// recursive call
		return new QRPolynomial(num, 0).mod(e);
	}
};

//---------------------------------------------------------------------
// QRRSBlock
//---------------------------------------------------------------------

function QRRSBlock(totalCount, dataCount) {
	this.totalCount = totalCount;
	this.dataCount  = dataCount;
}

QRRSBlock.RS_BLOCK_TABLE = [

	// L
	// M
	// Q
	// H

	// 1
	[1, 26, 19],
	[1, 26, 16],
	[1, 26, 13],
	[1, 26, 9],
	
	// 2
	[1, 44, 34],
	[1, 44, 28],
	[1, 44, 22],
	[1, 44, 16],

	// 3
	[1, 70, 55],
	[1, 70, 44],
	[2, 35, 17],
	[2, 35, 13],

	// 4		
	[1, 100, 80],
	[2, 50, 32],
	[2, 50, 24],
	[4, 25, 9],
	
	// 5
	[1, 134, 108],
	[2, 67, 43],
	[2, 33, 15, 2, 34, 16],
	[2, 33, 11, 2, 34, 12],
	
	// 6
	[2, 86, 68],
	[4, 43, 27],
	[4, 43, 19],
	[4, 43, 15],
	
	// 7		
	[2, 98, 78],
	[4, 49, 31],
	[2, 32, 14, 4, 33, 15],
	[4, 39, 13, 1, 40, 14],
	
	// 8
	[2, 121, 97],
	[2, 60, 38, 2, 61, 39],
	[4, 40, 18, 2, 41, 19],
	[4, 40, 14, 2, 41, 15],
	
	// 9
	[2, 146, 116],
	[3, 58, 36, 2, 59, 37],
	[4, 36, 16, 4, 37, 17],
	[4, 36, 12, 4, 37, 13],
	
	// 10		
	[2, 86, 68, 2, 87, 69],
	[4, 69, 43, 1, 70, 44],
	[6, 43, 19, 2, 44, 20],
	[6, 43, 15, 2, 44, 16],

	// 11
	[4, 101, 81],
	[1, 80, 50, 4, 81, 51],
	[4, 50, 22, 4, 51, 23],
	[3, 36, 12, 8, 37, 13],

	// 12
	[2, 116, 92, 2, 117, 93],
	[6, 58, 36, 2, 59, 37],
	[4, 46, 20, 6, 47, 21],
	[7, 42, 14, 4, 43, 15],

	// 13
	[4, 133, 107],
	[8, 59, 37, 1, 60, 38],
	[8, 44, 20, 4, 45, 21],
	[12, 33, 11, 4, 34, 12],

	// 14
	[3, 145, 115, 1, 146, 116],
	[4, 64, 40, 5, 65, 41],
	[11, 36, 16, 5, 37, 17],
	[11, 36, 12, 5, 37, 13],

	// 15
	[5, 109, 87, 1, 110, 88],
	[5, 65, 41, 5, 66, 42],
	[5, 54, 24, 7, 55, 25],
	[11, 36, 12],

	// 16
	[5, 122, 98, 1, 123, 99],
	[7, 73, 45, 3, 74, 46],
	[15, 43, 19, 2, 44, 20],
	[3, 45, 15, 13, 46, 16],

	// 17
	[1, 135, 107, 5, 136, 108],
	[10, 74, 46, 1, 75, 47],
	[1, 50, 22, 15, 51, 23],
	[2, 42, 14, 17, 43, 15],

	// 18
	[5, 150, 120, 1, 151, 121],
	[9, 69, 43, 4, 70, 44],
	[17, 50, 22, 1, 51, 23],
	[2, 42, 14, 19, 43, 15],

	// 19
	[3, 141, 113, 4, 142, 114],
	[3, 70, 44, 11, 71, 45],
	[17, 47, 21, 4, 48, 22],
	[9, 39, 13, 16, 40, 14],

	// 20
	[3, 135, 107, 5, 136, 108],
	[3, 67, 41, 13, 68, 42],
	[15, 54, 24, 5, 55, 25],
	[15, 43, 15, 10, 44, 16],

	// 21
	[4, 144, 116, 4, 145, 117],
	[17, 68, 42],
	[17, 50, 22, 6, 51, 23],
	[19, 46, 16, 6, 47, 17],

	// 22
	[2, 139, 111, 7, 140, 112],
	[17, 74, 46],
	[7, 54, 24, 16, 55, 25],
	[34, 37, 13],

	// 23
	[4, 151, 121, 5, 152, 122],
	[4, 75, 47, 14, 76, 48],
	[11, 54, 24, 14, 55, 25],
	[16, 45, 15, 14, 46, 16],

	// 24
	[6, 147, 117, 4, 148, 118],
	[6, 73, 45, 14, 74, 46],
	[11, 54, 24, 16, 55, 25],
	[30, 46, 16, 2, 47, 17],

	// 25
	[8, 132, 106, 4, 133, 107],
	[8, 75, 47, 13, 76, 48],
	[7, 54, 24, 22, 55, 25],
	[22, 45, 15, 13, 46, 16],

	// 26
	[10, 142, 114, 2, 143, 115],
	[19, 74, 46, 4, 75, 47],
	[28, 50, 22, 6, 51, 23],
	[33, 46, 16, 4, 47, 17],

	// 27
	[8, 152, 122, 4, 153, 123],
	[22, 73, 45, 3, 74, 46],
	[8, 53, 23, 26, 54, 24],
	[12, 45, 15, 28, 46, 16],

	// 28
	[3, 147, 117, 10, 148, 118],
	[3, 73, 45, 23, 74, 46],
	[4, 54, 24, 31, 55, 25],
	[11, 45, 15, 31, 46, 16],

	// 29
	[7, 146, 116, 7, 147, 117],
	[21, 73, 45, 7, 74, 46],
	[1, 53, 23, 37, 54, 24],
	[19, 45, 15, 26, 46, 16],

	// 30
	[5, 145, 115, 10, 146, 116],
	[19, 75, 47, 10, 76, 48],
	[15, 54, 24, 25, 55, 25],
	[23, 45, 15, 25, 46, 16],

	// 31
	[13, 145, 115, 3, 146, 116],
	[2, 74, 46, 29, 75, 47],
	[42, 54, 24, 1, 55, 25],
	[23, 45, 15, 28, 46, 16],

	// 32
	[17, 145, 115],
	[10, 74, 46, 23, 75, 47],
	[10, 54, 24, 35, 55, 25],
	[19, 45, 15, 35, 46, 16],

	// 33
	[17, 145, 115, 1, 146, 116],
	[14, 74, 46, 21, 75, 47],
	[29, 54, 24, 19, 55, 25],
	[11, 45, 15, 46, 46, 16],

	// 34
	[13, 145, 115, 6, 146, 116],
	[14, 74, 46, 23, 75, 47],
	[44, 54, 24, 7, 55, 25],
	[59, 46, 16, 1, 47, 17],

	// 35
	[12, 151, 121, 7, 152, 122],
	[12, 75, 47, 26, 76, 48],
	[39, 54, 24, 14, 55, 25],
	[22, 45, 15, 41, 46, 16],

	// 36
	[6, 151, 121, 14, 152, 122],
	[6, 75, 47, 34, 76, 48],
	[46, 54, 24, 10, 55, 25],
	[2, 45, 15, 64, 46, 16],

	// 37
	[17, 152, 122, 4, 153, 123],
	[29, 74, 46, 14, 75, 47],
	[49, 54, 24, 10, 55, 25],
	[24, 45, 15, 46, 46, 16],

	// 38
	[4, 152, 122, 18, 153, 123],
	[13, 74, 46, 32, 75, 47],
	[48, 54, 24, 14, 55, 25],
	[42, 45, 15, 32, 46, 16],

	// 39
	[20, 147, 117, 4, 148, 118],
	[40, 75, 47, 7, 76, 48],
	[43, 54, 24, 22, 55, 25],
	[10, 45, 15, 67, 46, 16],

	// 40
	[19, 148, 118, 6, 149, 119],
	[18, 75, 47, 31, 76, 48],
	[34, 54, 24, 34, 55, 25],
	[20, 45, 15, 61, 46, 16]
];

QRRSBlock.getRSBlocks = function(typeNumber, errorCorrectLevel) {
	
	var rsBlock = QRRSBlock.getRsBlockTable(typeNumber, errorCorrectLevel);
	
	if (rsBlock == undefined) {
		throw new Error("bad rs block @ typeNumber:" + typeNumber + "/errorCorrectLevel:" + errorCorrectLevel);
	}

	var length = rsBlock.length / 3;
	
	var list = new Array();
	
	for (var i = 0; i < length; i++) {

		var count = rsBlock[i * 3 + 0];
		var totalCount = rsBlock[i * 3 + 1];
		var dataCount  = rsBlock[i * 3 + 2];

		for (var j = 0; j < count; j++) {
			list.push(new QRRSBlock(totalCount, dataCount) );	
		}
	}
	
	return list;
}

QRRSBlock.getRsBlockTable = function(typeNumber, errorCorrectLevel) {

	switch(errorCorrectLevel) {
	case QRErrorCorrectLevel.L :
		return QRRSBlock.RS_BLOCK_TABLE[(typeNumber - 1) * 4 + 0];
	case QRErrorCorrectLevel.M :
		return QRRSBlock.RS_BLOCK_TABLE[(typeNumber - 1) * 4 + 1];
	case QRErrorCorrectLevel.Q :
		return QRRSBlock.RS_BLOCK_TABLE[(typeNumber - 1) * 4 + 2];
	case QRErrorCorrectLevel.H :
		return QRRSBlock.RS_BLOCK_TABLE[(typeNumber - 1) * 4 + 3];
	default :
		return undefined;
	}
}

//---------------------------------------------------------------------
// QRBitBuffer
//---------------------------------------------------------------------

function QRBitBuffer() {
	this.buffer = new Array();
	this.length = 0;
}

QRBitBuffer.prototype = {

	get : function(index) {
		var bufIndex = Math.floor(index / 8);
		return ( (this.buffer[bufIndex] >>> (7 - index % 8) ) & 1) == 1;
	},
	
	put : function(num, length) {
		for (var i = 0; i < length; i++) {
			this.putBit( ( (num >>> (length - i - 1) ) & 1) == 1);
		}
	},
	
	getLengthInBits : function() {
		return this.length;
	},
	
	putBit : function(bit) {
	
		var bufIndex = Math.floor(this.length / 8);
		if (this.buffer.length <= bufIndex) {
			this.buffer.push(0);
		}
	
		if (bit) {
			this.buffer[bufIndex] |= (0x80 >>> (this.length % 8) );
		}
	
		this.length++;
	}
};
		// if options is string, 
		if( typeof options === 'string' ){
			options	= { text: options };
		}

		// set default values
		// typeNumber < 1 for automatic calculation
		options	= $.extend( {}, {
			render		: "canvas",
			width		: 256,
			height		: 256,
			typeNumber	: -1,
			correctLevel	: QRErrorCorrectLevel.H,
                        background      : "#ffffff",
                        foreground      : "#000000"
		}, options);

		var createCanvas	= function(){
			// create the qrcode itself
			var qrcode	= new QRCode(options.typeNumber, options.correctLevel);
			qrcode.addData(options.text);
			qrcode.make();

			// create canvas element
			var canvas	= document.createElement('canvas');
			canvas.width	= options.width;
			canvas.height	= options.height;
			var ctx		= canvas.getContext('2d');

			// compute tileW/tileH based on options.width/options.height
			var tileW	= options.width  / qrcode.getModuleCount();
			var tileH	= options.height / qrcode.getModuleCount();

			// draw in the canvas
			for( var row = 0; row < qrcode.getModuleCount(); row++ ){
				for( var col = 0; col < qrcode.getModuleCount(); col++ ){
					ctx.fillStyle = qrcode.isDark(row, col) ? options.foreground : options.background;
					var w = (Math.ceil((col+1)*tileW) - Math.floor(col*tileW));
					var h = (Math.ceil((row+1)*tileW) - Math.floor(row*tileW));
					ctx.fillRect(Math.round(col*tileW),Math.round(row*tileH), w, h);  
				}	
			}
			// return just built canvas
			return canvas;
		}

		// from Jon-Carlos Rivera (https://github.com/imbcmdth)
		var createTable	= function(){
			// create the qrcode itself
			var qrcode	= new QRCode(options.typeNumber, options.correctLevel);
			qrcode.addData(options.text);
			qrcode.make();
			
			// create table element
			var $table	= $('<table></table>')
				.css("width", options.width+"px")
				.css("height", options.height+"px")
				.css("border", "0px")
				.css("border-collapse", "collapse")
				.css('background-color', options.background);
		  
			// compute tileS percentage
			var tileW	= options.width / qrcode.getModuleCount();
			var tileH	= options.height / qrcode.getModuleCount();

			// draw in the table
			for(var row = 0; row < qrcode.getModuleCount(); row++ ){
				var $row = $('<tr></tr>').css('height', tileH+"px").appendTo($table);
				
				for(var col = 0; col < qrcode.getModuleCount(); col++ ){
					$('<td></td>')
						.css('width', tileW+"px")
						.css('background-color', qrcode.isDark(row, col) ? options.foreground : options.background)
						.appendTo($row);
				}	
			}
			// return just built canvas
			return $table;
		}
  

		return this.each(function(){
			var element	= options.render == "canvas" ? createCanvas() : createTable();
			$(element).appendTo(this);
		});
	};
})( jQuery );

// jQuery.XDomainRequest.js
// Author: Jason Moon - @JSONMOON
// IE8+
if ( window.XDomainRequest ) {
	jQuery.ajaxTransport(function( s ) {
		if ( s.crossDomain && s.async ) {
			if ( s.timeout ) {
				s.xdrTimeout = s.timeout;
				delete s.timeout;
			}
			var xdr;
			return {
				send: function( _, complete ) {
					function callback( status, statusText, responses, responseHeaders ) {
						xdr.onload = xdr.onerror = xdr.ontimeout = jQuery.noop;
						xdr = undefined;
						complete( status, statusText, responses, responseHeaders );
					}
					xdr = new XDomainRequest();
					xdr.onload = function() {
						callback( 200, "OK", { text: xdr.responseText }, "Content-Type: " + xdr.contentType );
					};
					xdr.onerror = function() {
						callback( 404, "Not Found" );
					};
					xdr.onprogress = jQuery.noop;
					xdr.ontimeout = function() {
						callback( 0, "timeout" );
					};
					xdr.timeout = s.xdrTimeout || Number.MAX_VALUE;
					xdr.open( s.type, s.url );
					xdr.send( ( s.hasContent && s.data ) || null );
				},
				abort: function() {
					if ( xdr ) {
						xdr.onerror = jQuery.noop;
						xdr.abort();
					}
				}
			};
		}
	});
}
/*!
 * zeroclipboard
 * The Zero Clipboard library provides an easy way to copy text to the clipboard using an invisible Adobe Flash movie, and a JavaScript interface.
 * Copyright 2012 Jon Rohan, James M. Greene, .
 * Released under the MIT license
 * http://jonrohan.github.com/ZeroClipboard/
 * v1.1.7
 */(function() {
  "use strict";
  var _getStyle = function(el, prop) {
    var y = el.style[prop];
    if (el.currentStyle) y = el.currentStyle[prop]; else if (window.getComputedStyle) y = document.defaultView.getComputedStyle(el, null).getPropertyValue(prop);
    if (y == "auto" && prop == "cursor") {
      var possiblePointers = [ "a" ];
      for (var i = 0; i < possiblePointers.length; i++) {
        if (el.tagName.toLowerCase() == possiblePointers[i]) {
          return "pointer";
        }
      }
    }
    return y;
  };
  var _elementMouseOver = function(event) {
    if (!ZeroClipboard.prototype._singleton) return;
    if (!event) {
      event = window.event;
    }
    var target;
    if (this !== window) {
      target = this;
    } else if (event.target) {
      target = event.target;
    } else if (event.srcElement) {
      target = event.srcElement;
    }
    ZeroClipboard.prototype._singleton.setCurrent(target);
  };
  var _addEventHandler = function(element, method, func) {
    if (element.addEventListener) {
      element.addEventListener(method, func, false);
    } else if (element.attachEvent) {
      element.attachEvent("on" + method, func);
    }
  };
  var _removeEventHandler = function(element, method, func) {
    if (element.removeEventListener) {
      element.removeEventListener(method, func, false);
    } else if (element.detachEvent) {
      element.detachEvent("on" + method, func);
    }
  };
  var _addClass = function(element, value) {
    if (element.addClass) {
      element.addClass(value);
      return element;
    }
    if (value && typeof value === "string") {
      var classNames = (value || "").split(/\s+/);
      if (element.nodeType === 1) {
        if (!element.className) {
          element.className = value;
        } else {
          var className = " " + element.className + " ", setClass = element.className;
          for (var c = 0, cl = classNames.length; c < cl; c++) {
            if (className.indexOf(" " + classNames[c] + " ") < 0) {
              setClass += " " + classNames[c];
            }
          }
          element.className = setClass.replace(/^\s+|\s+$/g, "");
        }
      }
    }
    return element;
  };
  var _removeClass = function(element, value) {
    if (element.removeClass) {
      element.removeClass(value);
      return element;
    }
    if (value && typeof value === "string" || value === undefined) {
      var classNames = (value || "").split(/\s+/);
      if (element.nodeType === 1 && element.className) {
        if (value) {
          var className = (" " + element.className + " ").replace(/[\n\t]/g, " ");
          for (var c = 0, cl = classNames.length; c < cl; c++) {
            className = className.replace(" " + classNames[c] + " ", " ");
          }
          element.className = className.replace(/^\s+|\s+$/g, "");
        } else {
          element.className = "";
        }
      }
    }
    return element;
  };
  var _getDOMObjectPosition = function(obj) {
    var info = {
      left: 0,
      top: 0,
      width: obj.width || obj.offsetWidth || 0,
      height: obj.height || obj.offsetHeight || 0,
      zIndex: 9999
    };
    var zi = _getStyle(obj, "zIndex");
    if (zi && zi != "auto") {
      info.zIndex = parseInt(zi, 10);
    }
    while (obj) {
      var borderLeftWidth = parseInt(_getStyle(obj, "borderLeftWidth"), 10);
      var borderTopWidth = parseInt(_getStyle(obj, "borderTopWidth"), 10);
      info.left += isNaN(obj.offsetLeft) ? 0 : obj.offsetLeft;
      info.left += isNaN(borderLeftWidth) ? 0 : borderLeftWidth;
      info.top += isNaN(obj.offsetTop) ? 0 : obj.offsetTop;
      info.top += isNaN(borderTopWidth) ? 0 : borderTopWidth;
      obj = obj.offsetParent;
    }
    return info;
  };
  var _noCache = function(path) {
    return (path.indexOf("?") >= 0 ? "&" : "?") + "nocache=" + (new Date).getTime();
  };
  var _vars = function(options) {
    var str = [];
    if (options.trustedDomains) {
      if (typeof options.trustedDomains === "string") {
        str.push("trustedDomain=" + options.trustedDomains);
      } else {
        str.push("trustedDomain=" + options.trustedDomains.join(","));
      }
    }
    return str.join("&");
  };
  var _inArray = function(elem, array) {
    if (array.indexOf) {
      return array.indexOf(elem);
    }
    for (var i = 0, length = array.length; i < length; i++) {
      if (array[i] === elem) {
        return i;
      }
    }
    return -1;
  };
  var _prepGlue = function(elements) {
    if (typeof elements === "string") throw new TypeError("ZeroClipboard doesn't accept query strings.");
    if (!elements.length) return [ elements ];
    return elements;
  };
  var ZeroClipboard = function(elements, options) {
    if (elements) (ZeroClipboard.prototype._singleton || this).glue(elements);
    if (ZeroClipboard.prototype._singleton) return ZeroClipboard.prototype._singleton;
    ZeroClipboard.prototype._singleton = this;
    this.options = {};
    for (var kd in _defaults) this.options[kd] = _defaults[kd];
    for (var ko in options) this.options[ko] = options[ko];
    this.handlers = {};
    if (ZeroClipboard.detectFlashSupport()) _bridge();
  };
  var currentElement, gluedElements = [];
  ZeroClipboard.prototype.setCurrent = function(element) {
    currentElement = element;
    this.reposition();
    if (element.getAttribute("title")) {
      this.setTitle(element.getAttribute("title"));
    }
    this.setHandCursor(_getStyle(element, "cursor") == "pointer");
  };
  ZeroClipboard.prototype.setText = function(newText) {
    if (newText && newText !== "") {
      this.options.text = newText;
      if (this.ready()) this.flashBridge.setText(newText);
    }
  };
  ZeroClipboard.prototype.setTitle = function(newTitle) {
    if (newTitle && newTitle !== "") this.htmlBridge.setAttribute("title", newTitle);
  };
  ZeroClipboard.prototype.setSize = function(width, height) {
    if (this.ready()) this.flashBridge.setSize(width, height);
  };
  ZeroClipboard.prototype.setHandCursor = function(enabled) {
    if (this.ready()) this.flashBridge.setHandCursor(enabled);
  };
  ZeroClipboard.version = "1.1.7";
  var _defaults = {
    moviePath: "ZeroClipboard.swf",
    trustedDomains: null,
    text: null,
    hoverClass: "zeroclipboard-is-hover",
    activeClass: "zeroclipboard-is-active",
    allowScriptAccess: "sameDomain"
  };
  ZeroClipboard.setDefaults = function(options) {
    for (var ko in options) _defaults[ko] = options[ko];
  };
  ZeroClipboard.destroy = function() {
    ZeroClipboard.prototype._singleton.unglue(gluedElements);
    var bridge = ZeroClipboard.prototype._singleton.htmlBridge;
    bridge.parentNode.removeChild(bridge);
    delete ZeroClipboard.prototype._singleton;
  };
  ZeroClipboard.detectFlashSupport = function() {
    var hasFlash = false;
    try {
      if (new ActiveXObject("ShockwaveFlash.ShockwaveFlash")) {
        hasFlash = true;
      }
    } catch (error) {
      if (navigator.mimeTypes["application/x-shockwave-flash"]) {
        hasFlash = true;
      }
    }
    return hasFlash;
  };
  var _bridge = function() {
    var client = ZeroClipboard.prototype._singleton;
    var container = document.getElementById("global-zeroclipboard-html-bridge");
    if (!container) {
      var html = '      <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" id="global-zeroclipboard-flash-bridge" width="100%" height="100%">         <param name="movie" value="' + client.options.moviePath + _noCache(client.options.moviePath) + '"/>         <param name="allowScriptAccess" value="' + client.options.allowScriptAccess + '"/>         <param name="scale" value="exactfit"/>         <param name="loop" value="false"/>         <param name="menu" value="false"/>         <param name="quality" value="best" />         <param name="bgcolor" value="#ffffff"/>         <param name="wmode" value="transparent"/>         <param name="flashvars" value="' + _vars(client.options) + '"/>         <embed src="' + client.options.moviePath + _noCache(client.options.moviePath) + '"           loop="false" menu="false"           quality="best" bgcolor="#ffffff"           width="100%" height="100%"           name="global-zeroclipboard-flash-bridge"           allowScriptAccess="always"           allowFullScreen="false"           type="application/x-shockwave-flash"           wmode="transparent"           pluginspage="http://www.macromedia.com/go/getflashplayer"           flashvars="' + _vars(client.options) + '"           scale="exactfit">         </embed>       </object>';
      container = document.createElement("div");
      container.id = "global-zeroclipboard-html-bridge";
      container.setAttribute("class", "global-zeroclipboard-container");
      container.setAttribute("data-clipboard-ready", false);
      container.style.position = "absolute";
      container.style.left = "-9999px";
      container.style.top = "-9999px";
      container.style.width = "15px";
      container.style.height = "15px";
      container.style.zIndex = "9999";
      container.innerHTML = html;
      document.body.appendChild(container);
    }
    client.htmlBridge = container;
    client.flashBridge = document["global-zeroclipboard-flash-bridge"] || container.children[0].lastElementChild;
  };
  ZeroClipboard.prototype.resetBridge = function() {
    this.htmlBridge.style.left = "-9999px";
    this.htmlBridge.style.top = "-9999px";
    this.htmlBridge.removeAttribute("title");
    this.htmlBridge.removeAttribute("data-clipboard-text");
    _removeClass(currentElement, this.options.activeClass);
    currentElement = null;
    this.options.text = null;
  };
  ZeroClipboard.prototype.ready = function() {
    var ready = this.htmlBridge.getAttribute("data-clipboard-ready");
    return ready === "true" || ready === true;
  };
  ZeroClipboard.prototype.reposition = function() {
    if (!currentElement) return false;
    var pos = _getDOMObjectPosition(currentElement);
    this.htmlBridge.style.top = pos.top + "px";
    this.htmlBridge.style.left = pos.left + "px";
    this.htmlBridge.style.width = pos.width + "px";
    this.htmlBridge.style.height = pos.height + "px";
    this.htmlBridge.style.zIndex = pos.zIndex + 1;
    this.setSize(pos.width, pos.height);
  };
  ZeroClipboard.dispatch = function(eventName, args) {
    ZeroClipboard.prototype._singleton.receiveEvent(eventName, args);
  };
  ZeroClipboard.prototype.on = function(eventName, func) {
    var events = eventName.toString().split(/\s/g);
    for (var i = 0; i < events.length; i++) {
      eventName = events[i].toLowerCase().replace(/^on/, "");
      if (!this.handlers[eventName]) this.handlers[eventName] = func;
    }
    if (this.handlers.noflash && !ZeroClipboard.detectFlashSupport()) {
      this.receiveEvent("onNoFlash", null);
    }
  };
  ZeroClipboard.prototype.addEventListener = ZeroClipboard.prototype.on;
  ZeroClipboard.prototype.off = function(eventName, func) {
    var events = eventName.toString().split(/\s/g);
    for (var i = 0; i < events.length; i++) {
      eventName = events[i].toLowerCase().replace(/^on/, "");
      for (var event in this.handlers) {
        if (event === eventName && this.handlers[event] === func) {
          delete this.handlers[event];
        }
      }
    }
  };
  ZeroClipboard.prototype.removeEventListener = ZeroClipboard.prototype.off;
  ZeroClipboard.prototype.receiveEvent = function(eventName, args) {
    eventName = eventName.toString().toLowerCase().replace(/^on/, "");
    var element = currentElement;
    switch (eventName) {
     case "load":
      if (args && parseFloat(args.flashVersion.replace(",", ".").replace(/[^0-9\.]/gi, "")) < 10) {
        this.receiveEvent("onWrongFlash", {
          flashVersion: args.flashVersion
        });
        return;
      }
      this.htmlBridge.setAttribute("data-clipboard-ready", true);
      break;
     case "mouseover":
      _addClass(element, this.options.hoverClass);
      break;
     case "mouseout":
      _removeClass(element, this.options.hoverClass);
      this.resetBridge();
      break;
     case "mousedown":
      _addClass(element, this.options.activeClass);
      break;
     case "mouseup":
      _removeClass(element, this.options.activeClass);
      break;
     case "datarequested":
      var targetId = element.getAttribute("data-clipboard-target"), targetEl = !targetId ? null : document.getElementById(targetId);
      if (targetEl) {
        var textContent = targetEl.value || targetEl.textContent || targetEl.innerText;
        if (textContent) this.setText(textContent);
      } else {
        var defaultText = element.getAttribute("data-clipboard-text");
        if (defaultText) this.setText(defaultText);
      }
      break;
     case "complete":
      this.options.text = null;
      break;
    }
    if (this.handlers[eventName]) {
      var func = this.handlers[eventName];
      if (typeof func == "function") {
        func.call(element, this, args);
      } else if (typeof func == "string") {
        window[func].call(element, this, args);
      }
    }
  };
  ZeroClipboard.prototype.glue = function(elements) {
    elements = _prepGlue(elements);
    for (var i = 0; i < elements.length; i++) {
      if (_inArray(elements[i], gluedElements) == -1) {
        gluedElements.push(elements[i]);
        _addEventHandler(elements[i], "mouseover", _elementMouseOver);
      }
    }
  };
  ZeroClipboard.prototype.unglue = function(elements) {
    elements = _prepGlue(elements);
    for (var i = 0; i < elements.length; i++) {
      _removeEventHandler(elements[i], "mouseover", _elementMouseOver);
      var arrayIndex = _inArray(elements[i], gluedElements);
      if (arrayIndex != -1) gluedElements.splice(arrayIndex, 1);
    }
  };
  if (typeof module !== "undefined") {
    module.exports = ZeroClipboard;
  } else if (typeof define === "function" && define.amd) {
    define(function() {
      return ZeroClipboard;
    });
  } else {
    window.ZeroClipboard = ZeroClipboard;
  }
})();
(function(kmc) {

	/* 
	 * TODO:
	 * Use ng-view for preview template
	 * Use filters for delivery 
	 */

	var Preview = kmc.Preview || {};

	// Preview Partner Defaults
	kmc.vars.previewDefaults = {
		showAdvancedOptions: false,
		includeKalturaLinks: (!kmc.vars.ignore_seo_links),
		includeSeoMetadata: (!kmc.vars.ignore_entry_seo),
		deliveryType: kmc.vars.default_delivery_type,
		embedType: kmc.vars.default_embed_code_type,
		secureEmbed: kmc.vars.embed_code_protocol_https
	};

	// Check for current protocol and update secureEmbed
	if(window.location.protocol == 'https:') {
		kmc.vars.previewDefaults.secureEmbed = true;
	}

	Preview.storageName = 'previewDefaults';
	Preview.el = '#previewModal';
	Preview.iframeContainer = 'previewIframe';

	// We use this flag to ignore all change evnets when we initilize the preview ( on page start up )
	// We will set that to false once Preview is opened.
	Preview.ignoreChangeEvents = true;

	// Set generator
	Preview.getGenerator = function() {
		if(!this.generator) {
			this.generator = new kEmbedCodeGenerator({
				host: kmc.vars.embed_host,
				securedHost: kmc.vars.embed_host_https,
				partnerId: kmc.vars.partner_id,
				includeKalturaLinks: kmc.vars.previewDefaults.includeKalturaLinks
			});
		}
		return this.generator;
	};

	Preview.clipboard = new ZeroClipboard($('.copy-code'), {
		moviePath: "/lib/flash/ZeroClipboard.swf",
		trustedDomains: ['*'],
		allowScriptAccess: "always"
	});

	Preview.clipboard.on('complete', function() {
		var $this = $(this);
		// Mark embed code as selected
		$('#' + $this.data('clipboard-target')).select();
		// Close preview
		if($this.data('close') === true) {
			Preview.closeModal(Preview.el);
		}
	});

	Preview.objectToArray = function(obj) {
		var arr = [];
		for(var key in obj) {
			obj[key].id = key;
			arr.push(obj[key]);
		}
		return arr;
	};

	Preview.getObjectById = function(id, arr) {
		var result = $.grep(arr, function(e) {
			return e.id == id;
		});
		return(result.length) ? result[0] : false;
	};

	Preview.getDefault = function(setting) {
		var defaults = localStorage.getItem(Preview.storageName);
		if(defaults) {
			defaults = JSON.parse(defaults);
		} else {
			defaults = kmc.vars.previewDefaults;
		}
		if(defaults[setting] !== undefined) {
			return defaults[setting];
		}
		return null;
	};

	Preview.savePreviewState = function() {
		var previewService = this.Service;
		var defaults = {
			embedType: previewService.get('embedType'),
			secureEmbed: previewService.get('secureEmbed'),
			includeSeoMetadata: previewService.get('includeSeo'),
			deliveryType: previewService.get('deliveryType').id,
			showAdvancedOptions: previewService.get('showAdvancedOptions')
		};
		// Save defaults to localStorage
		localStorage.setItem(Preview.storageName, JSON.stringify(defaults));
	};

	Preview.getDeliveryTypeFlashVars = function(deliveryType) {
		// Not delivery type, exit
		if(!deliveryType) return {};

		// Get original delivery type flashvars
		var originalFlashVars = (deliveryType.flashvars) ? deliveryType.flashvars : {};

		// Clone flashVars object
		var newFlashVars = $.extend({}, originalFlashVars);

		// Add streamerType and mediaProtocol flashVars
		if(deliveryType.streamerType)
			newFlashVars.streamerType = deliveryType.streamerType;

		if(deliveryType.mediaProtocol)
			newFlashVars.mediaProtocol = deliveryType.mediaProtocol;

		// Return the new Flashvars object
		return newFlashVars;
	};

	Preview.getPreviewTitle = function(options) {
		if(options.entryMeta && options.entryMeta.name) {
			return 'Embedding: ' + options.entryMeta.name;
		}
		if(options.playlistName) {
			return 'Playlist: ' + options.playlistName;
		}
		if(options.playerOnly) {
			return 'Player Name:' + options.name;
		}
	};

	Preview.openPreviewEmbed = function(options, previewService) {

		var _this = this;
		var el = _this.el;

		// Enable preview events
		this.ignoreChangeEvents = false;

		var defaults = {
			entryId: null,
			entryMeta: {},
			playlistId: null,
			playlistName: null,
			previewOnly: false,
			liveBitrates: null,
			playerOnly: false,
			uiConfId: null,
			name: null
		};

		options = $.extend({}, defaults, options);
		previewService.disableEvents();
		// In case of live entry preview, set delivery type to auto
		if( options.liveBitrates ) {
			previewService.setDeliveryType('auto');
		}
		// Update our players
		previewService.updatePlayers(options);
		previewService.enableEvents();
		// Set options
		previewService.set(options);

		var title = this.getPreviewTitle(options);

		var $previewModal = $(el);
		$previewModal.find(".title h2").text(title).attr('title', title);
		$previewModal.find(".close").unbind('click').click(function() {
			_this.closeModal(el);
		});

		// Show our preview modal
		var modalHeight = $('body').height() - 173;
		$previewModal.find('.content').height(modalHeight);
		kmc.layout.modal.show(el, false);
	};

	Preview.closeModal = function(el) {
		this.savePreviewState();
		this.emptyDiv(this.iframeContainer);
		$(el).fadeOut(300, function() {
			kmc.layout.overlay.hide();
			kmc.utils.hideFlash();
		});
	};

	Preview.emptyDiv = function(divId) {
		var $targetDiv = $('#' + divId);
		var $previewIframe = $('#previewIframe iframe');
		if( $previewIframe.length ) {
			try {
				var $iframeDoc = $($previewIframe[0].contentWindow.document);
				$iframeDoc.find('#framePlayerContainer').empty();
			} catch (e) {}
		}

		if( $targetDiv.length ) {
			$targetDiv.empty();
			return $targetDiv[0];
		}
		return false;
	};

	Preview.hasIframe = function() {
		return $('#' + this.iframeContainer + ' iframe').length;
	};

	Preview.getCacheSt = function() {
		var d = new Date();
		return Math.floor(d.getTime() / 1000) + (15 * 60); // start caching in 15 minutes
	};

	Preview.generateIframe = function(embedCode) {

		var ltIE10 = $('html').hasClass('lt-ie10');
		var style = '<style>html, body {margin: 0; padding: 0; width: 100%; height: 100%; } #framePlayerContainer {margin: 0 auto; padding-top: 20px; text-align: center; } object, div { margin: 0 auto; }</style>';
		var container = this.emptyDiv(this.iframeContainer);
		var iframe = document.createElement('iframe');
		// Reset iframe style
		iframe.frameborder = "0";
		iframe.frameBorder = "0";
		iframe.marginheight="0";
		iframe.marginwidth="0";
		iframe.frameborder="0";

		container.appendChild(iframe);

		if(ltIE10) {
			iframe.src = this.getPreviewUrl(this.Service, true);
		} else {
			var newDoc = iframe.contentDocument;
			newDoc.open();
			newDoc.write('<!doctype html><html><head>' + style + '</head><body><div id="framePlayerContainer">' + embedCode + '</div></body></html>');
			newDoc.close();
		}
	};

	Preview.getEmbedProtocol = function(previewService, previewPlayer) {
		if(previewPlayer === true) {
			return location.protocol.substring(0, location.protocol.length - 1); // Get host protocol
		}
		return (previewService.get('secureEmbed')) ? 'https' : 'http';
	};

	Preview.getEmbedFlashVars = function(previewService, addKs) {
		var protocol = this.getEmbedProtocol(previewService, addKs);
		var player = previewService.get('player');
		var flashVars = this.getDeliveryTypeFlashVars(previewService.get('deliveryType'));
		if(addKs === true) {
			flashVars.ks = kmc.vars.ks;
		}

		var playlistId = previewService.get('playlistId');
		if(playlistId) {
			// Use new kpl0Id flashvar for new players only
			var html5_version = kmc.functions.getVersionFromPath(player.html5Url);
			var kdpVersionCheck = kmc.functions.versionIsAtLeast(kmc.vars.min_kdp_version_for_playlist_api_v3, player.swf_version);
			var html5VersionCheck = kmc.functions.versionIsAtLeast(kmc.vars.min_html5_version_for_playlist_api_v3, html5_version);
			if( kdpVersionCheck && html5VersionCheck ) {
				flashVars['playlistAPI.kpl0Id'] = playlistId;
			} else {
				flashVars['playlistAPI.autoInsert'] = 'true';
				flashVars['playlistAPI.kpl0Name'] = previewService.get('playlistName');
				flashVars['playlistAPI.kpl0Url'] = protocol + '://' + kmc.vars.api_host + '/index.php/partnerservices2/executeplaylist?' + 'partner_id=' + kmc.vars.partner_id + '&subp_id=' + kmc.vars.partner_id + '00' + '&format=8&ks={ks}&playlist_id=' + playlistId;
			}
		}
		return flashVars;	
	};

	Preview.getEmbedCode = function(previewService, previewPlayer) {
		var player = previewService.get('player');
		if(!player || !previewService.get('embedType')) {
			return '';
		}
		var cacheSt = this.getCacheSt();
		var params = {
			protocol: this.getEmbedProtocol(previewService, previewPlayer),
			embedType: previewService.get('embedType'),
			uiConfId: player.id,
			width: player.width,
			height: player.height,
			entryMeta: previewService.get('entryMeta'),
			includeSeoMetadata: previewService.get('includeSeo'),
			playerId: 'kaltura_player_' + cacheSt,
			cacheSt: cacheSt,
			flashVars: this.getEmbedFlashVars(previewService, previewPlayer)
		};

		if(previewService.get('entryId')) {
			params.entryId = previewService.get('entryId');
		}

		var code = this.getGenerator().getCode(params);
		return code;
	};

	Preview.getPreviewUrl = function(previewService, framed) {
		var player = previewService.get('player');
		if(!player || !previewService.get('embedType')) {
			return '';
		}

		var protocol = this.getEmbedProtocol(previewService, framed);
		var url = protocol + '://' + kmc.vars.api_host + '/index.php/extwidget/preview';
		//var url = protocol + '://' + window.location.host + '/KMC_V2/preview.php';
		url += '/partner_id/' + kmc.vars.partner_id;
		url += '/uiconf_id/' + player.id;
		// Add entry Id
		if(previewService.get('entryId')) {
			url += '/entry_id/' + previewService.get('entryId');
		}
		url += '/embed/' + previewService.get('embedType');
		url += '?' + kmc.functions.flashVarsToUrl(this.getEmbedFlashVars(previewService, framed));
		if( framed === true ) {
			url += '&framed=true';
		}
		return url;
	};

	Preview.generateQrCode = function(url) {
		var $qrCode = $('#qrcode').empty();
		if(!url) return ;
		if($('html').hasClass('lt-ie9')) return;
		$qrCode.qrcode({
			width: 80,
			height: 80,
			text: url
		});
	};

	Preview.generateShortUrl = function(url, callback) {
		if(!url) return ;
		kmc.client.createShortURL(url, callback);
	};

	kmc.Preview = Preview;

})(window.kmc);

var kmcApp = angular.module('kmcApp', []);
kmcApp.factory('previewService', ['$rootScope', function($rootScope) {
	var previewProps = {};
	var disableCount = 0;
	return {
		get: function(key) {
			if(key === undefined) return previewProps;
			return previewProps[key];
		},
		set: function(key, value, quiet) {
			if(typeof key == 'object') {
				angular.extend(previewProps, key);
			} else {
				previewProps[key] = value;
			}
			if( !quiet && disableCount === 0 ) {
				$rootScope.$broadcast('previewChanged');
			}
		},
		enableEvents: function(){
			disableCount--;
		},
		disableEvents: function(){
			disableCount++
		},
		updatePlayers: function(options) {
			$rootScope.$broadcast('playersUpdated', options);
		},
		changePlayer: function(playerId) {
			$rootScope.$broadcast('changePlayer', playerId);
		},
		setDeliveryType: function( deliveryTypeId ) {
			$rootScope.$broadcast('changeDelivery', deliveryTypeId );
		}
	};
}]);
kmcApp.directive('showSlide', function() {
	return {
		//restrict it's use to attribute only.
		restrict: 'A',

		//set up the directive.
		link: function(scope, elem, attr) {

			//get the field to watch from the directive attribute.
			var watchField = attr.showSlide;

			//set up the watch to toggle the element.
			scope.$watch(attr.showSlide, function(v) {
				if(v && !elem.is(':visible')) {
					elem.slideDown();
				} else {
					elem.slideUp();
				}
			});
		}
	};
});

kmcApp.controller('PreviewCtrl', ['$scope', 'previewService', function($scope, previewService) {

	var draw = function() {
			if(!$scope.$$phase) {
				$scope.$apply();
			}
		};

	var Preview = kmc.Preview;
	Preview.playlistMode = false;

	Preview.Service = previewService;

	var updatePlayers = function(options) {
			options = options || {};
			var playerId = (options.uiConfId) ? options.uiConfId : undefined;
			// Exit if player not loaded
			if(!kmc.vars.playlists_list || !kmc.vars.players_list) {
				return ;
			}
			// List of players
			if(options.playlistId || options.playerOnly) {
				$scope.players = kmc.vars.playlists_list;
				if(!Preview.playlistMode) {
					Preview.playlistMode = true;
					$scope.$broadcast('changePlayer', playerId);
					return;
				}
			} else {
				$scope.players = kmc.vars.players_list;
				if(Preview.playlistMode || !$scope.player) {
					Preview.playlistMode = false;
					$scope.$broadcast('changePlayer', playerId);
					return;
				}
			}
			if(playerId){
				$scope.$broadcast('changePlayer', playerId);
			}
		};

	var setDeliveryTypes = function(player) {
			var deliveryTypes = Preview.objectToArray(kmc.vars.delivery_types);
			var defaultType = $scope.deliveryType || Preview.getDefault('deliveryType');
			var validDeliveryTypes = [];
			$.each(deliveryTypes, function() {
				if(this.minVersion && !kmc.functions.versionIsAtLeast(this.minVersion, player.swf_version)) {
					if(this.id == defaultType) {
						defaultType = null;
					}
					return true;
				}
				validDeliveryTypes.push(this);
			});
			// List of delivery types
			$scope.deliveryTypes = validDeliveryTypes;
			// Set default delivery type
			if(!defaultType) {
				defaultType = $scope.deliveryTypes[0].id;
			}
			previewService.setDeliveryType(defaultType);
		};

	var setEmbedTypes = function(player) {
			var embedTypes = Preview.objectToArray(kmc.vars.embed_code_types);
			var defaultType = $scope.embedType || Preview.getDefault('embedType');
			var validEmbedTypes = [];
			$.each(embedTypes, function() {
				// Don't add embed code that are entry only for playlists
				if(Preview.playlistMode && this.entryOnly) {
					if(this.id == defaultType) {
						defaultType = null;
					}
					return true;
				}
				// Check for library minimum version to eanble embed type
				var libVersion = kmc.functions.getVersionFromPath(player.html5Url);
				if(this.minVersion && !kmc.functions.versionIsAtLeast(this.minVersion, libVersion)) {
					if(this.id == defaultType) {
						defaultType = null;
					}
					return true;
				}
				validEmbedTypes.push(this);
			});
			// List of embed types
			$scope.embedTypes = validEmbedTypes;
			// Set default embed type
			if(!defaultType) {
				defaultType = $scope.embedTypes[0].id;
			}
			$scope.embedType = defaultType;
		};

	// Set defaults
	$scope.players = [];
	$scope.player = null;
	$scope.deliveryTypes = [];
	$scope.deliveryType = null;
	$scope.embedTypes = [];
	$scope.embedType = null;
	$scope.secureEmbed = Preview.getDefault('secureEmbed');
	$scope.includeSeo = Preview.getDefault('includeSeoMetadata');
	$scope.previewOnly = false;
	$scope.playerOnly = false;
	$scope.liveBitrates = false;
	$scope.showAdvancedOptionsStatus = Preview.getDefault('showAdvancedOptions');
	$scope.shortLinkGenerated = false;

	// Set players on update
	$scope.$on('playersUpdated', function(e, options) {
		updatePlayers(options);
	});

	$scope.$on('changePlayer', function(e, playerId) {
		playerId = ( playerId ) ? playerId : $scope.players[0].id;
		$scope.player = playerId;
		draw();
	});

	$scope.$on('changeDelivery', function(e, deliveryTypeId) {
		$scope.deliveryType = deliveryTypeId;
		draw();
	});

	$scope.showAdvancedOptions = function($event, show) {
		$event.preventDefault();
		previewService.set('showAdvancedOptions', show, true);
		$scope.showAdvancedOptionsStatus = show;
	};

	$scope.$watch('showAdvancedOptionsStatus', function() {
		Preview.clipboard.reposition();
	});

	// Listen to player change
	$scope.$watch('player', function() {
		var player = Preview.getObjectById($scope.player, $scope.players);
		if(!player) { return ; }
		previewService.disableEvents();
		setDeliveryTypes(player);
		setEmbedTypes(player);
		setTimeout(function(){
			previewService.enableEvents();
			previewService.set('player', player);
		},0);
	});
	$scope.$watch('deliveryType', function() {
		var deliveryType = Preview.getObjectById($scope.deliveryType, $scope.deliveryTypes);
		previewService.set('deliveryType', deliveryType);
	});
	$scope.$watch('embedType', function() {
		previewService.set('embedType', $scope.embedType);
	});
	$scope.$watch('secureEmbed', function() {
		previewService.set('secureEmbed', $scope.secureEmbed);
	});
	$scope.$watch('includeSeo', function() {
		previewService.set('includeSeo', $scope.includeSeo);
	});
	$scope.$watch('embedCodePreview', function() {
		Preview.generateIframe($scope.embedCodePreview);
	});
	$scope.$watch('previewOnly', function() {
		if($scope.previewOnly) {
			$scope.closeButtonText = 'Close';
		} else {
			$scope.closeButtonText = 'Copy Embed & Close';
		}
		draw();
	});
	$scope.$on('previewChanged', function() {
		if(Preview.ignoreChangeEvents) return;
		var previewUrl = Preview.getPreviewUrl(previewService);
		$scope.embedCode = Preview.getEmbedCode(previewService);
		$scope.embedCodePreview = Preview.getEmbedCode(previewService, true);
		$scope.previewOnly = previewService.get('previewOnly');
		$scope.playerOnly = previewService.get('playerOnly');
		$scope.liveBitrates = previewService.get('liveBitrates');
		draw();
		// Generate Iframe if not exist
		if(!Preview.hasIframe()) {
			Preview.generateIframe($scope.embedCodePreview);
		}
		// Update Short url
		$scope.previewUrl = 'Updating...';
		$scope.shortLinkGenerated = false;
		Preview.generateShortUrl(previewUrl, function(tinyUrl) {
			if(!tinyUrl) {
				// Set tinyUrl to fullUrl
				tinyUrl = previewUrl;
			}
			$scope.shortLinkGenerated = true;
			$scope.previewUrl = tinyUrl;
			// Generate QR Code
			Preview.generateQrCode(tinyUrl);			
			draw();
		});
	});

}]);
// Prevent the page to be framed
if(kmc.vars.allowFrame == false && top != window) { top.location = window.location; }

/* kmc and kmc.vars defined in script block in kmc4success.php */

// For debug enable to true. Debug will show information in the browser console
kmc.vars.debug = false;

// Quickstart guide (should be moved to kmc4success.php)
kmc.vars.quickstart_guide = "/content/docs/pdf/KMC_User_Manual.pdf";
kmc.vars.help_url = kmc.vars.service_url + '/kmc5help.html';

// Set base URL
kmc.vars.port = (window.location.port) ? ":" + window.location.port : "";
kmc.vars.base_host = window.location.hostname + kmc.vars.port;
kmc.vars.base_url = window.location.protocol + '//' + kmc.vars.base_host;
kmc.vars.api_host = kmc.vars.host;
kmc.vars.api_url = window.location.protocol + '//' + kmc.vars.api_host;

// Holds the minimum version for html5 & kdp with the api_v3 for playlists
kmc.vars.min_kdp_version_for_playlist_api_v3 = '3.6.15';
kmc.vars.min_html5_version_for_playlist_api_v3 = '1.7.1.3';

// Log function
kmc.log = function() {
	if( kmc.vars.debug && typeof console !='undefined' && console.log ){
		if (arguments.length == 1) {
			console.log( arguments[0] );
		} else {
			var args = Array.prototype.slice.call(arguments);  
			console.log( args[0], args.slice( 1 ) );
		}
	}	
};

kmc.functions = {

	loadSwf : function() {

		var kmc_swf_url = window.location.protocol + '//' + kmc.vars.cdn_host + '/flash/kmc/' + kmc.vars.kmc_version + '/kmc.swf';
		var flashvars = {
			// kmc configuration
			kmc_uiconf			: kmc.vars.kmc_general_uiconf,

			//permission uiconf id:
			permission_uiconf	: kmc.vars.kmc_permissions_uiconf,

			host				: kmc.vars.host,
			cdnhost				: kmc.vars.cdn_host,
			srvurl				: "api_v3/index.php",
			protocol 			: window.location.protocol + '//',
			partnerid			: kmc.vars.partner_id,
			subpid				: kmc.vars.partner_id + '00',
			ks					: kmc.vars.ks,
			entryId				: "-1",
			kshowId				: "-1",
			debugmode			: "true",
			widget_id			: "_" + kmc.vars.partner_id,
			urchinNumber		: kmc.vars.google_analytics_account, // "UA-12055206-1""
			firstLogin			: kmc.vars.first_login,
			openPlayer			: "kmc.preview_embed.doPreviewEmbed", // @todo: remove for 2.0.9 ?
			openPlaylist		: "kmc.preview_embed.doPreviewEmbed",
			openCw				: "kmc.functions.openKcw",
			language			: (kmc.vars.language || "")
		};
		// Disable analytics
		if( kmc.vars.disable_analytics ) {
			flashvars.disableAnalytics = true;
		}
		var params = {
			allowNetworking: "all",
			allowScriptAccess: "always"
		};

		swfobject.embedSWF(kmc_swf_url, "kcms", "100%", "100%", "10.0.0", false, flashvars, params);
		$("#kcms").attr('style', ''); // Reset the object style
	},

	checkForOngoingProcess : function() {
		var warning_message;
		try {
			warning_message = $("#kcms")[0].hasOngoingProcess();
		}
		catch(e) {
			warning_message = null;
		}

		if(warning_message !== null) {
			return warning_message;
		}
		return;
	},
	
	expired : function() {
		kmc.user.logout();
	},

	openKcw : function(conversion_profile, uiconf_tag) {

		conversion_profile = conversion_profile || "";

		// uiconf_tag - uploadWebCam or uploadImport
		var kcw_uiconf = (uiconf_tag == "uploadWebCam") ? kmc.vars.kcw_webcam_uiconf : kmc.vars.kcw_import_uiconf;

		var flashvars = {
			host			: kmc.vars.host,
			cdnhost			: kmc.vars.cdn_host,
			protocol 		: window.location.protocol.slice(0, -1),
			partnerid		: kmc.vars.partner_id,
			subPartnerId	: kmc.vars.partner_id + '00',
			sessionId		: kmc.vars.ks,
			devFlag			: "true",
			entryId			: "-1",
			kshow_id		: "-1",
			terms_of_use	: kmc.vars.terms_of_use,
			close			: "kmc.functions.onCloseKcw",
			quick_edit		: 0, 
			kvar_conversionQuality : conversion_profile
		};

		var params = {
			allowscriptaccess: "always",
			allownetworking: "all",
			bgcolor: "#DBE3E9",
			quality: "high",
			movie: kmc.vars.service_url + "/kcw/ui_conf_id/" + kcw_uiconf
		};
		
		kmc.layout.modal.open( {
			'width' : 700,
			'height' : 420,
			'content' : '<div id="kcw"></div>'
		} );

		swfobject.embedSWF(params.movie, "kcw", "680", "400" , "9.0.0", false, flashvars , params);
	},
	onCloseKcw : function() {
		kmc.layout.modal.close();
		$("#kcms")[0].gotoPage({
			moduleName: "content",
			subtab: "manage"
		});
	},
	// Should be moved into user object
	openChangePwd : function(email) {
		kmc.user.changeSetting('password');
	},
	openChangeEmail : function(email) {
		kmc.user.changeSetting('email');
	},
	openChangeName : function(fname, lname, email) {
		kmc.user.changeSetting('name');
	},
	getAddPanelPosition : function() {
		var el = $("#add").parent();
		return (el.position().left + el.width() - 10);
	},
	openClipApp : function( entry_id, mode ) {
		
		var iframe_url = kmc.vars.base_url + '/apps/clipapp/' + kmc.vars.clipapp.version;
			iframe_url += '/?kdpUiconf=' + kmc.vars.clipapp.kdp + '&kclipUiconf=' + kmc.vars.clipapp.kclip;
			iframe_url += '&partnerId=' + kmc.vars.partner_id + '&host=' + kmc.vars.host + '&mode=' + mode + '&config=kmc&entryId=' + entry_id;

		var title = ( mode == 'trim' ) ? 'Trimming Tool' : 'Clipping Tool';

		kmc.layout.modal.open( {
			'width' : 950,
			'height' : 616,
			'title'	: title,
			'content' : '<iframe src="' + iframe_url + '" width="100%" height="586" frameborder="0"></iframe>',
			'className'	: 'iframe',
			'closeCallback': function() {
				$("#kcms")[0].gotoPage({
					moduleName: "content",
					subtab: "manage"
				});				
			}
		} );
	},
	flashVarsToUrl: function( flashVarsObject ){
		 var params = '';
		 for( var i in flashVarsObject ){
			 var curVal = typeof flashVarsObject[i] == 'object'?
					 JSON.stringify( flashVarsObject[i] ):
					 flashVarsObject[i];
			 params+= '&' + 'flashvars[' + encodeURIComponent( i ) + ']=' +
			 	encodeURIComponent(  curVal );
		 }
		 return params;
	},
	versionIsAtLeast: function( minVersion, clientVersion ) {
		if( ! clientVersion ){
			return false;
		}
		var minVersionParts = minVersion.split('.');
		var clientVersionParts = clientVersion.split('.');
		for( var i =0; i < minVersionParts.length; i++ ) {
			if( parseInt( clientVersionParts[i] ) > parseInt( minVersionParts[i] ) ) {
				return true;
			}
			if( parseInt( clientVersionParts[i] ) < parseInt( minVersionParts[i] ) ) {
				return false;
			}
		}
		// Same version:
		return true;
	},
	getVersionFromPath: function( path ) {
		return (typeof path == 'string') ? path.split("/v")[1].split("/")[0] : false;
	}
};

kmc.utils = {
	// Backward compatability
	closeModal : function() {kmc.layout.modal.close();},

	handleMenu : function() {

		// Activate menu links
		kmc.utils.activateHeader();
	
		// Calculate menu width
		var menu_width = 10;
		$("#user_links > *").each( function() {
			menu_width += $(this).width();
		});

		var openMenu = function() {

			// Set close menu to true
			kmc.vars.close_menu = true;

			var menu_default_css = {
				"width": 0,
				"visibility": 'visible',
				"top": '6px',
				"right": '6px'
			};

			var menu_animation_css = {
				"width": menu_width + 'px',
				"padding-top": '2px',
				"padding-bottom": '2px'
			};

			$("#user_links").css( menu_default_css );
			$("#user_links").animate( menu_animation_css , 500);
		};

		$("#user").hover( openMenu ).click( openMenu );
		$("#user_links").mouseover( function(){
			kmc.vars.close_menu = false;
		} );
		$("#user_links").mouseleave( function() {
			kmc.vars.close_menu = true;
			setTimeout( kmc.utils.closeMenu , 650 );
		} );
		$("#closeMenu").click( function() {
			kmc.vars.close_menu = true;
			kmc.utils.closeMenu();
		} );
	},

	closeMenu : function() {
		if( kmc.vars.close_menu ) {
			$("#user_links").animate( {
				width: 0
			} , 500, function() {
				$("#user_links").css( {
					width: 'auto',
					visibility: 'hidden'
				} );
			});
		}
	},

	activateHeader : function() {
		$("#user_links a").click(function(e) {
			var tab = (e.target.tagName == "A") ? e.target.id : $(e.target).parent().attr("id");

			switch(tab) {
				case "Quickstart Guide" :
					this.href = kmc.vars.quickstart_guide;
					return true;
				case "Logout" :
					kmc.user.logout();
					return false;
				case "Support" :
					kmc.user.openSupport(this);
					return false;
				case "ChangePartner" :
					kmc.user.changePartner();
					return false;
				default :
					return false;
			}
		});
	},

	resize : function() {
		var min_height = ($.browser.ie) ? 640 : 590;
		var doc_height = $(document).height(),
		offset = $.browser.mozilla ? 37 : 74;
		doc_height = (doc_height-offset);
		doc_height = (doc_height < min_height) ? min_height : doc_height; // Flash minimum height is 590 px
		$("#flash_wrap").height(doc_height + "px");
		$("#server_wrap iframe").height(doc_height + "px");
		$("#server_wrap").css("margin-top", "-"+ (doc_height + 2) +"px");
	},
	isModuleLoaded : function() {
		if($("#flash_wrap object").length || $("#flash_wrap embed").length) {
			kmc.utils.resize();
			clearInterval(kmc.vars.isLoadedInterval);
			kmc.vars.isLoadedInterval = null;
		}
	},
	debug : function() {
		try{
			console.info(" ks: ",kmc.vars.ks);
			console.info(" partner_id: ",kmc.vars.partner_id);
		}
		catch(err) {}
	},
	
	// we should have only one overlay for both flash & html modals
	maskHeader : function(hide) {
		if(hide) {
			$("#mask").hide();
		}
		else {
			$("#mask").show();
		}
	},

	// Create dynamic tabs
	createTabs : function(arr) {
		// Close the user link menu
		$("#closeMenu").trigger('click');
	
		if(arr) {
			var module_url = kmc.vars.service_url + '/index.php/kmc/kmc4',
				arr_len = arr.length,
				tabs_html = '',
				tab_class;
			for( var i = 0; i < arr_len; i++ ) {
				tab_class = (arr[i].type == "action") ? 'class="menu" ' : '';
				tabs_html += '<li><a id="'+ arr[i].module_name +'" ' + tab_class + ' rel="'+ arr[i].subtab +'" href="'+ module_url + '#' + arr[i].module_name +'|'+ arr[i].subtab +'"><span>' + arr[i].display_name + '</span></a></li>';
			}
				
			$('#hTabs').html(tabs_html);

			// Get maximum width for user name
			var max_user_width = ( $("body").width() - ($("#logo").width() + $("#hTabs").width() + 100) );
			if( ($("#user").width()+ 20) > max_user_width ) {
				$("#user").width(max_user_width);
			}
				
			$('#hTabs a').click(function(e) {
				var tab = (e.target.tagName == "A") ? e.target.id : $(e.target).parent().attr("id");
				var subtab = (e.target.tagName == "A") ? $(e.target).attr("rel") : $(e.target).parent().attr("rel");
					
				var go_to = {
					moduleName : tab,
					subtab : subtab
				};
				$("#kcms")[0].gotoPage(go_to);
				return false;
					
			});
		} else {
			alert('Error geting tabs');
		}
	},
		
	setTab : function(module, resetAll){
		if( resetAll ) {$("#kmcHeader ul li a").removeClass("active");}
		$("a#" + module).addClass("active");
	},

	// Reset active tab
	resetTab : function(module) {
		$("a#" + module).removeClass("active");
	},

	// we should combine the two following functions into one
	hideFlash : function(hide) {
		var ltIE8 = $('html').hasClass('lt-ie8');
		if(hide) {
			if( ltIE8 ) {
				// For IE only we're positioning outside of the screen
				$("#flash_wrap").css("margin-right","3333px");
			} else {
				// For other browsers we're just make it
				$("#flash_wrap").css("visibility","hidden");
				$("#flash_wrap object").css("visibility","hidden");
			}
		} else {
			if( ltIE8 ) {
				$("#flash_wrap").css("margin-right","0");
			} else {
				$("#flash_wrap").css("visibility","visible");
				$("#flash_wrap object").css("visibility","visible");
			}
		}
	},
	showFlash : function() {
		$("#server_wrap").hide();
		$("#server_frame").removeAttr('src');
		if( !kmc.layout.modal.isOpen() ) {
			$("#flash_wrap").css("visibility","visible");
		}
		$("#server_wrap").css("margin-top", 0);
	},

	// HTML Tab iframe
	openIframe : function(url) {
		$("#flash_wrap").css("visibility","hidden");
		$("#server_frame").attr("src", url);
		$("#server_wrap").css("margin-top", "-"+ ($("#flash_wrap").height() + 2) +"px");
		$("#server_wrap").show();
	},
	
	openHelp: function( key ) {
		$("#kcms")[0].doHelp( key );
	},

	setClientIP: function() {
		kmc.vars.clientIP = "";
		if( kmc.vars.akamaiEdgeServerIpURL ) {
			$.ajax({
			  url: window.location.protocol + '//' + kmc.vars.akamaiEdgeServerIpURL,
			  crossDomain: true,
			  success: function( data ) {
				kmc.vars.clientIP = $(data).find('serverip').text();
			  }
			});
		}
	},
	getClientIP: function() {
		return kmc.vars.clientIP;
	}
		
};

kmc.mediator =  {

	writeUrlHash : function(module,subtab){
		location.hash = module + "|" + subtab;
		document.title = "KMC > " + module + ((subtab && subtab !== "") ? " > " + subtab + " |" : "");
	},
	readUrlHash : function() {
		var module = "dashboard", 
		subtab = "",
		extra = {}, 
		hash, nohash;

		try {
			hash = location.hash.split("#")[1].split("|");
		}
		catch(err) {
			nohash=true;
		}
		if(!nohash && hash[0]!=="") {
			module = hash[0];
			subtab = hash[1];
			
			if (hash[2])
			{
				var tmp = hash[2].split("&");
				for (var i = 0; i<tmp.length; i++)
				{
					var tmp2 = tmp[i].split(":");
					extra[tmp2[0]] = tmp2[1];
				}
			}

			// Support old hash links
			switch(module) {

				// case for Content tab
				case "content":
					switch(subtab) {
						case "Moderate":
							subtab = "moderation";
							break;
						case "Syndicate":
							subtab = "syndication";
							break;
					}
					subtab = subtab.toLowerCase();
					break;

				// case for Studio tab
				case "appstudio":
					module = "studio";
					subtab = "playersList";
					break;

				// case for Settings tab
				case "Settings":
					module = "account";
					switch(subtab) {
						case "Account_Settings":
							subtab = "overview";
							break;
						case "Integration Settings":
							subtab = "integration";
							break;
						case "Access Control":
							subtab = "accessControl";
							break;
						case "Transcoding Settings":
							subtab = "transcoding";
							break;
						case "Account Upgrade":
							subtab = "upgrade";
							break;
					}
					break;
		    
				// case for Analytics tab
				case "reports":
					module = "analytics";
					if(subtab == "Bandwidth Usage Reports") {
						subtab = "usageTabTitle";
					}
					break;
			}
		}

		return {
			"moduleName" : module,
			"subtab" : subtab,
			"extra" : extra
		};
	}
};

kmc.preview_embed = {
	// Should be changed to accept object with parameters
	doPreviewEmbed : function(id, name, description, previewOnly, is_playlist, uiconf_id, live_bitrates, entry_flavors, is_video) {

		var embedOptions = {
			'previewOnly': previewOnly
		};

		// Add uiConfId
		if( uiconf_id ) {
			embedOptions.uiConfId = parseInt(uiconf_id);
		}

		// Single entry
		if( ! is_playlist ) {
			embedOptions.entryId = id;
			embedOptions.entryMeta = {
				'name': name,
				'description': description
			};
			if( live_bitrates ) {
				embedOptions.liveBitrates = live_bitrates;
			}
		} else {
			// Multiple Playlists
			if( id == 'multitab_playlist' ) {
				embedOptions.playerOnly = true;
				embedOptions.name = name;
			} else { // Single playlist
				embedOptions.playlistId = id;
				embedOptions.playlistName = name;
			}
		}

		kmc.Preview.openPreviewEmbed( embedOptions, kmc.Preview.Service );
	}, // doPreviewEmbed

	// for content|Manage->drilldown->flavors->preview
	doFlavorPreview : function(entryId, entryName, flavorDetails) {

		var player = kmc.vars.default_kdp;
		var code = kmc.Preview.getGenerator().getCode({
			protocol: location.protocol.substring(0, location.protocol.length - 1),
			embedType: 'legacy',
			entryId: entryId,
			uiConfId: parseInt(player.id),
			width: player.width,
			height: player.height,
			includeSeoMetadata: false,
			includeHtml5Library: false,
			flashVars: {
				'ks': kmc.vars.ks,
				'flavorId': flavorDetails.asset_id
			}
		});

		var modal_content = '<div class="center">' + code + '</div><dl>' +
		'<dt>Entry Name:</dt><dd>&nbsp;' + entryName + '</dd>' +
		'<dt>Entry Id:</dt><dd>&nbsp;' + entryId + '</dd>' +
		'<dt>Flavor Name:</dt><dd>&nbsp;' + flavorDetails.flavor_name + '</dd>' +
		'<dt>Flavor Asset Id:</dt><dd>&nbsp;' + flavorDetails.asset_id + '</dd>' +
		'<dt>Bitrate:</dt><dd>&nbsp;' + flavorDetails.bitrate + '</dd>' +
		'<dt>Codec:</dt><dd>&nbsp;' + flavorDetails.codec + '</dd>' +
		'<dt>Dimensions:</dt><dd>&nbsp;' + flavorDetails.dimensions.width + ' x ' + flavorDetails.dimensions.height + '</dd>' +
		'<dt>Format:</dt><dd>&nbsp;' + flavorDetails.format + '</dd>' +
		'<dt>Size (KB):</dt><dd>&nbsp;' + flavorDetails.sizeKB + '</dd>' +
		'<dt>Status:</dt><dd>&nbsp;' + flavorDetails.status + '</dd>' +
		'</dl>';

		kmc.layout.modal.open( {
			'width' : parseInt(player.width) + 120,
			'height' : parseInt(player.height) + 300,
			'title' : 'Flavor Preview',
			'content' : '<div id="preview_embed">' + modal_content + '</div>'
		} );

	},
	updateList : function(is_playlist) {
		var type = is_playlist ? "playlist" : "player";
		$.ajax({
			url: kmc.vars.base_url + kmc.vars.getuiconfs_url,
			type: "POST",
			data: {
				"type": type,
				"partner_id": kmc.vars.partner_id,
				"ks": kmc.vars.ks
				},
			dataType: "json",
			success: function(data) {
				if (data && data.length) {
					if(is_playlist) {
						kmc.vars.playlists_list = data;
					}
					else {
						kmc.vars.players_list = data;
					}
					kmc.Preview.Service.updatePlayers();
				}
			}
		});
	}
};

kmc.client = {
	makeRequest: function( service, action, params, callback ) {
		var serviceUrl = kmc.vars.api_url + '/api_v3/index.php?service='+service+'&action='+action;
		var defaultParams = {
			"ks"		: kmc.vars.ks,
			"format"	: 9
		};
		// Merge params and defaults
		$.extend( params, defaultParams);
		
		var ksort = function ( arr ) {
			var sArr = [];
			var tArr = [];
			var n = 0;
			for ( var k in arr ){
				tArr[n++] = k+"|"+arr[k];
			}
			tArr = tArr.sort();
			for (var i=0; i<tArr.length; i++) {
				var x = tArr[i].split("|");
				sArr[x[0]] = x[1];
			}
			return sArr;
		};
		
		var getSignature = function( params ){
			params = ksort(params);
			var str = "";
			for(var v in params) {
				var k = params[v];
				str += k + v;
			}
			return md5(str);
		};
		
		// Add kaltura signature param
		var kalsig = getSignature( params );
		serviceUrl += '&kalsig=' + kalsig;

		// Make request
		$.ajax({
			type: 'GET',
			url: serviceUrl, 
			dataType: 'jsonp',
			data: params, 
			cache: false,
			success: callback
		});	
	},
		
	createShortURL : function(url, callback) {
		kmc.log('createShortURL');
			
		var data = {
			"shortLink:objectType"	: "KalturaShortLink",
			"shortLink:systemName"	: "KMC-PREVIEW", // Unique name for filtering
			"shortLink:fullUrl"		: url
		};
			
		kmc.client.makeRequest("shortlink_shortlink", "add", data, function( res ) {
			var tinyUrl = false;
			if( callback ) {
				if( res.id ) {
					tinyUrl = kmc.vars.service_url + '/tiny/' + res.id;
				}
				callback( tinyUrl );
			} else {
				kmc.preview_embed.setShortURL(res.id);
			}
		});
	}
};

kmc.layout = {
	init: function() {
		// Close open menu if user click anywhere
		$("#kmcHeader").bind( 'click', function() { 
			$("#hTabs a").each(function(inx, tab) {
				var $tab = $(tab);
				if( $tab.hasClass('menu') && $tab.hasClass('active') ){
					$("#kcms")[0].gotoPage({
						moduleName: $tab.attr('id'),
						subtab: $tab.attr('rel')
					});
				} else {
					return true;
				}
			});
		} );
		// Add Modal & Overlay divs when page loads
		$("body").append('<div id="mask"></div><div id="overlay"></div><div id="modal" class="modal"><div class="title"><h2></h2><span class="close icon"></span></div><div class="content"></div></div>');
	},
	overlay: {
		show: function() {$("#overlay").show();},
		hide: function() {$("#overlay").hide();}
	},
	modal: {
		el: '#modal',

		create: function(data) {
			var options = {
				'el': kmc.layout.modal.el,
				'title': '',
				'content': '',
				'help': '',
				'width': 680,
				'height': 'auto',
				'className': ''
			};
			// Overwrite defaults with data
			$.extend(options, data);			
			// Set defaults
			var $modal = $(options.el),
				$modal_title = $modal.find(".title h2"),
				$modal_content = $modal.find(".content");

			// Add default ".modal" class
			options.className = 'modal ' + options.className;

			// Set width & height
			$modal.css( {
				'width' : options.width,
				'height' : options.height
			}).attr('class', options.className);

			// Insert data into modal
			if( options.title ) {
				$modal_title.text(options.title).attr('title', options.title).parent().show();
			} else {
				$modal_title.parent().hide();
				$modal_content.addClass('flash_only');
			}
			$modal.find(".help").remove();
			$modal_title.parent().append( options.help );
			$modal_content[0].innerHTML = options.content;

			// Activate close button
			$modal.find(".close").click( function() {
				kmc.layout.modal.close(options.el);
				if( $.isFunction( data.closeCallback ) ) {
					data.closeCallback();
				}
			});

			return $modal;
		},

		show: function(el, position) {
			position = (position === undefined) ? true : position;
			el = el || kmc.layout.modal.el;
			var $modal = $(el);

			kmc.utils.hideFlash(true);
			kmc.layout.overlay.show();
			$modal.fadeIn(600);
			
			if( ! $.browser.msie ) {
				$modal.css('display', 'table');
			}
			
			if( position ) {
				this.position(el);
			}
		},

		open: function(data) {
			this.create(data);
			var el = data.el || kmc.layout.modal.el;
			this.show(el);
		},
		
		position: function(el) {
			el = el || kmc.layout.modal.el;
			var $modal = $(el);
			// Calculate Modal Position
			var mTop = ( ($(window).height() - $modal.height()) / 2 ),
				mLeft = ( ($(window).width() - $modal.width()) / (2+$(window).scrollLeft()) );
				mTop = (mTop < 40) ? 40 : mTop;
			// Apply style
			$modal.css( {
				'top' : mTop + "px",
				'left' : mLeft + "px"
			});
			
		},
		close: function(el) {
			el = el || kmc.layout.modal.el;
			$(el).fadeOut(300, function() {
				$(el).find(".content").html('');
				kmc.layout.overlay.hide();
				kmc.utils.hideFlash();
			});
		},
		isOpen: function(el) {
			el = el || kmc.layout.modal.el;
			return $(el).is(":visible");
		}
	}
};

kmc.user = {

	openSupport: function(el) {
		var href = el.href;
		// Show overlay
		kmc.utils.hideFlash(true);
		kmc.layout.overlay.show();

		// We want the show the modal only after the iframe is loaded so we use "create" instead of "open"
	   	var modal_content = '<iframe id="support" src="' + href + '" width="100%" scrolling="no" frameborder="0"></iframe>';
		kmc.layout.modal.create( {
			'width' : 550,
			'title' : 'Support Request',
			'content' : modal_content
		} );

		// Wait until iframe loads and then show the modal
		$("#support").load(function() {
			// In order to get the iframe content height the modal must be visible
			kmc.layout.modal.show();
			// Get iframe content height & update iframe
			if( ! kmc.vars.support_frame_height ) {
				kmc.vars.support_frame_height = $("#support")[0].contentWindow.document.body.scrollHeight;
			}
			$("#support").height( kmc.vars.support_frame_height );
			// Re-position the modal box
			kmc.layout.modal.position();
		});
	},

	logout: function() {
		var message = kmc.functions.checkForOngoingProcess();
		if( message ) {alert( message );return false;}
		var state = kmc.mediator.readUrlHash();
		// Cookies are HTTP only, we delete them using logoutAction
		$.ajax({
			url: kmc.vars.base_url + "/index.php/kmc/logout",
			type: "POST",
			data: {
				"ks": kmc.vars.ks
				},
			dataType: "json",
			complete: function() {
				if (kmc.vars.logoutUrl)
					window.location = kmc.vars.logoutUrl;
				else
					window.location = kmc.vars.service_url + "/index.php/kmc/kmc#" + state.moduleName + "|" + state.subtab;
			}
		});
	},

	changeSetting: function(action) {
		// Set title
		var title, iframe_height;
		switch(action) {
			case "password":
				title = "Change Password";
				iframe_height = 180;
				break;
			case "email":
				title = "Change Email Address";
				iframe_height = 160;
				break;
			case "name":
				title = "Edit Name";
				iframe_height = 200;
				break;
		}

		// setup url
		var http_protocol = (kmc.vars.kmc_secured || location.protocol == 'https:') ? 'https' : 'http';
		var from_domain = http_protocol + '://' + window.location.hostname;
		var url = from_domain + kmc.vars.port + "/index.php/kmc/updateLoginData/type/" + action;
		// pass the parent url for the postMessage to work
		url = url + '?parent=' + encodeURIComponent(document.location.href);

		var modal_content = '<iframe src="' + url + '" width="100%" height="' + iframe_height + '" scrolling="no" frameborder="0"></iframe>';

		kmc.layout.modal.open( {
			'width' : 370,
			'title' : title,
			'content' : modal_content
		} );

		// setup a callback to handle the dispatched MessageEvent. if window.postMessage is supported the passed
		// event will have .data, .origin and .source properties. otherwise, it will only have the .data property.
		XD.receiveMessage(function(message){
			kmc.layout.modal.close();
			if(message.data == "reload") {
				if( ($.browser.msie) && ($.browser.version < 8) ) {
					window.location.hash = "account|user";
				}
				window.location.reload();
			}
		}, from_domain);
	},

	changePartner: function() {

		var i, pid = 0, selected, bolded,
			total = kmc.vars.allowed_partners.length;

		var modal_content = '<div id="change_account"><span>Please choose partner:</span><div class="container">';

		for( i=0; i < total; i++ ) {
			pid = kmc.vars.allowed_partners[i].id;
			if( kmc.vars.partner_id == pid ) {
				selected = ' checked="checked"';
				bolded = ' style="font-weight: bold"';
			} else {
				selected = '';
				bolded = '';
			}
			modal_content += '<label' + bolded + '><input type="radio" name="pid" value="' + pid + '" ' + selected + '/> &nbsp;' + kmc.vars.allowed_partners[i].name + '</label>';
		}
		modal_content += '</div><div class="center"><button id="do_change_partner"><span>Continue</span></button></div>';

		kmc.layout.modal.open( {
			'width' : 300,
			'title' : 'Change Account',
			'content' : modal_content
		} );

		$("#do_change_partner").click(function() {

			var url = kmc.vars.base_url + '/index.php/kmc/extlogin';

			// Setup input fields
			var ks_input = $('<input />').attr({
				'type': 'hidden',
				'name': 'ks',
				'value': kmc.vars.ks
			});
			var partner_id_input = $('<input />').attr({
				'type': 'hidden',
				'name': 'partner_id',
				'value': $('input[name=pid]:radio:checked').val() // grab the selected partner id
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
		});

		return false;
	}
};

// Maintain support for old kmc2 functions:
function openPlayer(title, width, height, uiconf_id, previewOnly) {
	if (previewOnly===true) $("#kcms")[0].alert('previewOnly from studio');
	kmc.preview_embed.doPreviewEmbed("multitab_playlist", title, null, previewOnly, true, uiconf_id, false, false, false);
}
function playlistAdded() {kmc.preview_embed.updateList(true);}
function playerAdded() {kmc.preview_embed.updateList(false);}
/*** end old functions ***/

// When page ready initilize KMC
$(function() {
	kmc.layout.init();
	kmc.utils.handleMenu();
	kmc.functions.loadSwf();

	// Set resize event to update the flash object size
	$(window).wresize(kmc.utils.resize);
	kmc.vars.isLoadedInterval = setInterval(kmc.utils.isModuleLoaded,200);	

	// Load kdp player & playlists for preview & embed
	kmc.preview_embed.updateList(); // Load players
	kmc.preview_embed.updateList(true); // Load playlists

	// Set client IP
	kmc.utils.setClientIP();
});

// Auto resize modal windows
$(window).resize(function() {
	// Exit if not open
	if( kmc.layout.modal.isOpen() ) {
		kmc.layout.modal.position();
	}
});

// If we have ongoing process, we show a warning message when the user try to leaves the page
window.onbeforeunload = kmc.functions.checkForOngoingProcess;

/* WResize: plugin for fixing the IE window resize bug (http://noteslog.com/) */
(function($){$.fn.wresize=function(f){version='1.1';wresize={fired:false,width:0};function resizeOnce(){if($.browser.msie){if(!wresize.fired){wresize.fired=true}else{var version=parseInt($.browser.version,10);wresize.fired=false;if(version<7){return false}else if(version==7){var width=$(window).width();if(width!=wresize.width){wresize.width=width;return false}}}}return true}function handleWResize(e){if(resizeOnce()){return f.apply(this,[e])}}this.each(function(){if(this==window){$(this).resize(handleWResize)}else{$(this).resize(f)}});return this}})(jQuery);

/* XD: a backwards compatable implementation of postMessage (http://www.onlineaspect.com/2010/01/15/backwards-compatible-postmessage/) */
var XD=function(){var e,g,h=1,f,d=this;return{postMessage:function(c,b,a){if(b)if(a=a||parent,d.postMessage)a.postMessage(c,b.replace(/([^:]+:\/\/[^\/]+).*/,"$1"));else if(b)a.location=b.replace(/#.*$/,"")+"#"+ +new Date+h++ +"&"+c},receiveMessage:function(c,b){if(d.postMessage)if(c&&(f=function(a){if(typeof b==="string"&&a.origin!==b||Object.prototype.toString.call(b)==="[object Function]"&&b(a.origin)===!1)return!1;c(a)}),d.addEventListener)d[c?"addEventListener":"removeEventListener"]("message",
f,!1);else d[c?"attachEvent":"detachEvent"]("onmessage",f);else e&&clearInterval(e),e=null,c&&(e=setInterval(function(){var a=document.location.hash,b=/^#?\d+&/;a!==g&&b.test(a)&&(g=a,c({data:a.replace(b,"")}))},100))}}}();

/* md5 and utf8_encode from phpjs.org */
function md5(str){var xl;var rotateLeft=function(lValue,iShiftBits){return(lValue<<iShiftBits)|(lValue>>>(32-iShiftBits));};var addUnsigned=function(lX,lY){var lX4,lY4,lX8,lY8,lResult;lX8=(lX&0x80000000);lY8=(lY&0x80000000);lX4=(lX&0x40000000);lY4=(lY&0x40000000);lResult=(lX&0x3FFFFFFF)+(lY&0x3FFFFFFF);if(lX4&lY4){return(lResult^0x80000000^lX8^lY8);}
if(lX4|lY4){if(lResult&0x40000000){return(lResult^0xC0000000^lX8^lY8);}else{return(lResult^0x40000000^lX8^lY8);}}else{return(lResult^lX8^lY8);}};var _F=function(x,y,z){return(x&y)|((~x)&z);};var _G=function(x,y,z){return(x&z)|(y&(~z));};var _H=function(x,y,z){return(x^y^z);};var _I=function(x,y,z){return(y^(x|(~z)));};var _FF=function(a,b,c,d,x,s,ac){a=addUnsigned(a,addUnsigned(addUnsigned(_F(b,c,d),x),ac));return addUnsigned(rotateLeft(a,s),b);};var _GG=function(a,b,c,d,x,s,ac){a=addUnsigned(a,addUnsigned(addUnsigned(_G(b,c,d),x),ac));return addUnsigned(rotateLeft(a,s),b);};var _HH=function(a,b,c,d,x,s,ac){a=addUnsigned(a,addUnsigned(addUnsigned(_H(b,c,d),x),ac));return addUnsigned(rotateLeft(a,s),b);};var _II=function(a,b,c,d,x,s,ac){a=addUnsigned(a,addUnsigned(addUnsigned(_I(b,c,d),x),ac));return addUnsigned(rotateLeft(a,s),b);};var convertToWordArray=function(str){var lWordCount;var lMessageLength=str.length;var lNumberOfWords_temp1=lMessageLength+8;var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1%64))/64;var lNumberOfWords=(lNumberOfWords_temp2+1)*16;var lWordArray=new Array(lNumberOfWords-1);var lBytePosition=0;var lByteCount=0;while(lByteCount<lMessageLength){lWordCount=(lByteCount-(lByteCount%4))/4;lBytePosition=(lByteCount%4)*8;lWordArray[lWordCount]=(lWordArray[lWordCount]|(str.charCodeAt(lByteCount)<<lBytePosition));lByteCount++;}
lWordCount=(lByteCount-(lByteCount%4))/4;lBytePosition=(lByteCount%4)*8;lWordArray[lWordCount]=lWordArray[lWordCount]|(0x80<<lBytePosition);lWordArray[lNumberOfWords-2]=lMessageLength<<3;lWordArray[lNumberOfWords-1]=lMessageLength>>>29;return lWordArray;};var wordToHex=function(lValue){var wordToHexValue="",wordToHexValue_temp="",lByte,lCount;for(lCount=0;lCount<=3;lCount++){lByte=(lValue>>>(lCount*8))&255;wordToHexValue_temp="0"+lByte.toString(16);wordToHexValue=wordToHexValue+wordToHexValue_temp.substr(wordToHexValue_temp.length-2,2);}
return wordToHexValue;};var x=[],k,AA,BB,CC,DD,a,b,c,d,S11=7,S12=12,S13=17,S14=22,S21=5,S22=9,S23=14,S24=20,S31=4,S32=11,S33=16,S34=23,S41=6,S42=10,S43=15,S44=21;str=this.utf8_encode(str);x=convertToWordArray(str);a=0x67452301;b=0xEFCDAB89;c=0x98BADCFE;d=0x10325476;xl=x.length;for(k=0;k<xl;k+=16){AA=a;BB=b;CC=c;DD=d;a=_FF(a,b,c,d,x[k+0],S11,0xD76AA478);d=_FF(d,a,b,c,x[k+1],S12,0xE8C7B756);c=_FF(c,d,a,b,x[k+2],S13,0x242070DB);b=_FF(b,c,d,a,x[k+3],S14,0xC1BDCEEE);a=_FF(a,b,c,d,x[k+4],S11,0xF57C0FAF);d=_FF(d,a,b,c,x[k+5],S12,0x4787C62A);c=_FF(c,d,a,b,x[k+6],S13,0xA8304613);b=_FF(b,c,d,a,x[k+7],S14,0xFD469501);a=_FF(a,b,c,d,x[k+8],S11,0x698098D8);d=_FF(d,a,b,c,x[k+9],S12,0x8B44F7AF);c=_FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);b=_FF(b,c,d,a,x[k+11],S14,0x895CD7BE);a=_FF(a,b,c,d,x[k+12],S11,0x6B901122);d=_FF(d,a,b,c,x[k+13],S12,0xFD987193);c=_FF(c,d,a,b,x[k+14],S13,0xA679438E);b=_FF(b,c,d,a,x[k+15],S14,0x49B40821);a=_GG(a,b,c,d,x[k+1],S21,0xF61E2562);d=_GG(d,a,b,c,x[k+6],S22,0xC040B340);c=_GG(c,d,a,b,x[k+11],S23,0x265E5A51);b=_GG(b,c,d,a,x[k+0],S24,0xE9B6C7AA);a=_GG(a,b,c,d,x[k+5],S21,0xD62F105D);d=_GG(d,a,b,c,x[k+10],S22,0x2441453);c=_GG(c,d,a,b,x[k+15],S23,0xD8A1E681);b=_GG(b,c,d,a,x[k+4],S24,0xE7D3FBC8);a=_GG(a,b,c,d,x[k+9],S21,0x21E1CDE6);d=_GG(d,a,b,c,x[k+14],S22,0xC33707D6);c=_GG(c,d,a,b,x[k+3],S23,0xF4D50D87);b=_GG(b,c,d,a,x[k+8],S24,0x455A14ED);a=_GG(a,b,c,d,x[k+13],S21,0xA9E3E905);d=_GG(d,a,b,c,x[k+2],S22,0xFCEFA3F8);c=_GG(c,d,a,b,x[k+7],S23,0x676F02D9);b=_GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);a=_HH(a,b,c,d,x[k+5],S31,0xFFFA3942);d=_HH(d,a,b,c,x[k+8],S32,0x8771F681);c=_HH(c,d,a,b,x[k+11],S33,0x6D9D6122);b=_HH(b,c,d,a,x[k+14],S34,0xFDE5380C);a=_HH(a,b,c,d,x[k+1],S31,0xA4BEEA44);d=_HH(d,a,b,c,x[k+4],S32,0x4BDECFA9);c=_HH(c,d,a,b,x[k+7],S33,0xF6BB4B60);b=_HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);a=_HH(a,b,c,d,x[k+13],S31,0x289B7EC6);d=_HH(d,a,b,c,x[k+0],S32,0xEAA127FA);c=_HH(c,d,a,b,x[k+3],S33,0xD4EF3085);b=_HH(b,c,d,a,x[k+6],S34,0x4881D05);a=_HH(a,b,c,d,x[k+9],S31,0xD9D4D039);d=_HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);c=_HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);b=_HH(b,c,d,a,x[k+2],S34,0xC4AC5665);a=_II(a,b,c,d,x[k+0],S41,0xF4292244);d=_II(d,a,b,c,x[k+7],S42,0x432AFF97);c=_II(c,d,a,b,x[k+14],S43,0xAB9423A7);b=_II(b,c,d,a,x[k+5],S44,0xFC93A039);a=_II(a,b,c,d,x[k+12],S41,0x655B59C3);d=_II(d,a,b,c,x[k+3],S42,0x8F0CCC92);c=_II(c,d,a,b,x[k+10],S43,0xFFEFF47D);b=_II(b,c,d,a,x[k+1],S44,0x85845DD1);a=_II(a,b,c,d,x[k+8],S41,0x6FA87E4F);d=_II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);c=_II(c,d,a,b,x[k+6],S43,0xA3014314);b=_II(b,c,d,a,x[k+13],S44,0x4E0811A1);a=_II(a,b,c,d,x[k+4],S41,0xF7537E82);d=_II(d,a,b,c,x[k+11],S42,0xBD3AF235);c=_II(c,d,a,b,x[k+2],S43,0x2AD7D2BB);b=_II(b,c,d,a,x[k+9],S44,0xEB86D391);a=addUnsigned(a,AA);b=addUnsigned(b,BB);c=addUnsigned(c,CC);d=addUnsigned(d,DD);}
var temp=wordToHex(a)+wordToHex(b)+wordToHex(c)+wordToHex(d);return temp.toLowerCase();}
function utf8_encode(argString){if(argString===null||typeof argString==="undefined"){return"";}
var string=(argString+'');var utftext="",start,end,stringl=0;start=end=0;stringl=string.length;for(var n=0;n<stringl;n++){var c1=string.charCodeAt(n);var enc=null;if(c1<128){end++;}else if(c1>127&&c1<2048){enc=String.fromCharCode((c1>>6)|192)+String.fromCharCode((c1&63)|128);}else{enc=String.fromCharCode((c1>>12)|224)+String.fromCharCode(((c1>>6)&63)|128)+String.fromCharCode((c1&63)|128);}
if(enc!==null){if(end>start){utftext+=string.slice(start,end);}
utftext+=enc;start=end=n+1;}}
if(end>start){utftext+=string.slice(start,stringl);}
return utftext;}