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

goog.provide('mirosubs.controls.VolumeSlider');

mirosubs.controls.VolumeSlider = function(opt_domHelper) {
    mirosubs.SliderBase.call(this, opt_domHelper);
    this.setOrientation(mirosubs.SliderBase.Orientation.VERTICAL);
};
goog.inherits(mirosubs.controls.VolumeSlider, mirosubs.SliderBase);

mirosubs.controls.VolumeSlider.CSS_CLASS_PREFIX =
    goog.getCssName('mirosubs');

mirosubs.controls.VolumeSlider.THUMB_CSS_CLASS =
    goog.getCssName(mirosubs.controls.VolumeSlider.CSS_CLASS_PREFIX,
                    'volume-scrobbler');

mirosubs.controls.VolumeSlider.prototype.getCssClass = function(orient) {
    return goog.getCssName(mirosubs.controls.VolumeSlider.CSS_CLASS_PREFIX,
			   'volume-slider');
};

/** @inheritDoc */
mirosubs.controls.VolumeSlider.prototype.createThumb = function() {
    var element = this.getElement();
    var thumb = this.getDomHelper().createDom(
	'div', mirosubs.controls.VolumeSlider.THUMB_CSS_CLASS);
    goog.dom.a11y.setRole(thumb, goog.dom.a11y.Role.BUTTON);
    element.appendChild(thumb);
    this.thumb = thumb;
};