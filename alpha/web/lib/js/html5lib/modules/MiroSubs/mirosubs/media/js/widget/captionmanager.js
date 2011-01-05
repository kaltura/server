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

goog.provide('mirosubs.CaptionManager');

/**
 * Constructor.
 *
 * @param {mirosubs.video.AbstractVideoPlayer} videoPlayer
 * @param {mirosubs.subtitle.EditableCaptionSet} captionSet
 */
mirosubs.CaptionManager = function(videoPlayer, captionSet) {
    goog.events.EventTarget.call(this);
    this.captions_ = captionSet.captionsWithTimes();
    this.binaryCompare_ = function(time, caption) {
	return time - caption.getStartTime();
    };
    this.binaryCaptionCompare_ = function(c0, c1) {
        return c0.getStartTime() - c1.getStartTime();
    };
    this.videoPlayer_ = videoPlayer;
    this.eventHandler_ = new goog.events.EventHandler(this);
    this.eventHandler_.listen(
	videoPlayer,
	mirosubs.video.AbstractVideoPlayer.EventType.TIMEUPDATE,
	this.timeUpdate_);
    this.eventHandler_.listen(
	captionSet,
        goog.array.concat(
            goog.object.getValues(
                mirosubs.subtitle.EditableCaptionSet.EventType),
            mirosubs.subtitle.EditableCaption.CHANGE),
	this.captionSetUpdate_);

    this.currentCaptionIndex_ = -1;
    this.lastCaptionDispatched_ = null;
    this.eventsDisabled_ = false;
};
goog.inherits(mirosubs.CaptionManager, goog.events.EventTarget);

mirosubs.CaptionManager.CAPTION = 'caption';
mirosubs.CaptionManager.CAPTIONS_FINISHED = 'captionsfinished';

mirosubs.CaptionManager.prototype.captionSetUpdate_ = function(event) {
    var et = mirosubs.subtitle.EditableCaptionSet.EventType;
    if (event.type == et.CLEAR_ALL ||
        event.type == et.CLEAR_TIMES) {
	this.captions_ = [];
        this.currentCaptionIndex_ = -1;
	this.dispatchCaptionEvent_(null);
    }
    else if (event.type == et.ADD) {
        var caption = event.caption;
        if (caption.getStartTime() != -1) {
            goog.array.binaryInsert(
                this.captions_, caption, this.binaryCaptionCompare_);
            this.sendEventForRandomPlayheadTime_(
                this.videoPlayer_.getPlayheadTime());
        }
    }
    else if (event.type == et.DELETE) {
        var caption = event.caption;
        if (caption.getStartTime() != -1) {
            goog.array.binaryRemove(
                this.captions_, caption, this.binaryCaptionCompare_);
            this.sendEventForRandomPlayheadTime_(
                this.videoPlayer_.getPlayheadTime());
        }
    }
    else if (event.type == mirosubs.subtitle.EditableCaption.CHANGE) {
	if (event.timesFirstAssigned) {
	    this.captions_.push(event.target);
	    this.timeUpdate_();
	}
    }
};

mirosubs.CaptionManager.prototype.timeUpdate_ = function() {
    this.sendEventsForPlayheadTime_(
	this.videoPlayer_.getPlayheadTime());
};

mirosubs.CaptionManager.prototype.sendEventsForPlayheadTime_ =
    function(playheadTime)
{
    if (this.captions_.length == 0)
        return;
    if (this.currentCaptionIndex_ == -1 &&
        playheadTime < this.captions_[0].getStartTime())
        return;

    // we may need to update the current caption index if we have shown at least
    // one caption before AND the slider has been dragged backwards

    if (this.currentCaptionIndex_ > -1) {
        var backedUp = false;
        while (this.currentCaptionIndex_ > -1 &&
               playheadTime < this.captions_[this.currentCaptionIndex_].getStartTime()) {
            backedUp = true;
            this.currentCaptionIndex_--;
        }

        // If we backed up and changed the current caption, display that one instead.
        if (backedUp && this.currentCaptionIndex_ > -1) {
            this.dispatchCaptionEvent_(this.captions_[this.currentCaptionIndex_]);
            return;
        }
    }

    var curCaption = this.currentCaptionIndex_ > -1 ?
        this.captions_[this.currentCaptionIndex_] : null;
    if (this.currentCaptionIndex_ > -1 &&
        curCaption != null &&
	curCaption.isShownAt(playheadTime))
        return;

    var nextCaption = this.currentCaptionIndex_ < this.captions_.length - 1 ?
        this.captions_[this.currentCaptionIndex_ + 1] : null;
    if (nextCaption != null &&
	nextCaption.isShownAt(playheadTime)) {
        this.currentCaptionIndex_++;
        this.dispatchCaptionEvent_(nextCaption);
        return;
    }
    if ((nextCaption == null ||
         playheadTime < nextCaption.getStartTime()) &&
        (curCaption == null ||
         playheadTime >= curCaption.getStartTime())) {
        this.dispatchCaptionEvent_(null);
        if (nextCaption == null && !this.eventsDisabled_)
            this.dispatchEvent(mirosubs.CaptionManager.CAPTIONS_FINISHED);
        return;
    }
    this.sendEventForRandomPlayheadTime_(playheadTime);
};

mirosubs.CaptionManager.prototype.sendEventForRandomPlayheadTime_ =
    function(playheadTime)
{
    var lastCaptionIndex = goog.array.binarySearch(this.captions_,
        playheadTime, this.binaryCompare_);
    if (lastCaptionIndex < 0)
        lastCaptionIndex = -lastCaptionIndex - 2;
    this.currentCaptionIndex_ = lastCaptionIndex;
    if (lastCaptionIndex >= 0 &&
	this.captions_[lastCaptionIndex].isShownAt(playheadTime)) {
        this.dispatchCaptionEvent_(this.captions_[lastCaptionIndex]);
    }
    else {
        this.dispatchCaptionEvent_(null);
    }
};

mirosubs.CaptionManager.prototype.dispatchCaptionEvent_ = function(caption) {
    if (caption == this.lastCaptionDispatched_)
        return;
    if (this.eventsDisabled_)
        return;
    this.lastCaptionDispatched_ = caption;
    this.dispatchEvent(new mirosubs.CaptionManager.CaptionEvent(caption));
};

mirosubs.CaptionManager.prototype.disposeInternal = function() {
    mirosubs.CaptionManager.superClass_.disposeInternal.call(this);
    this.eventHandler_.dispose();
};

mirosubs.CaptionManager.prototype.disableCaptionEvents = function(disabled) {
    this.eventsDisabled_ = disabled;
};

mirosubs.CaptionManager.CaptionEvent = function(editableCaption) {
    this.type = mirosubs.CaptionManager.CAPTION;
    this.caption = editableCaption;
};