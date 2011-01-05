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

goog.provide('mirosubs.timeline.TimelineInner');


/**
 *
 * @param {Timeline} timeline The timeline containing this object.
 * @param {number} spacing The space, in seconds, between two
 *     major ticks.
 * @param {mirosubs.timeline.SubtitleSet} subtitleSet
 */
mirosubs.timeline.TimelineInner = function(timeline, spacing, subtitleSet) {
    goog.ui.Component.call(this);
    this.timeline_ = timeline;
    this.spacing_ = spacing;
    this.subtitleSet_ = subtitleSet;
    this.pixelsPerSecond_ = mirosubs.timeline.TimeRowUL.PX_PER_TICK / spacing;
    this.left_ = 0;
    this.time_ = 0;
};
goog.inherits(mirosubs.timeline.TimelineInner, goog.ui.Component);
mirosubs.timeline.TimelineInner.prototype.createDom = function() {
    mirosubs.timeline.TimelineInner.superClass_.createDom.call(this);
    this.getElement().className = 'mirosubs-timeline-inner';
    this.timerow_ = new mirosubs.timeline.TimeRow(this, this.spacing_);
    this.addChild(this.timerow_, true);
    this.timelineSubs_ = new mirosubs.timeline.TimelineSubs(
        this.subtitleSet_,
        this.pixelsPerSecond_);
    this.addChild(this.timelineSubs_, true);
};
mirosubs.timeline.TimelineInner.prototype.ensureVisible = function(time) {
    this.timerow_.ensureVisible(time);
};
mirosubs.timeline.TimelineInner.prototype.getLeft = function() {
    return this.left_;
};
mirosubs.timeline.TimelineInner.prototype.setLeft = function(left, offset, maxTime) {
    // ensure that we never try to set the playhead time below 0
    // or above the duration of the video (if we know it)
    left = Math.min(left, 0);
    if (maxTime != 0) {
        left = Math.max(left, -maxTime * this.pixelsPerSecond_);
    }

    var newTime = -left / this.pixelsPerSecond_;
    this.left_ = left;
    this.time_ = newTime;
    this.getElement().style.left = (left + offset) + 'px';
    this.ensureVisible(newTime);
};
mirosubs.timeline.TimelineInner.prototype.getTime = function() {
    return this.time_;
};
mirosubs.timeline.TimelineInner.prototype.setTime = function(time, offset, maxTime) {
    if (maxTime != 0) {
        time = Math.min(time, maxTime);
    }

    var newLeft = -time * this.pixelsPerSecond_;
    this.left_ = newLeft;
    this.time_ = time;
    this.getElement().style.left = (newLeft + offset) + 'px';
    this.ensureVisible(time);
};
mirosubs.timeline.TimelineInner.prototype.beforeDrag = function(e) {
    return this.timeline_.beforeDrag(e);
};
mirosubs.timeline.TimelineInner.prototype.startDrag = function(e) {
    this.timerow_.changeCursor(true);
    this.timeline_.startDrag(e);
};
mirosubs.timeline.TimelineInner.prototype.onDrag = function(e) {
    this.timeline_.onDrag(e);
};
mirosubs.timeline.TimelineInner.prototype.endDrag = function(e) {
    this.timerow_.changeCursor(false);
    this.timeline_.endDrag(e);
};
