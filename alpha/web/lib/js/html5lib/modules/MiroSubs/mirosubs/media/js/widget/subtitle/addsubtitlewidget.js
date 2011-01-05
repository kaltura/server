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

goog.provide('mirosubs.subtitle.AddSubtitleWidget');

mirosubs.subtitle.AddSubtitleWidget = function() {
    goog.ui.Component.call(this);
    
};
goog.inherits(mirosubs.subtitle.AddSubtitleWidget, goog.ui.Component);

mirosubs.subtitle.AddSubtitleWidget.ADD = 'addsub';

mirosubs.subtitle.AddSubtitleWidget.prototype.createDom = function() {
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.addSubLink_ = 
        $d('a',
           {'href':'#', 
            'className':'mirosubs-append-sub-link'}, 
           'Add subtitle');
    this.setElementInternal($d('li', null, this.addSubLink_));
    this.getElement().className = 'mirosubs-append-sub-button';
};

mirosubs.subtitle.AddSubtitleWidget.prototype.enterDocument = function() {
    mirosubs.subtitle.AddSubtitleWidget.superClass_.enterDocument.call(this);
    this.getHandler().listen(
        this.addSubLink_, goog.events.EventType.CLICK, this.addClicked_);
};

mirosubs.subtitle.AddSubtitleWidget.prototype.addClicked_ = function(e) {
    this.dispatchEvent(mirosubs.subtitle.AddSubtitleWidget.ADD);
    e.preventDefault();
    e.stopPropagation();
};

mirosubs.subtitle.AddSubtitleWidget.prototype.showLink = function(display) {
    goog.style.showElement(this.addSubLink_, display);
};