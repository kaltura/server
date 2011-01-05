/*!
 * Modernizr JavaScript library 1.2pre
 * http://modernizr.com/
 *
 * Copyright (c) 2009-2010 Faruk Ates - http://farukat.es/
 * Licensed under the MIT license.
 * http://modernizr.com/license/
 *
 * Featuring major contributions by
 * Paul Irish  - http://paulirish.com
 */
/*
 * Modernizr is a script that will detect native CSS3 and HTML5 features
 * available in the current UA and provide an object containing all
 * features with a true/false value, depending on whether the UA has
 * native support for it or not.
 * 
 * In addition to that, Modernizr will add classes to the <html>
 * element of the page, one for each cutting-edge feature. If the UA
 * supports it, a class like "cssgradients" will be added. If not,
 * the class name will be "no-cssgradients". This allows for simple
 * if-conditionals in CSS styling, making it easily to have fine
 * control over the look and feel of your website.
 * 
 * @author    Faruk Ates
 * @copyright   (2009-2010) Faruk Ates.
 *
 * @contributor   Paul Irish
 * @contributor   Ben Alman
 */

window.Modernizr = (function(window,doc,undefined){
    
    var version = '1.2pre',
    
    ret = {},

    /**
     * enableHTML5 is a private property for advanced use only. If enabled,
     * it will make Modernizr.init() run through a brief while() loop in
     * which it will create all HTML5 elements in the DOM to allow for
     * styling them in Internet Explorer, which does not recognize any
     * non-HTML4 elements unless created in the DOM this way.
     * 
     * enableHTML5 is ON by default.
     */
    enableHTML5 = true,
    
    
    /**
     * fontfaceCheckDelay is the ms delay before the @font-face test is
     * checked a second time. This is neccessary because both Gecko and
     * WebKit do not load data: URI font data synchronously.
     *   https://bugzilla.mozilla.org/show_bug.cgi?id=512566
     * The check will be done again at fontfaceCheckDelay*2 and then 
     * a fourth time at window's load event. 
     * If you need to query for @font-face support, send a callback to: 
     *  Modernizr._fontfaceready(fn);
     * The callback is passed the boolean value of Modernizr.fontface
     */
    fontfaceCheckDelay = 75,
    
    
    docElement = doc.documentElement,

    /**
     * Create our "modernizr" element that we do most feature tests on.
     */
    mod = 'modernizr'
    m = doc.createElement( mod ),
    m_style = m.style,

    /**
     * Create the input element for various Web Forms feature tests.
     */
    f = doc.createElement( 'input' ),
    
    // Reused strings.
    
    canvas = 'canvas',
    canvastext = 'canvastext',
    webgl = 'webgl',
    rgba = 'rgba',
    hsla = 'hsla',
    multiplebgs = 'multiplebgs',
    borderimage = 'borderimage',
    borderradius = 'borderradius',
    boxshadow = 'boxshadow',
    opacity = 'opacity',
    cssanimations = 'cssanimations',
    csscolumns = 'csscolumns',
    cssgradients = 'cssgradients',
    cssreflections = 'cssreflections',
    csstransforms = 'csstransforms',
    csstransforms3d = 'csstransforms3d',
    csstransitions = 'csstransitions',
    fontface = 'fontface',
    geolocation = 'geolocation',
    video = 'video',
    audio = 'audio',
    input = 'input',
    inputtypes = input + 'types',
    // inputtypes is an object of its own containing individual tests for
    // various new input types, such as search, range, datetime, etc.
    
    svg = 'svg',
    smil = 'smil',
    svgclippaths = svg+'clippaths',
    
    background = 'background',
    backgroundColor = background + 'Color',
    canPlayType = 'canPlayType',
    
    // FF gets really angry if you name local variables as these, but camelCased.
    localstorage = 'localStorage',
    sessionstorage = 'sessionStorage',
    applicationcache = 'applicationCache',
    
    webWorkers = 'webworkers',
    hashchange = 'hashchange',
    crosswindowmessaging = 'crosswindowmessaging',
    historymanagement = 'historymanagement',
    draganddrop = 'draganddrop',
    websqldatabase = 'websqldatabase',
    websocket = 'websocket',
    flash = 'flash',
    smile = ':)',
    touch = 'touch',
    
    toString = Object.prototype.toString,
    
    // list of property values to set for css tests. see ticket #21
    setProperties = ' -o- -moz- -ms- -webkit- -khtml- '.split(' '),

    tests = {},
    inputs = {},
    attrs = {},
    
    classes = [],
    
    /**
      * isEventSupported determines if a given element supports the given event
      * function from http://yura.thinkweb2.com/isEventSupported/
      */
    isEventSupported = (function(){
  
        var TAGNAMES = {
          'select':'input','change':'input',
          'submit':'form','reset':'form',
          'error':'img','load':'img','abort':'img'
        }, 
        cache = { };
        
        function isEventSupported(eventName, element) {
            var canCache = (arguments.length == 1);
            
            // only return cached result when no element is given
            if (canCache && cache[eventName]) {
                return cache[eventName];
            }
            
            element = element || document.createElement(TAGNAMES[eventName] || 'div');
            eventName = 'on' + eventName;
            
            // When using `setAttribute`, IE skips "unload", WebKit skips "unload" and "resize"
            // `in` "catches" those
            var isSupported = (eventName in element);
            
            if (!isSupported && element.setAttribute) {
                element.setAttribute(eventName, 'return;');
                isSupported = typeof element[eventName] == 'function';
            }
            
            element = null;
            return canCache ? (cache[eventName] = isSupported) : isSupported;
        }
        
        return isEventSupported;
    })();    
    
    
    /**
     * set_css applies given styles to the Modernizr DOM node.
     */
    function set_css( str ) {
        m_style.cssText = str;
    }

    /**
     * set_css_all extrapolates all vendor-specific css strings.
     */
    function set_css_all( str1, str2 ) {
        return set_css(setProperties.join(str1 + ';') + ( str2 || '' ));
    }

    /**
     * contains returns a boolean for if substr is found within str.
     */
    function contains( str, substr ) {
        return (''+str).indexOf( substr ) !== -1;
    }

    /**
     * test_props is a generic CSS / DOM property test; if a browser supports
     *   a certain property, it won't return undefined for it.
     *   A supported CSS property returns empty string when its not yet set.
     */
    function test_props( props, callback ) {
        for ( var i in props ) {
            if ( m_style[ props[i] ] !== undefined && ( !callback || callback( props[i] ) ) ) {
                return true;
            }
        }
    }

    /**
     * test_props_all tests a list of DOM properties we want to check against.
     *   We specify literally ALL possible (known and/or likely) properties on 
     *   the element including the non-vendor prefixed one, for forward-
     *   compatibility.
     */
    function test_props_all( prop, callback ) {
        var uc_prop = prop.charAt(0).toUpperCase() + prop.substr(1),
        props = [
            prop,
            'Webkit' + uc_prop,
            'Moz' + uc_prop,
            'O' + uc_prop,
            'ms' + uc_prop,
            'Khtml' + uc_prop
        ];

        return !!test_props( props, callback );
    }
    
    // Tests
   
    tests[csstransforms] = function() {
        //  set_css_all( 'transform:rotate(3deg)' );
        return !!test_props([ 'transformProperty', 'WebkitTransform', 'MozTransform', 'OTransform', 'msTransform' ]);
    };
    
    // end of tests.



    // Run through all tests and detect their support in the current UA.
    // todo: hypothetically we could be doing an array of tests and use a basic loop here.
    for ( var feature in tests ) {
        if ( tests.hasOwnProperty( feature ) ) {
            // run the test, then based on the boolean, define an appropriate className
            classes.push( ( !( ret[ feature ] = tests[ feature ]() ) ? 'no-' : '' ) + feature );
        }
    }
    
    // input tests need to run.
//    if (!ret[input]) webforms();
    

   



    /**
     * Addtest allows the user to define their own feature tests
     * the result will be added onto the Modernizr object,
     * as well as an appropriate className set on the html element
     * 
     * @param feature - String naming the feature
     * @param test - Function returning true if feature is supported, false if not
     */
    ret.addTest = function (feature, test) {
      if (ret[ feature ]) {
        return; // quit if you're trying to overwrite an existing test
      } 
      feature = feature.toLowerCase();
      test = !!(test());
      docElement.className += ' ' + (!test ? 'no-' : '') + feature; 
      ret[ feature ] = test;
      return ret; // allow chaining.
    };

    /**
     * Reset m.style.cssText to nothing to reduce memory footprint.
     */
    set_css( '' );
    m = f = null;

    // Enable HTML 5 elements for styling in IE. 
    if ( enableHTML5 && !(!/*@cc_on@if(@_jscript_version<9)!@end@*/0) ) {
        // iepp v1.5.1 MIT @jon_neal  http://code.google.com/p/ie-print-protector/
        (function(p,e){function q(a,b){if(g[a])g[a].styleSheet.cssText+=b;else{var c=r[l],d=e[j]("style");d.media=a;c.insertBefore(d,c[l]);g[a]=d;q(a,b)}}function s(a,b){for(var c=new RegExp("\\b("+m+")\\b(?!.*[;}])","gi"),d=function(k){return".iepp_"+k},h=-1;++h<a.length;){b=a[h].media||b;s(a[h].imports,b);q(b,a[h].cssText.replace(c,d))}}function t(){for(var a,b=e.getElementsByTagName("*"),c,d,h=new RegExp("^"+m+"$","i"),k=-1;++k<b.length;)if((a=b[k])&&(d=a.nodeName.match(h))){c=new RegExp("^\\s*<"+d+"(.*)\\/"+d+">\\s*$","i");i.innerHTML=a.outerHTML.replace(/\r|\n/g," ").replace(c,a.currentStyle.display=="block"?"<div$1/div>":"<span$1/span>");c=i.childNodes[0];c.className+=" iepp_"+d;c=f[f.length]=[a,c];a.parentNode.replaceChild(c[1],c[0])}s(e.styleSheets,"all")}function u(){for(var a=-1,b;++a<f.length;)f[a][1].parentNode.replaceChild(f[a][0],f[a][1]);for(b in g)r[l].removeChild(g[b]);g={};f=[]}for(var r=e.documentElement,i=e.createDocumentFragment(),g={},m="abbr|article|aside|audio|canvas|command|datalist|details|figure|figcaption|footer|header|hgroup|keygen|mark|meter|nav|output|progress|section|source|summary|time|video",n=m.split("|"),f=[],o=-1,l="firstChild",j="createElement";++o<n.length;){e[j](n[o]);i[j](n[o])}i=i.appendChild(e[j]("div"));p.attachEvent("onbeforeprint",t);p.attachEvent("onafterprint",u)})(this,doc);
    }

    // Assign private properties to the return object with prefix
    ret._enableHTML5     = enableHTML5;
    ret._version         = version;

    // Remove "no-js" class from <html> element, if it exists:
    docElement.className=docElement.className.replace(/\bno-js\b/,'js');

    // Add the new classes to the <html> element.
    docElement.className += ' ' + classes.join( ' ' );
    
    return ret;

})(this,this.document);
