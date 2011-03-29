/**
 * Twitter - http://twitter.com
 * Copyright (C) 2010 Twitter
 * Author: Dustin Diaz (dustin@twitter.com)
 *
 * V 2.2.5 Twitter search/profile/faves/list widget
 * http://twitter.com/widgets
 * For full documented source see http://twitter.com/javascripts/widgets/widget.js
 * Hosting and modifications of the original source IS allowed.
 *
 * Example usage:
   <script src="path/to/widget.js"></script>
   <script>
     new TWTR.Widget({
       type: 'search',
       search: "your search query", // includes all advanced search queries.
       width: 250,
       height: 350,
       interval: 6000,
       subject: "What's being said about...",
       title: "San Francisco",
       rpp: 30,
       footer: "Follow me",
       theme: {
         shell: {
           background: '#1deb25',
           color: '#ffffff'
         },
         tweets: {
           background: 'silver',
           color: 'blue',
           links: 'black'
         }
       },

       features: {
         avatars: true, // defaults to false for profile widget, but true for search & faves widget
         hashtags: true,
         timestamp: true,
         fullscreen: false, // ignores width and height and just goes full screen
         scrollbar: false,
         live: true,
         loop: true,
         behavior: 'default',
         dateformat: 'absolute', // defaults to relative (eg: 3 minutes ago)
         toptweets: true // only for search widget
       }
     }).render().start();
   </script>
 *
 */

/**
  * @namespace TWTR public namespace for Twitter widget
  */
TWTR = window.TWTR || {};

/**
  * add core functionality to JS
  * Sugar Arrays http://www.dustindiaz.com/basement/sugar-arrays.html
  */
if (!Array.forEach) {

  Array.prototype.filter = function(fn, thisObj) {
    var scope = thisObj || window;
    var a = [];
    for (var i=0, j=this.length; i < j; ++i) {
      if (!fn.call(scope, this[i], i, this)) {
        continue;
      }
      a.push(this[i]);
    }
    return a;
  };

  // sorta like inArray if used clever-like
  Array.prototype.indexOf = function(el, start) {
    var start = start || 0;

    for (var i=0; i < this.length; ++i) {
      if (this[i] === el) {
        return i;
      }
    }

    return -1;
  };
}

/* first, a few dependencies */
(function() {
  if (TWTR && TWTR.Widget) {
    // this is most likely to happen when people try to embed multiple
    // widgets on the same page and include this script again
    return;
  }

  /**
    * Basic Array methods
    */
  function each(a, fn, opt_scope) {
    for (var i=0, j=a.length; i < j; ++i) {
      fn.call(opt_scope || window, a[i], i, a);
    }
  }

  /**
    * Generic Animation utility to tween dom elements
    *
    * Copyright (c) 2009 Dustin Diaz & Twitter (http://www.dustindiaz.com)
    * MIT License
    */

  /**
    * @constructor Animate
    * @param {HTMLElement} el the element we want to animate
    * @param {String} prop the CSS property we will be animating
    * @param {Object} opts a configuration object
    * object properties include
    * from {Int}
    * to {Int}
    * time {Int} time in milliseconds
    * callback {Function}
    */
  function Animate(el, prop, opts) {
    this.el = el;
    this.prop = prop;
    this.from = opts.from;
    this.to = opts.to;
    this.time = opts.time;
    this.callback = opts.callback;
    this.animDiff = this.to - this.from;
  };

  /**
    * @static
    * @boolean
    * allows us to check if native CSS transitions are possible
    */
  Animate.canTransition = function() {
    var el = document.createElement('twitter');
    el.style.cssText = '-webkit-transition: all .5s linear;';
    return !!el.style.webkitTransitionProperty;
  }();

  /**
    * @private
    * @param {String} val the CSS value we will set on the property
    */
  Animate.prototype._setStyle = function(val) {
    switch (this.prop) {
      case 'opacity':
        this.el.style[this.prop] = val;
        this.el.style.filter = 'alpha(opacity=' + val * 100 + ')';
        break;

      default:
        this.el.style[this.prop] = val + 'px';
        break;
    };
  };

  /**
    * @private
    * this is the tweening function
    */
  Animate.prototype._animate = function() {
    var that = this;
    this.now = new Date();
    this.diff = this.now - this.startTime;

    if (this.diff > this.time) {
      this._setStyle(this.to);

      if (this.callback) {
        this.callback.call(this);
      }
      clearInterval(this.timer);
      return;
    }

    this.percentage = (Math.floor((this.diff / this.time) * 100) / 100);
    this.val = (this.animDiff * this.percentage) + this.from;
    this._setStyle(this.val);
  };

  /**
    * @public
    * begins the animation
    */
  Animate.prototype.start = function() {
    var that = this;
    this.startTime = new Date();

    this.timer = setInterval(function() {
      that._animate.call(that);
    }, 15);
  };


  /**
    * @constructor
    * Widget Base for new instances of the Twitter search widget
    * @param {Object} opts the configuration options for the widget
    */
  TWTR.Widget = function(opts) {
    this.init(opts);
  };

  (function() {


    // Internal Namespace.
    var twttr = {};

    var isHttps = location.protocol.match(/https/);
    var httpsImageRegex = /^.+\/profile_images/;
    var httpsImageReplace = 'https://s3.amazonaws.com/twitter_production/profile_images';

    var matchUrlScheme = function(url) {
      return isHttps ? url.replace(httpsImageRegex, httpsImageReplace) : url;
    }

    // cache object for searching duplicates
    var reClassNameCache = {};

    // reusable regex for searching classnames
    var getClassRegEx = function(c) {

      // check to see if regular expression already exists
      var re = reClassNameCache[c];

      if (!re) {
        re = new RegExp('(?:^|\\s+)' + c + '(?:\\s+|$)');
        reClassNameCache[c] = re;
      }

      return re;
    };

    var getByClass = function(c, tag, root, apply) {
      var tag = tag || '*';
      var root = root || document;

      var nodes = [],
          elements = root.getElementsByTagName(tag),
          re = getClassRegEx(c);

      for (var i = 0, len = elements.length; i < len; ++i) {
        if (re.test(elements[i].className)) {
          nodes[nodes.length] = elements[i];

          if (apply) {
            apply.call(elements[i], elements[i]);
          }

        }
      }

      return nodes;
    };

    var browser = function() {
      var ua = navigator.userAgent;
      return {
        ie: ua.match(/MSIE\s([^;]*)/)
      };
    }();

    var byId = function(id) {
      if (typeof id == 'string') {
        return document.getElementById(id);
      }
      return id;
    };

    var trim = function(str) {
      return str.replace(/^\s+|\s+$/g, '')
    };

    var getViewportHeight = function() {
      var height = self.innerHeight; // Safari, Opera
      var mode = document.compatMode;
      if ((mode || browser.ie)) { // IE, Gecko
        height = (mode == 'CSS1Compat') ?
          document.documentElement.clientHeight : // Standards
          document.body.clientHeight; // Quirks
      }
      return height;
    };

    var getTarget = function(e, resolveTextNode) {
      var target = e.target || e.srcElement;
      return resolveTextNode(target);
    };

    var resolveTextNode = function(el) {
      try {
        if (el && 3 == el.nodeType) {
          return el.parentNode;
        } else {
          return el;
        }
      } catch (ex) { }
    };

    var getRelatedTarget = function(e) {
      var target = e.relatedTarget;
      if (!target) {
        if (e.type == 'mouseout') {
          target = e.toElement;
        }
        else if (e.type == 'mouseover') {
          target = e.fromElement;
        }
      }
      return resolveTextNode(target);
    };

    var insertAfter = function(el, reference) {
      reference.parentNode.insertBefore(el, reference.nextSibling);
    };

    var removeElement = function(el) {
      try {
        el.parentNode.removeChild(el);
      }
      catch (ex) { }
    };

    var getFirst = function(el) {
      return el.firstChild;
    };

    var withinElement = function(e) {
      var parent = getRelatedTarget(e);
      while (parent && parent != this) {
        try {
          parent = parent.parentNode;
        }
        catch(ex) {
          parent = this;
        }
      }
      if (parent != this) {
        return true;
      }
      return false;
    };

    var getStyle = function() {
      if (document.defaultView && document.defaultView.getComputedStyle) {
        return function(el, property) {
          var value = null;
          var computed = document.defaultView.getComputedStyle(el, '');
          if (computed) {
            value = computed[property];
          }
          var ret = el.style[property] || value;
          return ret;
        };
      }
      else if (document.documentElement.currentStyle && browser.ie) { // IE method
        return function(el, property) {
          var value = el.currentStyle ? el.currentStyle[property] : null;
          return (el.style[property] || value);
        };
      }
    }();

    /**
      * classes object
      * - has - add - remove
      */
    var classes = {
      has: function(el, c) {
        return new RegExp("(^|\\s)" + c + "(\\s|$)").test(byId(el).className);
      },

      add: function(el, c) {
        if (!this.has(el, c)) {
          byId(el).className = trim(byId(el).className) + ' ' + c;
        }
      },

      remove: function(el, c) {
        if (this.has(el, c)) {
          byId(el).className = byId(el).className.replace(new RegExp("(^|\\s)" + c + "(\\s|$)", "g"), "");
        }
      }
    };

    /**
      * basic x-browser event listener util
      * eg: events.add(element, 'click', fn);
      */
    var events = {
      add: function(el, type, fn) {
        if (el.addEventListener) {
          el.addEventListener(type, fn, false);
        }
        else {
          el.attachEvent('on' + type, function() {
            fn.call(el, window.event);
          });
        }
      },
      remove: function(el, type, fn) {
        if (el.removeEventListener) {
          el.removeEventListener(type, fn, false);
        }
        else {
          el.detachEvent('on' + type, fn);
        }
      }
    };

    var hex_rgb = function() {

      function HexToR(h) {
        return parseInt((h).substring(0,2),16);
      }
      function HexToG(h) {
        return parseInt((h).substring(2,4),16);
      }
      function HexToB(h) {
        return parseInt((h).substring(4,6),16);
      }

      return function(hex) {
        return [HexToR(hex), HexToG(hex), HexToB(hex)];
      };

    }();

    /**
      * core type detection on javascript objects
      */
    var is = {
      bool: function(b) {
        return typeof b === 'boolean';
      },

      def: function(o) {
        return !(typeof o === 'undefined');
      },

      number: function(n) {
        return typeof n === 'number' && isFinite(n);
      },
      string: function(s) {
        return typeof s === 'string';
      },

      fn: function(f) {
        return typeof f === 'function';
      },

      array: function(a) {
        if (a) {
          return is.number(a.length) && is.fn(a.splice);
        }
        return false;
      }
    };

    var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    var absoluteTime = function(s) {
      var d = new Date(s);
      if (browser.ie) {
        d = Date.parse(s.replace(/( \+)/, ' UTC$1'));
      }
      var ampm = '';
      var hour = function() {
        var h = d.getHours();
        if (h > 0 && h < 13) {
          ampm = 'am';
          return h;
        }
        else if (h < 1) {
          ampm = 'am';
          return 12;
        }
        else {
          ampm = 'pm';
          return h - 12;
        }
      }();
      var minutes = d.getMinutes();
      var seconds = d.getSeconds();
      function getRest() {
        var today = new Date();
        if (today.getDate() != d.getDate() || today.getYear() != d.getYear() || today.getMonth() != d.getMonth()) {
          return ' - ' + months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
        }
        else {
          return '';
        }
      }
      return hour + ':' + minutes + ampm + getRest();
    };

    /**
      * relative time calculator
      * @param {string} twitter date string returned from Twitter API
      * @return {string} relative time like "2 minutes ago"
      */
    var timeAgo = function(dateString) {
      var rightNow = new Date();
      var then = new Date(dateString);

      if (browser.ie) {
        // IE can't parse these crazy Ruby dates
        then = Date.parse(dateString.replace(/( \+)/, ' UTC$1'));
      }

      var diff = rightNow - then;

      var second = 1000,
          minute = second * 60,
          hour = minute * 60,
          day = hour * 24,
          week = day * 7;

      if (isNaN(diff) || diff < 0) {
        return ""; // return blank string if unknown
      }

      if (diff < second * 2) {
        // within 2 seconds
        return "right now";
      }

      if (diff < minute) {
        return Math.floor(diff / second) + " seconds ago";
      }

      if (diff < minute * 2) {
        return "about 1 minute ago";
      }

      if (diff < hour) {
        return Math.floor(diff / minute) + " minutes ago";
      }

      if (diff < hour * 2) {
        return "about 1 hour ago";
      }

      if (diff < day) {
        return  Math.floor(diff / hour) + " hours ago";
      }

      if (diff > day && diff < day * 2) {
        return "yesterday";
      }

      if (diff < day * 365) {
        return Math.floor(diff / day) + " days ago";
      }

      else {
        return "over a year ago";
      }

    };

    /**
      * The Twitalinkahashifyer!
      * http://www.dustindiaz.com/basement/ify.html
      * Eg:
      * ify.clean('your tweet text');
      */


    var ify = {
      link: function(tweet) {
        return tweet.replace(/\b(((https*\:\/\/)|www\.)[^\"\']+?)(([!?,.\)]+)?(\s|$))/g, function(link, m1, m2, m3, m4) {
          var http = m2.match(/w/) ? 'http://' : '';
          return '<a class="twtr-hyperlink" target="_blank" href="' + http + m1 + '">' + ((m1.length > 25) ? m1.substr(0, 24) + '...' : m1) + '</a>' + m4;
        });
      },

      at: function(tweet) {
        return tweet.replace(/\B[@＠]([a-zA-Z0-9_]{1,20})/g, function(m, username) {
          return '@<a target="_blank" class="twtr-atreply" href="http://twitter.com/' + username + '">' + username + '</a>';
        });
      },

      list: function(tweet) {
        return tweet.replace(/\B[@＠]([a-zA-Z0-9_]{1,20}\/\w+)/g, function(m, userlist) {
          return '@<a target="_blank" class="twtr-atreply" href="http://twitter.com/' + userlist + '">' + userlist + '</a>';
        });
      },

      hash: function(tweet) {
        return tweet.replace(/(^|\s+)#(\w+)/gi, function(m, before, hash) {
          return before + '<a target="_blank" class="twtr-hashtag" href="http://twitter.com/search?q=%23' + hash + '">#' + hash + '</a>';
        });
      },

      clean: function(tweet) {
        return this.hash(this.at(this.list(this.link(tweet))));
      }
    };


    /**
      * @constructor the classic twitter occasional job
      * @param {Function} job The job to execute upon each request
      * @param {Function} decay The deciding boolean method on whether to decay
      * @param {Int} interval The number in milliseconds to wait before executing
      */
    function Occasionally(job, decayFn, interval) {
      this.job = job;
      this.decayFn = decayFn;
      this.interval = interval;
      this.decayRate = 1;
      this.decayMultiplier = 1.25;
      this.maxDecayTime = 3 * 60 * 1000; // 3 minutes
    }

    Occasionally.prototype = {

      /**
        * @public
        * @return self
        * starts our occasional job
        */
      start: function() {
        this.stop().run();
        return this;
      },

      /**
        * @public
        * @return self
        * stops the occasional job
        */
      stop: function() {
        if (this.worker) {
          window.clearTimeout(this.worker);
        }
        return this;
      },

      /**
        * @private
        */
      run: function() {
        var that = this;
        this.job(function() {
          // running our decayer callback
          that.decayRate = that.decayFn() ? Math.max(1, that.decayRate / that.decayMultiplier)
                                          : that.decayRate * that.decayMultiplier;

          var expire = that.interval * that.decayRate;
          expire = (expire >= that.maxDecayTime) ? that.maxDecayTime : expire;
          expire = Math.floor(expire);

          that.worker = window.setTimeout(
            function () {
              that.run.call(that);
            },
            expire
          );

        });
      },

      /**
        * @public
        * @return self
        * stops occasional job and resets object
        */
      destroy: function() {
        this.stop();
        this.decayRate = 1;
        return this;
      }
    };

    /**
      * @Constructor runs a timer on an array passing back
      *              the next needle on each interval
      * @param haystack {Array}
      * @param time {Int} time in ms
      * @param loop {Bool} does this continue forever?
      * @param callback {Function} method that is passed back a needle for each interval
      */
    function IntervalJob(time, loop, callback) {
      this.time = time || 6000;
      this.loop = loop || false;
      this.repeated = 0;
      this.callback = callback;
      this.haystack = [];
    };

    IntervalJob.prototype = {

      set: function(haystack) {
        this.haystack = haystack;
      },

      add: function(needle) {
        this.haystack.unshift(needle);
      },

      /**
        * @public
        * @return self
        * begins the interval job
        */
      start: function() {
        if (this.timer) {
          return this;
        }
        this._job();
        var that = this;
        this.timer = setInterval(
          function() {
            that._job.call(that);
          }, this.time
        );

        return this;
      },

      /**
        * @public
        * @return self
        * stops the interval
        */
      stop: function() {
        if (this.timer) {
          window.clearInterval(this.timer);
          this.timer = null;
        }

        return this;
      },

      /**
        * @private
        */
      _next: function() {
        var old = this.haystack.shift();
        if (old && this.loop) {
          this.haystack.push(old);
        }
        return old || null;
      },

      /**
        * @private
        */
      _job: function() {
        var next = this._next();
        if (next) {
          this.callback(next);
        }

        return this;
      }

    };

    function Tweet(tweet) {
      function showPopular() {
        if (tweet.needle.metadata && tweet.needle.metadata.result_type && tweet.needle.metadata.result_type == 'popular') {
          return '<span class="twtr-popular">' + tweet.needle.metadata.recent_retweets + '+ recent retweets</span>';
        } else {
          return '';
        }
      }

      var html = '<div class="twtr-tweet-wrap"> \
        <div class="twtr-avatar"> \
          <div class="twtr-img"><a target="_blank" href="http://twitter.com/' + tweet.user + '"><img alt="' + tweet.user + ' profile" src="' + matchUrlScheme(tweet.avatar) + '"></a></div> \
        </div> \
        <div class="twtr-tweet-text"> \
          <p> \
            <a target="_blank" href="http://twitter.com/' + tweet.user + '" class="twtr-user">' + tweet.user + '</a> ' + tweet.tweet + ' \
            <em>\
            <a target="_blank" class="twtr-timestamp" time="' + tweet.timestamp + '" href="http://twitter.com/' + tweet.user + '/status/' + tweet.id + '">' + tweet.created_at + '</a> &middot;\
            <a target="_blank" class="twtr-reply" href="http://twitter.com/?status=@' + tweet.user + '%20&in_reply_to_status_id=' + tweet.id + '&in_reply_to=' + tweet.user + '">reply</a> \
            </em> ' + showPopular() + ' \
          </p> \
        </div> \
      </div>';

      var div = document.createElement('div');

      div.id = 'tweet-id-' + ++Tweet._tweetCount;
      div.className = 'twtr-tweet';
      div.innerHTML = html;
      this.element = div;
    };

    // static count so all tweets (even on multiple inst widgets) will have unique ids
    Tweet._tweetCount = 0;

      twttr.loadStyleSheet = function(url, widgetEl) {
        if (!TWTR.Widget.loadingStyleSheet) {
          TWTR.Widget.loadingStyleSheet = true;
          var linkElement = document.createElement('link');
          linkElement.href = url;
          linkElement.rel = 'stylesheet';
          linkElement.type = 'text/css';
          document.getElementsByTagName('head')[0].appendChild(linkElement);
          var timer = setInterval(function() {
            var style = getStyle(widgetEl, 'position');
            if (style == 'relative') {
              clearInterval(timer);
              timer = null;
              TWTR.Widget.hasLoadedStyleSheet = true;
            }
          }, 50);
        }
      };

    (function() {

      var isLoaded = false;
      twttr.css = function(rules) {
        var styleElement = document.createElement('style');
        styleElement.type = 'text/css';
        if (browser.ie) {
          styleElement.styleSheet.cssText = rules;
        }
        else {
          var frag = document.createDocumentFragment();
          frag.appendChild(document.createTextNode(rules));
          styleElement.appendChild(frag);
        }
        function append() {
          document.getElementsByTagName('head')[0].appendChild(styleElement);
        }

        // oh IE we love you.
        // this is needed because you can't modify document body when page is loading
        if (!browser.ie || isLoaded) {
          append();
        }
        else {
          window.attachEvent('onload', function() {
            isLoaded = true;
            append();
          });
        }
      };
    })();


    TWTR.Widget.isLoaded = false;
    TWTR.Widget.loadingStyleSheet = false;
    TWTR.Widget.hasLoadedStyleSheet = false;
    TWTR.Widget.WIDGET_NUMBER = 0;
    TWTR.Widget.matches = {
      mentions: /^@[a-zA-Z0-9_]{1,20}\b/,
      any_mentions: /\b@[a-zA-Z0-9_]{1,20}\b/
    };

    TWTR.Widget.jsonP = function(url, callback) {
      var script = document.createElement('script');
      var head = document.getElementsByTagName('head')[0];
      script.type = 'text/javascript';
      script.src = url;
      head.insertBefore(script, head.firstChild)
      callback(script);
      return script;
    };

    TWTR.Widget.prototype = function() {

      var http = isHttps ? 'https://' : 'http://';
      var domain = window.location.hostname.match(/twitter\.com/) ?
          (window.location.hostname + ":" + window.location.port) : 'twitter.com';
      var base = http + 'search.' + domain + '/search.';

      var profileBase = http + 'api.' + domain + '/1/statuses/user_timeline.';
      var favBase = http + domain + '/favorites/';
      var listBase = http + domain + '/';
      var occasionalInterval = 25000; // 25 seconds
      var defaultAvatar = isHttps ? 'https://twitter-widgets.s3.amazonaws.com/j/1/default.gif' : 'http://widgets.twimg.com/j/1/default.gif';

      return {
        init: function(opts) {
          var that = this;
          // first, define public callback for this widget
          this._widgetNumber = ++TWTR.Widget.WIDGET_NUMBER;
          TWTR.Widget['receiveCallback_' + this._widgetNumber] = function(resp) {
            that._prePlay.call(that, resp);
          };
          this._cb = 'TWTR.Widget.receiveCallback_' + this._widgetNumber;
          this.opts = opts;
          this._base = base;
          this._isRunning = false;
          this._hasOfficiallyStarted = false;
          this._hasNewSearchResults = false;
          this._rendered = false;
          this._profileImage = false;
          this._isCreator = !!opts.creator;

          this._setWidgetType(opts.type);

          this.timesRequested = 0;
          this.runOnce = false;
          this.newResults = false;
          this.results = [];
          this.jsonMaxRequestTimeOut = 19000;
          this.showedResults = [];
          this.sinceId = 1;
          this.source = 'TWITTERINC_WIDGET';
          this.id = opts.id || 'twtr-widget-' + this._widgetNumber;

          this.tweets = 0;
          this.setDimensions(opts.width, opts.height);
          this.interval = opts.interval || 6000;
          this.format = 'json';
          this.rpp = opts.rpp || 50;
          this.subject = opts.subject || '';
          this.title = opts.title || '';
          this.setFooterText(opts.footer);

          this.setSearch(opts.search);
          this._setUrl();
          this.theme = opts.theme ? opts.theme : this._getDefaultTheme();

          if (!opts.id) {
            document.write('<div class="twtr-widget" id="' + this.id + '"></div>');
          }
          this.widgetEl = byId(this.id);
          if (opts.id) {
            classes.add(this.widgetEl, 'twtr-widget');
          }

          if (opts.version >= 2 && !TWTR.Widget.hasLoadedStyleSheet) {
            if (isHttps) {
              twttr.loadStyleSheet('https://twitter-widgets.s3.amazonaws.com/j/2/widget.css', this.widgetEl);
            } else if (opts.creator) {
              twttr.loadStyleSheet('/stylesheets/widgets/widget.css', this.widgetEl);
            } else {
              // twttr.loadStyleSheet('http://localhost.twitter.com:3000/stylesheets/widgets/widget.css', this.widgetEl);
              twttr.loadStyleSheet('http://widgets.twimg.com/j/2/widget.css', this.widgetEl);
            }
          }

          this.occasionalJob = new Occasionally(
            function(decay) {
              that.decay = decay;
              that._getResults.call(that);
            },

            function() {
              return that._decayDecider.call(that);
            },

            occasionalInterval
          );

          this._ready = is.fn(opts.ready) ? opts.ready : function() { };

          // preset features
          this._isRelativeTime = true;
          this._tweetFilter = false;
          this._avatars = true;
          this._isFullScreen = false;
          this._isLive = true;
          this._isScroll = false;
          this._loop = true;
          this._showTopTweets = (this._isSearchWidget) ? true : false;
          this._behavior = 'default';
          this.setFeatures(this.opts.features);

          this.intervalJob = new IntervalJob(this.interval, this._loop, function(tweet) {
            that._normalizeTweet(tweet);
          });

          return this;
        },

        /**
          * @public
          * @param {Int} w - width for widget
          * @param {Int} h - height for widget
          * @return self
          */
        setDimensions: function(w, h) {
          this.wh = (w && h) ? [w, h] : [250, 300]; // default w/h if none provided
          if (w == 'auto' || w == '100%') {
            this.wh[0] = '100%';
          } else {
            this.wh[0] = ((this.wh[0] < 150) ? 150 : this.wh[0]) + 'px'; // min width is 150
          }
          this.wh[1] = ((this.wh[1] < 100) ? 100 : this.wh[1]) + 'px'; // min height is 100
          return this;
        },

        setRpp: function(rpp) {
          var rpp = parseInt(rpp);
          this.rpp = (is.number(rpp) && (rpp > 0 && rpp <= 100)) ? rpp : 30;
          return this;
        },

        /**
          * @private
          * @param {String} type the kind of widget you're instantiating
          * @return self
          */
        _setWidgetType: function(type) {
          this._isSearchWidget = false,
          this._isProfileWidget = false,
          this._isFavsWidget = false,
          this._isListWidget = false;
          switch(type) {
            case 'profile':
              this._isProfileWidget = true;
              break;
            case 'search':
              this._isSearchWidget = true,
              this.search = this.opts.search;
              break;
            case 'faves':
            case 'favs':
              this._isFavsWidget = true;
              break;
            case 'list':
            case 'lists':
              this._isListWidget = true;
              break;
          };
          return this;
        },

        /**
          * @public
          * @param {object}
          * @return self
          * allows implementer to set features which include:
          * - avatars {bool}
          * - timestamp {bool}
          * - hashtags {bool}
          * setting any of the previous properties will appropriately hide/show that feature
          * @example
          * WidgetInstance.setFeatures({ fullscreen: true, avatars: true, timestamp: false, hashtags: false }).render().start();
          * @return self
          */
        setFeatures: function(features) {

          if (features) {
            if (is.def(features.filters)) {
              this._tweetFilter = features.filters;
            }
            if (is.def(features.dateformat)) {
              this._isRelativeTime = !!(features.dateformat !== 'absolute')
            }

            if (is.def(features.fullscreen) && is.bool(features.fullscreen)) {
              if (features.fullscreen) {
                this._isFullScreen = true;
                this.wh[0] = '100%';
                this.wh[1] = (getViewportHeight() - 90) + 'px';
                var that = this;
                events.add(window, 'resize', function(e) {
                  that.wh[1] = getViewportHeight();
                  that._fullScreenResize();
                });
              }
            }

            if (is.def(features.loop) && is.bool(features.loop)) {
              this._loop = features.loop;
            }

            if (is.def(features.behavior) && is.string(features.behavior)) {
              switch (features.behavior) {
                case 'all':
                  this._behavior = 'all';
                  break;
                case 'preloaded':
                  this._behavior = 'preloaded';
                  break;
                default:
                  this._behavior = 'default';
                  break;
              };
            }

            if (is.def(features.toptweets) && is.bool(features.toptweets)) {
              this._showTopTweets = features.toptweets;
              var showTopTweet = (this._showTopTweets) ? 'inline-block' : 'none';
              twttr.css('#' + this.id + ' .twtr-popular { display: ' + showTopTweet + '; }');
            }

            if (!is.def(features.toptweets)) {
              this._showTopTweets = true;
              var showTopTweet = (this._showTopTweets) ? 'inline-block' : 'none';
              twttr.css('#' + this.id + ' .twtr-popular { display: ' + showTopTweet + '; }');
            }

            if (is.def(features.avatars) && is.bool(features.avatars)) {

              if (!features.avatars) {
                twttr.css('#' + this.id + ' .twtr-avatar, #' + this.id + ' .twtr-user { display: none; } ' +
                '#' + this.id + ' .twtr-tweet-text { margin-left: 0; }');
                this._avatars = false;
              } else {
                var margin = (this._isFullScreen) ? '90px' : '40px';
                twttr.css('#' + this.id + ' .twtr-avatar { display: block; } #' + this.id + ' .twtr-user { display: inline; } ' +
                '#' + this.id + ' .twtr-tweet-text { margin-left: ' + margin + '; }');
                this._avatars = true;
              }

            }
            else {
              if (this._isProfileWidget) {
                this.setFeatures({ avatars: false });
                this._avatars = false;
              }
              else {
                this.setFeatures({ avatars: true });
                this._avatars = true;
              }
            }

            if (is.def(features.hashtags) && is.bool(features.hashtags)) {
              (!features.hashtags) ?
                  twttr.css('#' + this.id + ' a.twtr-hashtag { display: none; }') : '';
            }

            if (is.def(features.timestamp) && is.bool(features.timestamp)) {
              var display = features.timestamp ? 'block' : 'none';
              twttr.css('#' + this.id + ' em { display: ' + display + '; }');
            }

            if (is.def(features.live) && is.bool(features.live)) {
              this._isLive = features.live;
            }
            if (is.def(features.scrollbar) && is.bool(features.scrollbar)) {
              this._isScroll = features.scrollbar;
            }

          }

          else {

            if (this._isProfileWidget) {
              this.setFeatures({ avatars: false });
              this._avatars = false;
            }
            if (this._isProfileWidget || this._isFavsWidget) {
              this.setFeatures({ behavior: 'all' });
            }

          }
          return this;
        },

        /**
          * @private
          * @param e Event listener for window resizing
          */
        _fullScreenResize: function() {
          var timeline = getByClass('twtr-timeline', 'div', document.body, function(el) {
            el.style.height = (getViewportHeight() - 90) + 'px';
          });
        },

        /**
          * @public facade
          * @param {int} in seconds
          * convenience method for setting time between each tweet render
          * @return self
          */
        setTweetInterval: function(interval) {
          this.interval = interval;
          return this;
        },

        /**
          * @public
          * @param {string} url
          * sets a url base for the JSONP call
          * useful for future API implementations or moderation platforms
          * @return self
          */
        setBase: function(b) {
          this._base = b;
          return this;
        },

        /**
          * @public
          * @param {string} username
          * used to distinguish a "favs" widget
          * @return self
          */
        setUser: function(username, opt_realname) {
          this.username = username;
          this.realname = opt_realname || ' ';
          if (this._isFavsWidget) {
            this.setBase(favBase + username + '.');
          }
          else if (this._isProfileWidget) {
            this.setBase(profileBase + this.format + '?screen_name=' + username);
          }
          this.setSearch(' ');
          return this;
        },

        /**
          * @public
          * @param {string} username - the owner of the list
          * @param {string} listName - the name of the list
          * return self
          */
        setList: function(username, listname) {
          this.listslug = listname.replace(/ /g, '-').toLowerCase();
          this.username = username;
          this.setBase(listBase + username + '/lists/' + this.listslug + '/statuses.');
          this.setSearch(' ');
          return this;
        },

        /**
          * @public
          * @param {string}
          * sets the profile image source to display in the widget
          * @return self
          */
        setProfileImage: function(url) {
          this._profileImage = url;
          this.byClass('twtr-profile-img', 'img').src = matchUrlScheme(url);
          this.byClass('twtr-profile-img-anchor', 'a').href = 'http://twitter.com/' + this.username;
          return this;
        },

        /**
          * @public
          * @param {string}
          * sets the main title to display at top of widget
          * @return self
          */
        setTitle: function(title) {
          this.title = title;
          this.widgetEl.getElementsByTagName('h3')[0].innerHTML = this.title;
          return this;
        },

        /**
          * @public
          * @param {string}
          * sets the main caption to display at top of widget (below title)
          * @return self
          */
        setCaption: function(subject) {
          this.subject = subject;
          this.widgetEl.getElementsByTagName('h4')[0].innerHTML = this.subject;
          return this;
        },

        /**
          * @public
          * @param {string}
          * sets the footer text
          * @return self
          */
        setFooterText: function(s) {
          this.footerText = (is.def(s) && is.string(s)) ? s : 'Join the conversation';
          if (this._rendered) {
            this.byClass('twtr-join-conv', 'a').innerHTML = this.footerText;
          }
          return this;
        },

        /**
          * @public
          * @param {string}
          * @return self
          * does double time. sets the search terms, and sets the appropriate
          * hyper reference on bottom anchor if widget has been rendered
          */
        setSearch: function(s) {
          this.searchString = s || '';
          this.search = encodeURIComponent(this.searchString);
          this._setUrl();
          if (this._rendered) {
            var anchor = this.byClass('twtr-join-conv', 'a');
            anchor.href = 'http://twitter.com/' + this._getWidgetPath();
          }

          return this;
        },

        _getWidgetPath: function() {
          if (this._isProfileWidget) {
            return this.username;
          }
          else if (this._isFavsWidget) {
            return this.username + '/favorites';
          }
          else if (this._isListWidget) {
            return this.username + '/lists/' + this.listslug;
          }
          else {
            return '#search?q=' + this.search;
          }
        },

        /**
          * @private
          * @return self
          * creates the proper URL to request data via JSONP
          */
        _setUrl: function() {
          var that = this;

          function cacheBust() {
            // chrome i hate your caching
            return '&' + (+new Date) + '=cachebust';
          }

          function showSince() {
            return (that.sinceId == 1) ? '' : '&since_id=' + that.sinceId + '&refresh=true';
          }

          if (this._isProfileWidget) {
            this.url = this._base + '&callback=' + this._cb +
                       '&include_rts=true' +
                       '&count=' + this.rpp + showSince() + '&clientsource=' + this.source;
          }

          else if (this._isFavsWidget || this._isListWidget) {
            this.url = this._base + this.format + '?callback=' + this._cb + showSince() +
                       '&include_rts=true' +
                       '&clientsource=' + this.source;
          }

          else {
            this.url = this._base + this.format + '?q=' + this.search +
                       '&include_rts=true' +
                       '&callback=' + this._cb +
                       '&rpp=' + this.rpp + showSince() + '&clientsource=' + this.source;
            if (!this.runOnce) {
              this.url += '&result_type=mixed';
            }
          }
          this.url += cacheBust();
          return this;
        },

        /**
          * @private
          */
        _getRGB: function(hex) {
          return hex_rgb(hex.substring(1, 7));
        },

        /**
          * @public
          * @param {object}
          * @param {boolean} important whether to be important style
          * @return self
          * allows implementer to set their own theme.
          * theme object can be passed into contructor, or set here.
          * defaults to default theme properties when not set
          */
        setTheme: function(o, important) {
          var that = this;
          var imp = ' !important';

          var onCreator = ((window.location.hostname.match(/twitter\.com/)) && (window.location.pathname.match(/goodies/)));
          if (important || onCreator) {
            imp = '';
          }
          this.theme = {
            shell: {
              background: function() {
                return o.shell.background || that._getDefaultTheme().shell.background;
              }(),

              color: function() {
                return o.shell.color || that._getDefaultTheme().shell.color;
              }()
            },

            tweets: {
              background: function() {
                return o.tweets.background || that._getDefaultTheme().tweets.background;
              }(),

              color: function() {
                return o.tweets.color || that._getDefaultTheme().tweets.color;
              }(),

              links: function() {
                return o.tweets.links || that._getDefaultTheme().tweets.links;
              }()
            }
          };

          var style = '#' + this.id + ' .twtr-doc, \
                     #' + this.id + ' .twtr-hd a, \
                     #' + this.id + ' h3, \
                     #' + this.id + ' h4, \
                     #' + this.id + ' .twtr-popular {\
            background-color: ' + this.theme.shell.background + imp + ';\
            color: ' + this.theme.shell.color + imp + ';\
          }\
          #' + this.id + ' .twtr-popular {\
            color: ' + this.theme.tweets.color + imp + ';\
            background-color: rgba(' + this._getRGB(this.theme.shell.background) + ', .3)' + imp + ';\
          }\
          #' + this.id + ' .twtr-tweet a {\
            color: ' + this.theme.tweets.links + imp + ';\
          }\
          #' + this.id + ' .twtr-bd, #' + this.id + ' .twtr-timeline i a, \
          #' + this.id + ' .twtr-bd p {\
            color: ' + this.theme.tweets.color + imp + ';\
          }\
          #' + this.id + ' .twtr-new-results, \
          #' + this.id + ' .twtr-results-inner, \
          #' + this.id + ' .twtr-timeline {\
            background: ' + this.theme.tweets.background + imp + ';\
          }';

          if (browser.ie) {
            style += '#' + this.id + ' .twtr-tweet { background: ' + this.theme.tweets.background + imp + '; }';
          }

          twttr.css(style);
          return this;
        },

        /**
          * @public
          * @param {string} classname
          * @param {string} tagname
          * @param optional {bool} whether to return collection or defaults to first match
          * @return HTML Element || Array HTML Elements
          * helper to get elements by classname based on the widget being the context
          */
        byClass: function(c, tag, opt_all) {
          var collection = getByClass(c, tag, byId(this.id));
          return (opt_all) ? collection : collection[0];
        },

        /**
          * @public
          * @return self
          * renders the widget onto an HTML page
          */
        render: function() {
          var that = this;

          if (!TWTR.Widget.hasLoadedStyleSheet) {
            window.setTimeout(function() {
              that.render.call(that);
            }, 50);
            return this;
          }

          this.setTheme(this.theme, this._isCreator);

          if (this._isProfileWidget) {
            classes.add(this.widgetEl, 'twtr-widget-profile');
          }

          if (this._isScroll) {
            classes.add(this.widgetEl, 'twtr-scroll')
          }
          if (!this._isLive && !this._isScroll) {
            this.wh[1] = 'auto';
          }
          if (this._isSearchWidget && this._isFullScreen) {
            document.title = 'Twitter search: ' + escape(this.searchString);
          }
          this.widgetEl.innerHTML = this._getWidgetHtml();
          var timeline = this.byClass('twtr-timeline', 'div');
          if (this._isLive && !this._isFullScreen) {
            var over = function(e) {
              if (that._behavior === 'all') {
                return;
              }
              if (withinElement.call(this, e)) {
                that.pause.call(that);
              }
            };
            var out = function(e) {
              if (that._behavior === 'all') {
                return;
              }
              if (withinElement.call(this, e)) {
                that.resume.call(that);
              }
            };

            this.removeEvents = function() {
              events.remove(timeline, 'mouseover', over);
              events.remove(timeline, 'mouseout', out);
            };
            events.add(timeline, 'mouseover', over);
            events.add(timeline, 'mouseout', out);
          }
          this._rendered = true;
          // call the ready handler
          this._ready();
          return this;
        },

        /**
          * empty placeholder for removing events
          * on live widgets
          */
        removeEvents: function() { },

        /**
          * @private
          * @return {object} theme
          */
        _getDefaultTheme: function() {
          return {
            shell: {
              background: '#8ec1da',
              color: '#ffffff'
            },

            tweets: {
              background: '#ffffff',
              color: '#444444',
              links: '#1985b5'
            }

          };
        },

        /**
          * @private
          * @return {string}
          * builds an HTML string that represents the widget chrome
          */
        _getWidgetHtml: function() {
          var that = this;

          function getHeader() {
            if (that._isProfileWidget) {
              return '<a target="_blank" href="http://twitter.com/" class="twtr-profile-img-anchor"><img alt="profile" class="twtr-profile-img" src="' + defaultAvatar + '"></a>\
                      <h3></h3>\
                      <h4></h4>';
            }
            else {
              return '<h3>' + that.title + '</h3><h4>' + that.subject + '</h4>';
            }
          }

          function isFull() {
            return that._isFullScreen ? ' twtr-fullscreen' : '';
          }

          var logo = isHttps ? 'https://twitter-widgets.s3.amazonaws.com/i/widget-logo.png' : 'http://widgets.twimg.com/i/widget-logo.png';

          if (this._isFullScreen) {
            logo = 'https://twitter-widgets.s3.amazonaws.com/i/widget-logo-fullscreen.png';
          }
/*
<div class="twtr-doc' + isFull() + '" style="width: ' + this.wh[0] + ';">\
            <div class="twtr-hd">' + getHeader() + ' \
            </div>\
			
			
            <div class="twtr-ft">\
              <div><a target="_blank" href="http://twitter.com"><img alt="" src="' + logo + '"></a>\
                <span><a target="_blank" class="twtr-join-conv" style="color:' + this.theme.shell.color + '" href="http://twitter.com/' + this._getWidgetPath() + '">' + this.footerText + '</a></span>\
              </div>\
            </div>\
*/
          var html = '<div class="twtr-bd">\
              <div class="twtr-timeline" style="height: ' + this.wh[1] + ';">\
                <div class="twtr-tweets">\
                  <div class="twtr-reference-tweet"></div>\
                  <!-- tweets show here -->\
                </div>\
              </div>\
            </div>\
          </div>';

          return html;
        },

        /**
          * @private
          * @return self
          * puts the tweet in the dom
          */
        _appendTweet: function(el) {
          this._insertNewResultsNumber();
          insertAfter(el, this.byClass('twtr-reference-tweet', 'div'));
          return this;
        },

        /**
          * @private
          * @return self
          * slides in a rendered tweet
          */
        _slide: function(el) {
          var that = this;
          var height = getFirst(el).offsetHeight;
          if (this.runOnce) {
            new Animate(el, 'height', {
              from: 0,
              to: height,
              time: 500,
              callback: function() {
                that._fade.call(that, el);
              }
            }).start();
          }
          return this;
        },

        /**
          * @private
          * @return self
          * fades in a rendered tweet
          */
        _fade: function(el) {
          var that = this;

          if (Animate.canTransition) {
            el.style.webkitTransition = 'opacity 0.5s ease-out';
            el.style.opacity = 1;
            return this;
          }
          new Animate(el, 'opacity', {
            from: 0,
            to: 1,
            time: 500
          }).start();
          return this;
        },

        /**
          * @private
          * @return self
          * removes the last tweet if it is offscreen
          */
        _chop: function() {
          if (this._isScroll) {
            return this;
          }
          var tweets = this.byClass('twtr-tweet', 'div', true);
          var resultUpdates = this.byClass('twtr-new-results', 'div', true);
          if (tweets.length) {
            for (var i=tweets.length - 1; i >=0; i--) {
              var tweet = tweets[i];
              var top = parseInt(tweet.offsetTop);
              if (top > parseInt(this.wh[1])) {
                removeElement(tweet);
              } else {
                break;
              }
            }


            if (resultUpdates.length > 0) {
              var result = resultUpdates[resultUpdates.length - 1];
              var resultTop = parseInt(result.offsetTop);
              if (resultTop > parseInt(this.wh[1])) {
                removeElement(result);
              }
            }
          }

          return this;
        },

        /**
          * @private
          * @return self
          * Big Facade for chop, append, slide, and fade
          */
        _appendSlideFade: function(opt_element) {
          var el = opt_element || this.tweet.element;
          this
            ._chop()
            ._appendTweet(el)
            ._slide(el);
          return this;
        },

        /**
          * @private
          * @return self
          * generates the HTML for a single tweet item
          */
        _createTweet: function(o) {
          o.timestamp = o.created_at;
          o.created_at = this._isRelativeTime ? timeAgo(o.created_at) : absoluteTime(o.created_at);
          this.tweet = new Tweet(o);
          if (this._isLive && this.runOnce) {
            this.tweet.element.style.opacity = 0;
            this.tweet.element.style.filter = 'alpha(opacity:0)';
            this.tweet.element.style.height = '0';
          }
          return this;
        },

        /**
          * @private
          * @param {Function} callback function that receives the results
          * makes a jsonP call to twitter.com
          */
        _getResults: function() {
          var that = this;

          this.timesRequested++;
          this.jsonRequestRunning = true;

          this.jsonRequestTimer = window.setTimeout(function() {

            if (that.jsonRequestRunning) {
              clearTimeout(that.jsonRequestTimer);
              that.jsonRequestTimer = null;
            }

            that.jsonRequestRunning = false;
            removeElement(that.scriptElement);
            that.newResults = false;
            that.decay();
          }, this.jsonMaxRequestTimeOut);
          TWTR.Widget.jsonP(that.url, function(script) {
            that.scriptElement = script;
          });

        },

        /**
          * @public
          * @return self
          * clears out the tweet space. used internally,
          * but free to use publicly
          */
        clear: function() {
          var tweets = this.byClass('twtr-tweet', 'div', true);
          var results = this.byClass('twtr-new-results', 'div', true);
          tweets = tweets.concat(results);
          each(tweets, function(el) {
            removeElement(el);
          });

          return this;
        },

        _sortByMagic: function(results) {
          var that = this;
          if (this._tweetFilter) {
            if (this._tweetFilter.negatives) {
              results = results.filter(function(el) {
                if (!that._tweetFilter.negatives.test(el.text)) {
                  return el;
                }
              });
            }
            if (this._tweetFilter.positives) {
              results = results.filter(function(el) {
                if (that._tweetFilter.positives.test(el.text)) {
                  return el;
                }
              });
            }
          }
          switch (this._behavior) {
            case 'all':
              this._sortByLatest(results);
              break;
            case 'preloaded':
            default:
              this._sortByDefault(results);
              break;
          };

          if (this._isLive && this._behavior !== 'all') {
            this.intervalJob.set(this.results);
            this.intervalJob.start();
          }

          return this;
        },

        /**
          * @private
          * @return results
          * puts the toptweets for search widget at the top
          */
        _loadTopTweetsAtTop: function(results) {
          var regular = [],
              popular = [],
              arr = [];
          // top tweets
          each(results, function(el) {
            if (el.metadata && el.metadata.result_type && el.metadata.result_type == 'popular') {
              popular.push(el);
            } else {
              regular.push(el);
            }
          });
          var result = popular.concat(regular);
          return result;
        },

        _sortByLatest: function(results) {
          this.results = results;
          this.results = this.results.slice(0, this.rpp);
          this.results = this._loadTopTweetsAtTop(this.results);
          this.results.reverse();
          return this;
        },

        /**
          * @private
          * @return self
          * default sorting method which tracks views and loops
          */
        _sortByDefault: function(results) {
          var that = this;

          var getDater = function(dateString) {
            return new Date(dateString).getTime();
          };

          // merge new results with old
          this.results.unshift.apply(this.results, results);

          each(this.results, function(el) {
            if (!el.views) {
              el.views = 0;
            }
          });

          // sort by date
          this.results.sort(function(a, b) {
            if (getDater(a.created_at) > getDater(b.created_at)) {
              return -1;
            }
            else if (getDater(a.created_at) < getDater(b.created_at)) {
              return 1;
            }
            else {
              return 0;
            }
          });

          // now cut off the oldest
          this.results = this.results.slice(0, this.rpp);

          this.results = this._loadTopTweetsAtTop(this.results);
          var foo = this.results;

          // now sort by views
          this.results = this.results.sort(function(a, b) {
            if (a.views < b.views) {
              return -1;
            }
            else if (a.views > b.views) {
              return 1;
            }
            return 0;
          });

          if (!this._isLive) {
            this.results.reverse();
          }

        },

        /**
          * @private
          * @method prePlay does a pre-check against last result.
          * @param resp the JSON response from twitter JsonP API
          */
        _prePlay: function(resp) {
          if (this.jsonRequestTimer) {
            clearTimeout(this.jsonRequestTimer);
            this.jsonRequestTimer = null;
          }

          if (!browser.ie) {
            removeElement(this.scriptElement);
          }

          if (resp.error) {
            this.newResults = false;
          }

          else if (resp.results && resp.results.length > 0) {
            this.response = resp;

            this.newResults = true;
            this.sinceId = resp.max_id_str;

            this._sortByMagic(resp.results);
            if (this.isRunning()) {
              this._play();
            }

          }

          else if ((this._isProfileWidget || this._isFavsWidget || this._isListWidget) && is.array(resp) && resp.length) {

            this.newResults = true;

            if (!this._profileImage && this._isProfileWidget) {
              var name = resp[0].user.screen_name;
              //this.setProfileImage(resp[0].user.profile_image_url);
              //this.setTitle(resp[0].user.name);
              //this.setCaption('<a target="_blank" href="http://twitter.com/' + name + '">' + name + '</a>');
            }

            this.sinceId = resp[0].id_str;

            this._sortByMagic(resp);

            if (this.isRunning()) {
              this._play();
            }

          }

          else {
            this.newResults = false;
          }

          this._setUrl();
          if (this._isLive) {
            this.decay();
          }

        },

        /**
          * @private
          * gets the ball rolling with a new widget
          * and resets the interval job
          */
        _play: function() {
          var that = this;
          if (this.runOnce) {
            this._hasNewSearchResults = true;
          }

          if (this._avatars) {
            this._preloadImages(this.results);
          }
          if (this._isRelativeTime && (this._behavior == 'all' || this._behavior == 'preloaded')) {
            each(this.byClass('twtr-timestamp', 'a', true), function(el) {
              el.innerHTML = timeAgo(el.getAttribute('time'));
            });
          }
          if (!this._isLive || this._behavior == 'all' || this._behavior == 'preloaded') {
            each(this.results, function(needle) {
              if (needle.retweeted_status) {
                needle = needle.retweeted_status;
              }

              if (that._isProfileWidget) {
                needle.from_user = needle.user.screen_name;
                needle.profile_image_url = needle.user.profile_image_url;
              }

              if (that._isFavsWidget || that._isListWidget) {
                needle.from_user = needle.user.screen_name;
                needle.profile_image_url = needle.user.profile_image_url;
              }

              needle.id = needle.id_str;

              that._createTweet({
                id: needle.id,
                user: needle.from_user,
                tweet: ify.clean(needle.text),
                avatar: needle.profile_image_url,
                created_at: needle.created_at,
                needle: needle
              });
              var el = that.tweet.element;
              (that._behavior == 'all') ? that._appendSlideFade(el) : that._appendTweet(el);
            });

            if (this._behavior != 'preloaded') {
              return this;
            }

          }

          return this;
        },

        _normalizeTweet: function(needle) {
          var that = this;
          needle.views++;

          if (this._isProfileWidget) {
            needle.from_user = that.username;
            needle.profile_image_url = needle.user.profile_image_url;
          }

          if (this._isFavsWidget || this._isListWidget) {
            needle.from_user = needle.user.screen_name;
            needle.profile_image_url = needle.user.profile_image_url;
          }

          if (this._isFullScreen) {
            needle.profile_image_url = needle.profile_image_url.replace(/_normal\./, '_bigger.');
          }

          this._createTweet({
            id: needle.id,
            user: needle.from_user,
            tweet: ify.clean(needle.text),
            avatar: needle.profile_image_url,
            created_at: needle.created_at,
            needle: needle
          })._appendSlideFade();

        },

        _insertNewResultsNumber: function() {
          if (!this._hasNewSearchResults) {
            this._hasNewSearchResults = false;
            return;
          }

          if (this.runOnce && this._isSearchWidget) {
            var newResultsTotal = this.response.total > this.rpp ? this.response.total : this.response.results.length;
            var plural = newResultsTotal > 1 ? 's' : '';
            var moreThan = (this.response.warning && this.response.warning.match(/adjusted since_id/)) ? 'more than' : '';
            var el = document.createElement('div');
            classes.add(el, 'twtr-new-results');
            el.innerHTML = '<div class="twtr-results-inner"> &nbsp; </div>' +
                           '<div class="twtr-results-hr"> &nbsp; </div><span>' + moreThan + ' <strong>' + newResultsTotal + '</strong> new tweet' + plural + '</span>';
            insertAfter(el, this.byClass('twtr-reference-tweet', 'div'));
            this._hasNewSearchResults = false;
          }
        },

        /**
          * @private
          * helps transitions to be smooth
          */
        _preloadImages: function(results) {
          if (this._isProfileWidget || this._isFavsWidget || this._isListWidget) {
            each(results, function(el) {
              var img = new Image();
              img.src = matchUrlScheme(el.user.profile_image_url);
            });
          }

          else {
            each(results, function(el) {
              (new Image()).src = matchUrlScheme(el.profile_image_url);
            });
          }

        },

        // FIXME: This seems like a bug in Occasionally.
        /**
          * @private
          * @return bool
          * tells the job whether to decay
          */
        _decayDecider: function() {
          var r = false;

          if (!this.runOnce) {
            this.runOnce = true;
            r = true;
          }

          else if (this.newResults) {
            r = true;
          }

          return r;
        },

        /**
          * @public
          * @return self
          * starts the cycle
          */
        start: function() {
          var that = this;
          if (!this._rendered) {
            setTimeout(function() {
              that.start.call(that);
            }, 50);
            return this;
          }
          if (!this._isLive) {
            this._getResults();
          }
          else {
            this.occasionalJob.start();
          }
          this._isRunning = true;
          this._hasOfficiallyStarted = true;
          return this;
        },

        /**
          * @public
          * @return self
          * stops the cycle
          */
        stop: function() {
          this.occasionalJob.stop();

          if (this.intervalJob) {
            this.intervalJob.stop();
          }

          this._isRunning = false;
          return this;
        },

        /**
          * @public
          * @return self
          * will pause the scrolling, but not stop polling for new results
          * useful for 'hover' interactions
          */
        pause: function() {
          if (this.isRunning() && this.intervalJob) {
            this.intervalJob.stop();
            classes.add(this.widgetEl, 'twtr-paused');
            this._isRunning = false;
          }

          if (this._resumeTimer) {
            clearTimeout(this._resumeTimer);
            this._resumeTimer = null;
          }

          return this;
        },

        /**
          * @public
          * @return self
          * it's like unpausing
          */
        resume: function() {
          var that = this;

          if (!this.isRunning() && this._hasOfficiallyStarted && this.intervalJob) {
            this._resumeTimer = window.setTimeout(function() {
              that.intervalJob.start();
              that._isRunning = true;
              classes.remove(that.widgetEl, 'twtr-paused');
            }, 2000);
          }

          return this;
        },

        /**
          * @public
          * @return bool
          * whether the widget is running
          */
        isRunning: function() {
          return this._isRunning;
        },

        /**
          * @public facade
          * @return self
          * convenience method to stop the cycle, then clear it out
          * widget can be reused if destroyed
          */
        destroy: function() {
          this.stop();
          this.clear();
          this.runOnce = false;
          this._hasOfficiallyStarted = false;
          this._profileImage = false;
          this._isLive = true;
          this._tweetFilter = false;
          this._isScroll = false;
          this.newResults = false;
          this._isRunning = false;
          this.sinceId = 1;
          this.results = [];
          this.showedResults = [];
          this.occasionalJob.destroy();

          if (this.jsonRequestRunning) {
            clearTimeout(this.jsonRequestTimer);
          }

          classes.remove(this.widgetEl, 'twtr-scroll');
          this.removeEvents();
          return this;
        }
      };
    }();
  })();
})(); // #end application closure
