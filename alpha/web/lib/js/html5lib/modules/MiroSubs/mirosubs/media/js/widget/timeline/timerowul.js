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

goog.provide('mirosubs.timeline.TimeRowUL');

/**
 *
 * @param {number} spacing Spacing between major ticks, in seconds.
 * @param {number} first time for this timerow ul, in seconds.
 */
mirosubs.timeline.TimeRowUL = function(spacing, firstTime) {
    goog.ui.Component.call(this);
    this.firstTime_ = firstTime;
    this.spacing_ = spacing;
    this.majorTicks_ = [];
};
goog.inherits(mirosubs.timeline.TimeRowUL, goog.ui.Component);
mirosubs.timeline.TimeRowUL.NUM_MAJOR_TICKS = 15;
mirosubs.timeline.TimeRowUL.PX_PER_TICK = 65;
mirosubs.timeline.TimeRowUL.DOUBLECLICK = 'timerowdblclick';
mirosubs.timeline.TimeRowUL.prototype.createDom = function() {
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.setElementInternal($d('ul', 'mirosubs-timeline-time'));
    var el = this.getElement();
    el.style.width =
        (mirosubs.timeline.TimeRowUL.NUM_MAJOR_TICKS *
         mirosubs.timeline.TimeRowUL.PX_PER_TICK) + 'px';
    el.style.left =
        (this.firstTime_ / this.spacing_ * 
         mirosubs.timeline.TimeRowUL.PX_PER_TICK) + 'px';
    this.majorTicks_ = [];
    var i;
    for (i = 0; i < mirosubs.timeline.TimeRowUL.NUM_MAJOR_TICKS; i++) {
        var tick = $d('li');
        el.appendChild(tick);
        this.majorTicks_.push(tick);
    }
    this.setFirstTime(this.firstTime_);
};
mirosubs.timeline.TimeRowUL.prototype.enterDocument = function() {
    mirosubs.timeline.TimeRowUL.superClass_.enterDocument.call(this);
    this.getHandler().listen(
        this.getElement(), 
        goog.events.EventType.DBLCLICK,
        this.doubleClicked_);
};
mirosubs.timeline.TimeRowUL.prototype.doubleClicked_ = function(e) {
    this.dispatchEvent(
        new mirosubs.timeline.TimeRowUL.DoubleClickEvent(
            this.firstTime_ + e.offsetX * this.spacing_ / 
                mirosubs.timeline.TimeRowUL.PX_PER_TICK));
};
mirosubs.timeline.TimeRowUL.prototype.setFirstTime = function(time) {
    time = Math.max(0, time);
    time = Math.floor(time / this.spacing_) * this.spacing_;
    this.firstTime_ = time;
    this.lastTime_ = time + this.spacing_ * 
        mirosubs.timeline.TimeRowUL.NUM_MAJOR_TICKS;
    var i, seconds;
    for (i = 0; i < mirosubs.timeline.TimeRowUL.NUM_MAJOR_TICKS; i++) {
        seconds = this.firstTime_ + i * this.spacing_;
        goog.dom.setTextContent(
            this.majorTicks_[i], 
            mirosubs.formatTime(seconds >= 0 ? ('' + seconds) : '', true));
    }
};
mirosubs.timeline.TimeRowUL.prototype.getFirstTime = function() {
    return this.firstTime_;
};
mirosubs.timeline.TimeRowUL.prototype.getLastTime = function() {
    return this.lastTime_;
};
mirosubs.timeline.TimeRowUL.DoubleClickEvent = function(time) {
    this.type = mirosubs.timeline.TimeRowUL.DOUBLECLICK;
    this.time = time;
};