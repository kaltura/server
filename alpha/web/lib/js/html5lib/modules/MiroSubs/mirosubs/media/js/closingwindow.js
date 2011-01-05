// Universal Subtitles, universalsubtitles.org
// 
// Copyright (C) 2010 Participatory Culture Foundation
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
// 
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see 
// http://www.gnu.org/licenses/agpl-3.0.html.

goog.provide('mirosubs.ClosingWindow');

/**
 * @constructor
 * This is a singleton, so use mirosubs.ClosingWindow.getInstance() instead.
 */
mirosubs.ClosingWindow = function() {
    goog.events.EventTarget.call(this);
    var oldOnBeforeUnload = window.onbeforeunload;
    var that = this;
    window.onbeforeunload = function(evt) {
        var ret, oldRet;
        try {
            ret = that.beforeunload_();
        } finally {
            oldRet = oldOnBeforeUnload && oldOnBeforeUnload(evt);
        }
        if (ret != null)
            return ret;
        if (oldRet != null)
            return oldRet;
        // returns undefined.
    };
};
goog.inherits(mirosubs.ClosingWindow, goog.events.EventTarget);
goog.addSingletonGetter(mirosubs.ClosingWindow);

mirosubs.ClosingWindow.BEFORE_UNLOAD = 'beforeunload';

mirosubs.ClosingWindow.prototype.beforeunload_ = function() {
    var event = new mirosubs.ClosingWindow.BeforeUnloadEvent();
    goog.events.dispatchEvent(this, event);
    return event.message;
};

mirosubs.ClosingWindow.BeforeUnloadEvent = function() {
    goog.events.Event.call(this, mirosubs.ClosingWindow.BEFORE_UNLOAD);
    this.message = null;
};
goog.inherits(mirosubs.ClosingWindow.BeforeUnloadEvent, goog.events.Event);
