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

goog.provide('mirosubs.timeline.Subtitle');

mirosubs.timeline.Subtitle = function(editableCaption, videoPlayer) {
    goog.events.EventTarget.call(this);
    this.editableCaption_ = editableCaption;
    this.videoPlayer_ = videoPlayer;
    this.nextSubtitle_ = null;
    this.eventHandler_ = new goog.events.EventHandler(this);
    this.eventHandler_.listen(
        editableCaption,
        mirosubs.subtitle.EditableCaption.CHANGE,
        this.captionChanged_);
    this.videoEventHandler_ = null;
    this.updateTimes_();
};
goog.inherits(mirosubs.timeline.Subtitle, goog.events.EventTarget);

mirosubs.timeline.Subtitle.CHANGE = 'tsubchanged';
mirosubs.timeline.Subtitle.MIN_UNASSIGNED_LENGTH = 2.0;
mirosubs.timeline.Subtitle.UNASSIGNED_SPACING = 0.5;

mirosubs.timeline.Subtitle.orderCompare = function(a, b) {
    return a.getEditableCaption().getSubOrder() - 
        b.getEditableCaption().getSubOrder();
};

mirosubs.timeline.Subtitle.prototype.captionChanged_ = function(e) {
    if (this.editableCaption_.getStartTime() != -1)
        this.updateTimes_();
    if (this.nextSubtitle_ && this.nextSubtitle_.isLoneUnsynced_())
        this.nextSubtitle_.updateTimes_();
    this.dispatchEvent(mirosubs.timeline.Subtitle.CHANGE);
};

mirosubs.timeline.Subtitle.prototype.updateTimes_ = function() {
    if (this.isLoneUnsynced_()) {
        var prevSubtitleEndTime =
            this.editableCaption_.getPreviousCaption() == null ?
            -1 : this.editableCaption_.getPreviousCaption().getEndTime();
        this.startTime_ =
            Math.max(prevSubtitleEndTime,
                     this.videoPlayer_.getPlayheadTime()) +
            mirosubs.timeline.Subtitle.UNASSIGNED_SPACING;
    }
    else {
        this.startTime_ = this.editableCaption_.getStartTime();
    }
    if (this.isLoneUnsynced_() ||
        this.editableCaption_.hasStartTimeOnly()) {
        if (this.videoEventHandler_ == null) {
            this.videoEventHandler_ = new goog.events.EventHandler(this);
            this.videoEventHandler_.listen(
                this.videoPlayer_,
                mirosubs.video.AbstractVideoPlayer.EventType.TIMEUPDATE,
                this.videoTimeUpdate_);
        }
        if (this.editableCaption_.hasStartTimeOnly()) {
            this.endTime_ = Math.max(
                this.startTime_ +
                    mirosubs.timeline.Subtitle.MIN_UNASSIGNED_LENGTH,
                this.videoPlayer_.getPlayheadTime());
            if (this.nextSubtitle_)
                this.nextSubtitle_.bumpUnsyncedTimes(this.endTime_);
        }
        else {
            this.endTime_ = this.startTime_ +
                mirosubs.timeline.Subtitle.MIN_UNASSIGNED_LENGTH;
        }
    }
    else {
        this.endTime_ = this.editableCaption_.getEndTime();
        if (this.videoEventHandler_ != null) {
            this.videoEventHandler_.dispose();
            this.videoEventHandler_ = null;
        }
    }
};

mirosubs.timeline.Subtitle.prototype.isLoneUnsynced_ = function() {
    return this.editableCaption_.getStartTime() == -1 &&
        (this.editableCaption_.getPreviousCaption() == null ||
         this.editableCaption_.getPreviousCaption().getEndTime() != -1);
};

mirosubs.timeline.Subtitle.prototype.isNextToBeSynced = function() {
    return this.editableCaption_.getStartTime() == -1;
};

mirosubs.timeline.Subtitle.prototype.setNextSubtitle = function(sub) {
    this.nextSubtitle_ = sub;
    if (sub && this.editableCaption_.hasStartTimeOnly())
        this.nextSubtitle_.bumpUnsyncedTimes(this.endTime_);
};

mirosubs.timeline.Subtitle.prototype.videoTimeUpdate_ = function(e) {
    if (this.editableCaption_.hasStartTimeOnly()) {
        var prevEndTime = this.endTime_;
        this.endTime_ = Math.max(
            this.startTime_ + mirosubs.timeline.Subtitle.MIN_UNASSIGNED_LENGTH,
            this.videoPlayer_.getPlayheadTime());
        if (prevEndTime != this.endTime_) {
            this.dispatchEvent(mirosubs.timeline.Subtitle.CHANGE);
            if (this.nextSubtitle_)
                this.nextSubtitle_.bumpUnsyncedTimes(this.endTime_);
        }
    }
    else {
        if (this.editableCaption_.getPreviousCaption() == null)
            this.bumpUnsyncedTimes(this.videoPlayer_.getPlayheadTime());
        else
            this.bumpUnsyncedTimes(Math.max(
                this.videoPlayer_.getPlayheadTime(),
                this.editableCaption_.getPreviousCaption().getEndTime()));
    }
};

mirosubs.timeline.Subtitle.prototype.bumpUnsyncedTimes = function(time) {
    var prevStartTime = this.startTime_;
    this.startTime_ = time +
        mirosubs.timeline.Subtitle.UNASSIGNED_SPACING;
    this.endTime_ = this.startTime_ +
        mirosubs.timeline.Subtitle.MIN_UNASSIGNED_LENGTH;
    if (this.startTime_ != prevStartTime)
        this.dispatchEvent(mirosubs.timeline.Subtitle.CHANGE);
};

mirosubs.timeline.Subtitle.prototype.getStartTime = function() {
    return this.startTime_;
};

mirosubs.timeline.Subtitle.prototype.getEndTime = function() {
    return this.endTime_;
};

mirosubs.timeline.Subtitle.prototype.getEditableCaption = function() {
    return this.editableCaption_
};

mirosubs.timeline.Subtitle.prototype.disposeInternal = function() {
    mirosubs.timeline.Subtitle.superClass_.disposeInternal.call(this);
    this.eventHandler_.dispose();
    if (this.videoEventHandler_)
        this.videoEventHandler_.dispose();
};

