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

goog.provide('mirosubs.Widgetizer');

/**
 * @constructor
 * This is a singleton, so don't call this method directly.
 */
mirosubs.Widgetizer = function() {
    var myURI = new goog.Uri(window.location);
    var DEBUG_WIN_NAME = 'mirosubsdebuggingmain';
    if (myURI.getParameterValue('debug_mirosubs_js') == 'true' &&
       window.name != DEBUG_WIN_NAME) {
        var debugWindow = new goog.debug.FancyWindow(DEBUG_WIN_NAME);
        debugWindow.setEnabled(true);
        debugWindow.init();
        mirosubs.DEBUG = true;
    }
};
goog.addSingletonGetter(mirosubs.Widgetizer);

mirosubs.Widgetizer.logger_ = 
    goog.debug.Logger.getLogger('mirosubs.Widgetizer');

/**
 * Converts all videos in the page to Mirosubs widgets.
 *
 */
mirosubs.Widgetizer.prototype.widgetize = function() {
    mirosubs.Widgetizer.logger_.info('widgetize called');
    mirosubs.Widgetizer.logger_.info(
        'is dom loaded: ' +
            (mirosubs.LoadingDom.getInstance().isDomLoaded() ? 
             'true' : 'false'));
    if (mirosubs.LoadingDom.getInstance().isDomLoaded()) {
        this.onLoaded_();
    }
    else {
        goog.events.listenOnce(
            mirosubs.LoadingDom.getInstance(),
            mirosubs.LoadingDom.DOMLOAD,
            this.onLoaded_, false, this);
    }
};

mirosubs.Widgetizer.prototype.videosExist = function() {
    return this.findAndWidgetizeElements_(true);
}

mirosubs.Widgetizer.prototype.onLoaded_ = function() {
    mirosubs.Widgetizer.logger_.info('onLoaded_ called');
    this.addHeadCss();
    this.findAndWidgetizeElements_();
};

mirosubs.Widgetizer.prototype.findAndWidgetizeElements_ = 
    function(opt_findOnly) 
{
    // including some heuristics here for some of the bigger sites.
    if (window.location.hostname.match(/youtube\.com$/) != null) {
        var videoElem = goog.dom.getElement('movie_player');
        if (videoElem) {
            if (!opt_findOnly)
                this.widgetizeElem_(videoElem, window.location.href);
            return true;
        }
        else
            return false;
    }
    else {
        var unwidgetizedVideos = this.filterUnwidgetized_(
            document.getElementsByTagName('video'));
        if (!opt_findOnly)
            this.widgetizeVideoElements_(unwidgetizedVideos);
        var objectsFound = this.widgetizeObjectElements_(
            this.filterUnwidgetized_(
                document.getElementsByTagName('object')), 
            opt_findOnly);
        return unwidgetizedVideos.length > 0 || objectsFound;
    }
};

mirosubs.Widgetizer.prototype.addHeadCss = function() {
    if (!window.MiroCSSLoading) {
        window.MiroCSSLoading = true;
        var head = document.getElementsByTagName('head')[0];
        var css = document.createElement('link');
        css.type = 'text/css';
        css.rel = 'stylesheet';
        css.href = mirosubs.Config.siteConfig['mediaURL'] + 
            'css/mirosubs-widget.css';
        css.media = 'screen';
        head.appendChild(css);
    }
};

mirosubs.Widgetizer.prototype.filterUnwidgetized_ = function(elementArray) {
    var that = this;
    return goog.array.filter(
        elementArray,
        function(elem) {
            return !that.alreadyWidgetized_(elem);
        });
};

mirosubs.Widgetizer.prototype.alreadyWidgetized_ = function(elem) {
    // we consider it to be widgetized if it's contained in a widget div or
    // it's contained in the subtitling dialog.
    return goog.dom.getAncestorByTagNameAndClass(
        elem, 'div', 'mirosubs-widget') != null ||
        goog.dom.getAncestorByTagNameAndClass(
            elem, 'div', 'mirosubs-modal-widget') != null;
};

mirosubs.Widgetizer.prototype.widgetizeVideoElements_ = function(videoElems) {
    for (var i = 0; i < videoElems.length; i++)
        this.widgetizeElem_(videoElems[i], this.findHtml5URL_(videoElems[i]));
};

mirosubs.Widgetizer.prototype.widgetizeObjectElements_ = 
    function(objectElems, findOnly) 
{
    var found = false;
    for (var i = 0; i < objectElems.length; i++) {
        var youtubeURL = this.findYoutubeURL_(objectElems[i]);
        if (youtubeURL != null) {
            found = true;
            if (!findOnly)
                this.widgetizeElem_(objectElems[i], youtubeURL);
        }
    }
    return found;
};

mirosubs.Widgetizer.prototype.findHtml5URL_ = function(videoElem) {
    return videoElem.getElementsByTagName('source')[0].src;
};

mirosubs.Widgetizer.prototype.findYoutubeURL_ = function(objectElem) {
    var paramElems = objectElem.getElementsByTagName('param');
    for (var i = 0; i < paramElems.length; i++) {
        if (paramElems[i]["name"] == "movie" &&
            paramElems[i]["value"] != null &&
            mirosubs.video.VideoSource.isYoutube(paramElems[i]["value"]))
            return paramElems[i]["value"];
    }
    return null;
};

mirosubs.Widgetizer.prototype.widgetizeElem_ = function(elem, videoURL) {
    mirosubs.Widgetizer.logger_.info('widgetizeElem_ called for ' + videoURL);
    var containingElement = document.createElement('div');
    var styleElement = document.createElement('style');
    var innerStyle = mirosubs.Config.innerStyle;
    if ('textContent' in styleElement)
        styleElement.textContent = innerStyle;
    else {
        // IE
        styleElement.setAttribute("type", "text/css")
        styleElement.styleSheet.cssText = innerStyle;
    }
    containingElement.appendChild(styleElement);
    var widgetDiv = document.createElement('div');
    widgetDiv.className = 'mirosubs-widget';
    containingElement.appendChild(widgetDiv);

    var parentElem = elem.parentNode;
    parentElem.insertBefore(containingElement, elem);
    parentElem.removeChild(elem);
    var widgetConfig = { 'video_url': videoURL };
    mirosubs.widget.CrossDomainEmbed.embed(
        widgetDiv, widgetConfig, mirosubs.Config.siteConfig);
};
