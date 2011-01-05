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

goog.provide('mirosubs.Extension');

/**
 * @constructor
 */
mirosubs.Extension = function() {
    this.shown_ = false;
    var element = document.createElement('MirosubsExtensionLoaded');
    document.documentElement.appendChild(element);
    var evt = document.createEvent('Events');
    evt.initEvent("MirosubsExtensionLoadedEvent", true, false);
    element.dispatchEvent(evt);
    this.show_(evt.target.getAttribute('enabled') == 'true');
};
goog.addSingletonGetter(mirosubs.Extension);

mirosubs.Extension.prototype.show_ = function(enabled) {
    if (this.shown_)
        return;
    this.shown_ = true;
    if (mirosubs.Widgetizer.getInstance().videosExist())
        this.addElementToPage_(enabled);
    if (enabled)
        mirosubs.Widgetizer.getInstance().widgetize();
};

mirosubs.Extension.prototype.addElementToPage_ = function(enabled) {
    this.enabled_ = enabled;
    mirosubs.Widgetizer.getInstance().addHeadCss();
    var $d = goog.dom.createDom;
    var $t = goog.dom.createTextNode;
    this.enableLink_ = this.createEnableLink_($d);
    this.reportProblemLink_ = this.createReportProblemLink_($d);
    this.learnMoreLink_ = this.createLearnMoreLink_($d);
    this.enabledSpan_ = $d('span', null, this.enabledSpanText_());
    this.element_ = $d('div', 'mirosubs-extension' + 
                       (enabled ? ' mirosubs-extension-enabled' : ''),
                       $d('span', null, 'Universal Subtitles Addon '),
                       this.enabledSpan_,
                       $d('span', null, ' '),
                       this.enableLink_,
                       $t(' / '),
                       this.reportProblemLink_,
                       $t(' / '),
                       this.learnMoreLink_);
    document.body.appendChild(this.element_);
    goog.events.listen(this.enableLink_, 'click',
                       this.enableClicked_, false, this);
};

mirosubs.Extension.prototype.enableClicked_ = function(e) {
    e.preventDefault();
    this.enabled_ = !this.enabled_;
    goog.dom.setTextContent(this.enableLink_, this.enableLinkText_());
    goog.dom.setTextContent(this.enabledSpan_, this.enabledSpanText_());
    goog.dom.classes.enable(
        this.element_, 'mirosubs-extension-enabled', this.enabled_);

    if (!this.toggleElement_) {
        this.toggleElement_ = document.createElement('MirosubsExtensionToggled');
        document.documentElement.appendChild(this.toggleElement_);
    }
    this.toggleElement_.setAttribute(
        'enabled', this.enabled_ ? 'true' : 'false');
    var evt = document.createEvent('Events');
    evt.initEvent("MirosubsExtensionToggledEvent", true, false);
    this.toggleElement_.dispatchEvent(evt);

    if (this.enabled_)
        mirosubs.Widgetizer.getInstance().widgetize();
};

mirosubs.Extension.prototype.createEnableLink_ = function($d) {
    return $d('a', {'href':'#'}, this.enableLinkText_());
};

mirosubs.Extension.prototype.enableLinkText_ = function() {
    return this.enabled_ ? 'disable' : 'enable';
};

mirosubs.Extension.prototype.enabledSpanText_ = function() {
    return this.enabled_ ? "Enabled!" : "Disabled";
};

mirosubs.Extension.prototype.createReportProblemLink_ = function($d) {
    var message = 
        'I had a problem with the Universal Subtitles Firefox ' +
        'extension on this page: ' + 
        window.location.href;
    var uri = new goog.Uri(mirosubs.Config.siteConfig['siteURL'] + 
                           '/videos/site_feedback/');
    uri.setParameterValue('text', message);
    return $d('a', {'href': uri.toString(), 
                    'target': mirosubs.randomString()},
              'report problem');
};

mirosubs.Extension.prototype.createLearnMoreLink_ = function($d) {
    return $d('a', {'href': 'http://universalsubtitles.org', 
                    'target': mirosubs.randomString()},
              'learn more');
};

(function() {
    var extension = mirosubs.Extension.getInstance();
    window['mirosubs'] = mirosubs;
    mirosubs['showExtension'] = function(enabled) {
        extension.show(enabled);
    };
})();
