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

goog.provide('mirosubs.controls.ProgressSlider');

mirosubs.controls.ProgressSlider = function(opt_domHelper) {
    mirosubs.SliderBase.call(this, opt_domHelper);
    this.setClickToMove(false);
};
goog.inherits(mirosubs.controls.ProgressSlider, mirosubs.SliderBase);

mirosubs.controls.ProgressSlider.CSS_CLASS_PREFIX =
    goog.getCssName('mirosubs');

mirosubs.controls.ProgressSlider.THUMB_CSS_CLASS =
    goog.getCssName(mirosubs.controls.ProgressSlider.CSS_CLASS_PREFIX,
                    'scrobbler');

mirosubs.controls.ProgressSlider.prototype.getCssClass = function(orient) {
    return goog.getCssName(mirosubs.controls.ProgressSlider.CSS_CLASS_PREFIX,
			   'progress-slider');
};

/** @inheritDoc */
mirosubs.controls.ProgressSlider.prototype.createThumb = function() {
    var element = this.getElement();
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    var thumb = 
        $d('div', mirosubs.controls.ProgressSlider.THUMB_CSS_CLASS,
           $d('span'));
    goog.dom.a11y.setRole(thumb, goog.dom.a11y.Role.BUTTON);
    element.appendChild(thumb);
    this.thumb = thumb;
};