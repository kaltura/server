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

goog.provide('mirosubs.widget.VideoTab');

/**
 * @constructor
 */
mirosubs.widget.VideoTab = function() {
    goog.ui.Component.call(this);
    this.anchorElem_ = null;
    this.imageElem_ = null;
    this.spanElem_ = null;
    this.nudgeElem_ = null;
    this.nudgeSpanElem_ = null;
    this.nudgeClickCallback_ = null;
    this.spinnerGifURL_ = mirosubs.imageAssetURL('spinner.gif');
    this.logoURL_ = mirosubs.imageAssetURL('small_logo.png');
    this.imageLoader_ = new goog.net.ImageLoader();
    this.imageLoader_.addImage('spinner', this.spinnerGifURL_);
    this.imageLoader_.addImage('small_logo', this.logoURL_);
    this.imageLoader_.start();
};
goog.inherits(mirosubs.widget.VideoTab, goog.ui.Component);

mirosubs.widget.VideoTab.prototype.createDom = function() {
    mirosubs.widget.VideoTab.superClass_.createDom.call(this);
    this.getElement().className = 'mirosubs-videoTab';
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.imageElem_ = $d('img', {'alt': 'small logo'});
    this.spanElem_ = $d('span', 'mirosubs-tabTextchoose');
    this.anchorElem_ = 
        $d('a', {'className': 'mirosubs-subtitleMeLink', 'href':'#'},
           this.imageElem_, this.spanElem_);
    this.nudgeSpanElem_ = $d('span', 'mirosubs-tabTextfinish', 'NUDGE TEXT');
    this.nudgeElem_ = $d('a', {'href':'#'}, this.nudgeSpanElem_);
    this.getElement().appendChild(this.anchorElem_);
    this.getElement().appendChild(this.nudgeElem_);
};

mirosubs.widget.VideoTab.prototype.enterDocument = function() {
    mirosubs.widget.VideoTab.superClass_.enterDocument.call(this);
    goog.style.showElement(this.nudgeElem_, false);
    this.getHandler().
        listen(this.nudgeElem_, 'click', this.nudgeClicked_);
};

mirosubs.widget.VideoTab.prototype.getMenuAnchor = function() {
    return this.anchorElem_;
};

mirosubs.widget.VideoTab.prototype.showLoading = function() {
    this.imageElem_.src = this.spinnerGifURL_;
    goog.dom.setTextContent(this.spanElem_, "Loading");
};

/**
 * Just stops loading. If state has changed, stop loading by
 * calling showContent instead.
 *
 */
mirosubs.widget.VideoTab.prototype.stopLoading = function() {
    this.imageElem_.src = this.logoURL_;
    if (this.text_)
        goog.dom.setTextContent(this.spanElem_, this.text_);
};

/**
 * Stops loading, and shows text appropriate for content.
 * @param {boolean} hasSubtitles Do subs currently exist for this video?
 * @param {mirosubs.widget.SubtitleState=} opt_playSubState Subtitles 
 *     that are currently loaded to play in widget.
 */
mirosubs.widget.VideoTab.prototype.showContent = function(
    hasSubtitles, opt_playSubState) 
{
    this.imageElem_.src = this.logoURL_;
    var text;
    if (opt_playSubState)
        text = opt_playSubState.LANGUAGE ? 
            mirosubs.languageNameForCode(opt_playSubState.LANGUAGE) :
            "Original Language";
    else
        text = hasSubtitles ? "Select Language" : "Subtitle Me";
    this.text_ = text;
    goog.dom.setTextContent(this.spanElem_, text);
};
mirosubs.widget.VideoTab.prototype.getAnchorElem = function() {
    return this.anchorElem_;
};

mirosubs.widget.VideoTab.prototype.nudgeClicked_ = function(e) {
    e.preventDefault();
    if (this.nudgeClickCallback_)
        this.nudgeClickCallback_();
};
mirosubs.widget.VideoTab.prototype.showNudge = function(showHide) {
    goog.style.showElement(this.nudgeElem_, showHide);
};
mirosubs.widget.VideoTab.prototype.updateNudge = function(text, fn) {
    goog.dom.setTextContent(this.nudgeSpanElem_, text);
    this.nudgeClickCallback_ = fn;
};

mirosubs.widget.VideoTab.prototype.disposeInternal = function() {
    mirosubs.widget.VideoTab.superClass_.disposeInternal.call(this);
    this.imageLoader_.dispose();
};
