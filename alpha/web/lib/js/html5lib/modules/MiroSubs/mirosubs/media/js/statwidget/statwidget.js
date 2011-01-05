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

goog.provide('mirosubs.statwidget.StatWidget');

/**
 * @constructor
 *
 */
mirosubs.statwidget.StatWidget = function() {
    mirosubs.siteConfig = mirosubs.StatWidgetConfig.siteConfig;
    var scripts = document.getElementsByTagName('script');
    this.script_ = scripts[scripts.length - 1];
    this.div_ = goog.dom.createDom('div');
    this.div_.innerHTML = mirosubs.StatWidgetConfig.innerHTML;
};
goog.addSingletonGetter(mirosubs.statwidget.StatWidget);

mirosubs.statwidget.StatWidget.prototype.add = function() {
    if (!goog.userAgent.IE)
        this.insertDiv_();
    else {
        if (mirosubs.LoadingDom.getInstance().isDomLoaded())
            this.insertDiv_();
        else
            goog.events.listenOnce(
                mirosubs.LoadingDom.getInstance(),
                mirosubs.LoadingDom.DOMLOAD,
                goog.bind(this.insertDiv_, this));
    }
};

mirosubs.statwidget.StatWidget.prototype.insertDiv_ = 
    function()
{
    this.script_.parentNode.insertBefore(this.div_, this.script_);
    mirosubs.Rpc.call(
        'get_widget_info', {},
        goog.bind(this.infoReceived_, this));
};

mirosubs.statwidget.StatWidget.prototype.infoReceived_ = function(result) {
    var statDiv = this.div_.getElementsByClassName('mirosubs-stats')[0];
    goog.dom.setTextContent(statDiv, result['all_videos'] + '');
};

mirosubs.statwidget.StatWidget.getInstance().add()