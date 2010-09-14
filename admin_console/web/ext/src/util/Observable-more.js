/*!
 * Ext JS Library 3.1.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.util.Observable
 */
Ext.apply(Ext.util.Observable.prototype, function(){
    // this is considered experimental (along with beforeMethod, afterMethod, removeMethodListener?)
    // allows for easier interceptor and sequences, including cancelling and overwriting the return value of the call
    // private
    function getMethodEvent(method){
        var e = (this.methodEvents = this.methodEvents ||
        {})[method], returnValue, v, cancel, obj = this;

        if (!e) {
            this.methodEvents[method] = e = {};
            e.originalFn = this[method];
            e.methodName = method;
            e.before = [];
            e.after = [];

            var makeCall = function(fn, scope, args){
                if (!Ext.isEmpty(v = fn.apply(scope || obj, args))) {
                    if (Ext.isObject(v)) {
                        returnValue = !Ext.isEmpty(v.returnValue) ? v.returnValue : v;
                        cancel = !!v.cancel;
                    }
                    else
                        if (v === false) {
                            cancel = true;
                        }
                        else {
                            returnValue = v;
                        }
                }
            };

            this[method] = function(){
                var args = Ext.toArray(arguments);
                returnValue = v = undefined;
                cancel = false;

                Ext.each(e.before, function(b){
                    makeCall(b.fn, b.scope, args);
                    if (cancel) {
                        return returnValue;
                    }
                });

                if (!Ext.isEmpty(v = e.originalFn.apply(obj, args))) {
                    returnValue = v;
                }
                Ext.each(e.after, function(a){
                    makeCall(a.fn, a.scope, args);
                    if (cancel) {
                        return returnValue;
                    }
                });
                return returnValue;
            };
        }
        return e;
    }

    return {
        // these are considered experimental
        // allows for easier interceptor and sequences, including cancelling and overwriting the return value of the call
        // adds an 'interceptor' called before the original method
        beforeMethod : function(method, fn, scope){
            getMethodEvent.call(this, method).before.push({
                fn: fn,
                scope: scope
            });
        },

        // adds a 'sequence' called after the original method
        afterMethod : function(method, fn, scope){
            getMethodEvent.call(this, method).after.push({
                fn: fn,
                scope: scope
            });
        },

        removeMethodListener: function(method, fn, scope){
            var e = getMethodEvent.call(this, method), found = false;
            Ext.each(e.before, function(b, i, arr){
                if (b.fn == fn && b.scope == scope) {
                    arr.splice(i, 1);
                    found = true;
                    return false;
                }
            });
            if (!found) {
                Ext.each(e.after, function(a, i, arr){
                    if (a.fn == fn && a.scope == scope) {
                        arr.splice(i, 1);
                        return false;
                    }
                });
            }
        },

        /**
         * Relays selected events from the specified Observable as if the events were fired by <tt><b>this</b></tt>.
         * @param {Object} o The Observable whose events this object is to relay.
         * @param {Array} events Array of event names to relay.
         */
        relayEvents : function(o, events){
            var me = this;
            function createHandler(ename){
                return function(){
                    return me.fireEvent.apply(me, [ename].concat(Ext.toArray(arguments)));
                };
            }
            Ext.each(events, function(ename){
                me.events[ename] = me.events[ename] || true;
                o.on(ename, createHandler(ename), me);
            });
        },

        /**
         * <p>Enables events fired by this Observable to bubble up an owner hierarchy by calling
         * <code>this.getBubbleTarget()</code> if present. There is no implementation in the Observable base class.</p>
         * <p>This is commonly used by Ext.Components to bubble events to owner Containers. See {@link Ext.Component.getBubbleTarget}. The default
         * implementation in Ext.Component returns the Component's immediate owner. But if a known target is required, this can be overridden to
         * access the required target more quickly.</p>
         * <p>Example:</p><pre><code>
Ext.override(Ext.form.Field, {
    //  Add functionality to Field&#39;s initComponent to enable the change event to bubble
    initComponent : Ext.form.Field.prototype.initComponent.createSequence(function() {
        this.enableBubble('change');
    }),

    //  We know that we want Field&#39;s events to bubble directly to the FormPanel.
    getBubbleTarget : function() {
        if (!this.formPanel) {
            this.formPanel = this.findParentByType('form');
        }
        return this.formPanel;
    }
});

var myForm = new Ext.formPanel({
    title: 'User Details',
    items: [{
        ...
    }],
    listeners: {
        change: function() {
            // Title goes red if form has been modified.
            myForm.header.setStyle('color', 'red');
        }
    }
});
</code></pre>
         * @param {String/Array} events The event name to bubble, or an Array of event names.
         */
        enableBubble : function(events){
            var me = this;
            if(!Ext.isEmpty(events)){
                events = Ext.isArray(events) ? events : Ext.toArray(arguments);
                Ext.each(events, function(ename){
                    ename = ename.toLowerCase();
                    var ce = me.events[ename] || true;
                    if (Ext.isBoolean(ce)) {
                        ce = new Ext.util.Event(me, ename);
                        me.events[ename] = ce;
                    }
                    ce.bubble = true;
                });
            }
        }
    };
}());


/**
 * Starts capture on the specified Observable. All events will be passed
 * to the supplied function with the event name + standard signature of the event
 * <b>before</b> the event is fired. If the supplied function returns false,
 * the event will not fire.
 * @param {Observable} o The Observable to capture events from.
 * @param {Function} fn The function to call when an event is fired.
 * @param {Object} scope (optional) The scope (<code>this</code> reference) in which the function is executed. Defaults to the Observable firing the event.
 * @static
 */
Ext.util.Observable.capture = function(o, fn, scope){
    o.fireEvent = o.fireEvent.createInterceptor(fn, scope);
};


/**
 * Sets observability on the passed class constructor.<p>
 * <p>This makes any event fired on any instance of the passed class also fire a single event through
 * the <i>class</i> allowing for central handling of events on many instances at once.</p>
 * <p>Usage:</p><pre><code>
Ext.util.Observable.observeClass(Ext.data.Connection);
Ext.data.Connection.on('beforerequest', function(con, options) {
    console.log('Ajax request made to ' + options.url);
});</code></pre>
 * @param {Function} c The class constructor to make observable.
 * @param {Object} listeners An object containing a series of listeners to add. See {@link #addListener}. 
 * @static
 */
Ext.util.Observable.observeClass = function(c, listeners){
    if(c){
      if(!c.fireEvent){
          Ext.apply(c, new Ext.util.Observable());
          Ext.util.Observable.capture(c.prototype, c.fireEvent, c);
      }
      if(Ext.isObject(listeners)){
          c.on(listeners);
      }
      return c;
   }
};