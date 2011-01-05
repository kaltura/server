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

goog.provide('mirosubs.timeline.Timeline');
goog.require('goog.fx.Dragger');
goog.require('goog.style.cursor');

/**
 *
 * @param {number} spacing The space, in seconds, between two
 *     major ticks.
 * @param {mirosubs.timeline.SubtitleSet} subtitleSet
 */
mirosubs.timeline.Timeline = function(spacing, subtitleSet, videoPlayer) {
    goog.ui.Component.call(this);
    this.spacing_ = spacing;
    this.subtitleSet_ = subtitleSet;
    this.videoPlayer_ = videoPlayer;
};
goog.inherits(mirosubs.timeline.Timeline, goog.ui.Component);
mirosubs.timeline.Timeline.prototype.createDom = function() {
    mirosubs.timeline.Timeline.superClass_.createDom.call(this);
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    var el = this.getElement();
    el.className = 'mirosubs-timeline';
    el.appendChild($d('div', 'top', ' '));
    this.timelineInner_ = new mirosubs.timeline.TimelineInner(
        this, this.spacing_, this.subtitleSet_);
    this.addChild(this.timelineInner_, true);
    el.appendChild($d('div', 'marker'));
};
/**
 * Useful for when times are cleared.
 */
mirosubs.timeline.Timeline.prototype.reset_ = function() {
    this.removeChild(this.timelineInner_, true);
    this.timelineInner_.dispose();
    this.timelineInner_ = new mirosubs.timeline.TimelineInner(
        this, this.spacing_, this.subtitleSet_);
    this.addChild(this.timelineInner_, true);
};
mirosubs.timeline.Timeline.prototype.enterDocument = function() {
    mirosubs.timeline.Timeline.superClass_.enterDocument.call(this);
    this.getHandler().
        listen(
            this.videoPlayer_,
            mirosubs.video.AbstractVideoPlayer.EventType.TIMEUPDATE,
            this.videoTimeUpdate_).
        listen(
            this.timelineInner_,
            goog.object.getValues(
                mirosubs.timeline.TimelineSub.EventType),
            this.timelineSubEdit_).
        listen(
            this.timelineInner_,
            mirosubs.timeline.TimeRowUL.DOUBLECLICK,
            this.timeRowDoubleClick_).
        listen(
            this.subtitleSet_,
            mirosubs.timeline.SubtitleSet.CLEAR_TIMES,
            this.reset_);
    this.initTime_();
};
mirosubs.timeline.Timeline.prototype.timeRowDoubleClick_ = function(e) {
    this.videoPlayer_.setPlayheadTime(e.time);
    this.videoPlayer_.play();
};
mirosubs.timeline.Timeline.prototype.timelineSubEdit_ = function(e) {
    var et = mirosubs.timeline.TimelineSub.EventType;
    if (e.type == et.START_EDITING)
        this.videoPlayer_.pause();
};
mirosubs.timeline.Timeline.prototype.videoTimeUpdate_ = function(e) {
    this.setTime_(this.videoPlayer_.getPlayheadTime());
};
mirosubs.timeline.Timeline.prototype.initTime_ = function() {
    this.ensureWidth_();
    if (this.width_)
        this.videoTimeUpdate_();
    else
        goog.Timer.callOnce(goog.bind(this.initTime_, this));
};
mirosubs.timeline.Timeline.prototype.ensureWidth_ = function() {
    if (!this.width_) {
        var size = goog.style.getSize(this.getElement());
        this.width_ = size.width;
    }
};
mirosubs.timeline.Timeline.prototype.setTime_ = function(time) {
    this.ensureWidth_();
    this.timelineInner_.setTime(
        time, this.width_ / 2, 
        this.videoPlayer_.getDuration());
};
mirosubs.timeline.Timeline.prototype.getTime_ = function() {
    this.ensureWidth_();
    return this.timelineInner_.getTime();
};
mirosubs.timeline.Timeline.prototype.beforeDrag = function(e) {
    // Returns false if a timeline subtitle's start or end time is being
    // changed, to keep the timeline from jumping around.
    return !mirosubs.timeline.TimelineSub.isCurrentlyEditing();
};
mirosubs.timeline.Timeline.prototype.startDrag = function(e) {
    this.wasPlaying_ = this.videoPlayer_.isPlaying();
    this.videoPlayer_.pause();
    this.oldLeft_ = this.timelineInner_.getLeft();
};
mirosubs.timeline.Timeline.prototype.onDrag = function(e) {
    this.ensureWidth_();
    this.timelineInner_.setLeft(e.left + this.oldLeft_, this.width_ / 2,
                                this.videoPlayer_.getDuration());
};
mirosubs.timeline.Timeline.prototype.endDrag = function(e) {
    this.oldLeft_ = null;
    this.videoPlayer_.setPlayheadTime(this.timelineInner_.getTime());
    if (this.wasPlaying_) {
        this.videoPlayer_.play();
    }
};
