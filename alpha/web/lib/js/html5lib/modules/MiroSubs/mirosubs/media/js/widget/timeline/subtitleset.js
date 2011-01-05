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

goog.provide('mirosubs.timeline.SubtitleSet');

mirosubs.timeline.SubtitleSet = function(editableCaptionSet, videoPlayer) {
    goog.events.EventTarget.call(this);
    this.eventHandler_ = new goog.events.EventHandler(this);
    this.editableCaptionSet_ = editableCaptionSet;
    this.videoPlayer_ = videoPlayer;
    this.createSubsToDisplay_();
    var et = mirosubs.subtitle.EditableCaptionSet.EventType;
    this.eventHandler_.
        listen(
            this.editableCaptionSet_,
            mirosubs.subtitle.EditableCaption.CHANGE,
            this.captionChange_).
        listen(
            this.editableCaptionSet_,
            [et.CLEAR_TIMES, et.ADD, et.DELETE],
            this.subsEdited_);
};
goog.inherits(mirosubs.timeline.SubtitleSet, goog.events.EventTarget);

mirosubs.timeline.SubtitleSet.DISPLAY_NEW = 'displaynew';
mirosubs.timeline.SubtitleSet.CLEAR_TIMES = 'cleartimes';
mirosubs.timeline.SubtitleSet.REMOVE = 'remove';

mirosubs.timeline.SubtitleSet.prototype.getSubsToDisplay = function() {
    return this.subsToDisplay_;
};

mirosubs.timeline.SubtitleSet.prototype.createSubsToDisplay_ = function() {
    if (this.subsToDisplay_)
        this.disposeSubsToDisplay_();
    var that = this;
    this.subsToDisplay_ = goog.array.map(
        this.editableCaptionSet_.timelineCaptions(),
        function(c) {
            return new mirosubs.timeline.Subtitle(
                c, that.videoPlayer_);
        });
    var i;
    for (i = 0; i < this.subsToDisplay_.length - 1; i++)
        this.subsToDisplay_[i].setNextSubtitle(
            this.subsToDisplay_[i + 1]);    
};

mirosubs.timeline.SubtitleSet.prototype.subsEdited_ = function(e) {
    var et = mirosubs.subtitle.EditableCaptionSet.EventType;
    if (e.type == et.CLEAR_TIMES) {
        this.createSubsToDisplay_();
        this.dispatchEvent(mirosubs.timeline.SubtitleSet.CLEAR_TIMES);
    }
    else if (e.type == et.ADD) {
        this.insertCaption_(e.caption);
    }
    else if (e.type == et.DELETE) {
        this.deleteCaption_(e.caption);
    }
};

mirosubs.timeline.SubtitleSet.prototype.deleteCaption_ = function(caption) {
    var subOrder = caption.getSubOrder();
    var index = goog.array.binarySearch(
        this.subsToDisplay_, 42,
        function(x, sub) { return subOrder - sub.getEditableCaption().getSubOrder(); });
    if (index >= 0) {
        var sub = this.subsToDisplay_[index];
        var previousSub = index > 0 ? 
            this.subsToDisplay_[index - 1] : null;
        var nextIsNew = false;
        var nextSub = index < this.subsToDisplay_.length - 1 ? 
            this.subsToDisplay_[index + 1] : null;
        goog.array.removeAt(this.subsToDisplay_, index);
        this.dispatchEvent(new mirosubs.timeline.SubtitleSet.RemoveEvent(sub));
        if (sub.getEditableCaption().getStartTime() == -1) {
            // we just removed the last unsynced subtitle.
            var nextCaption = caption.getNextCaption();
            if (nextCaption != null) {
                nextSub = new mirosubs.timeline.Subtitle(
                    nextCaption, this.videoPlayer_);
                this.subsToDisplay_.push(nextSub);
                nextIsNew = true;
            }
        }
        if (previousSub != null)
            previousSub.setNextSubtitle(nextSub);
        sub.dispose();
        if (nextIsNew)
            this.dispatchEvent(
                new mirosubs.timeline.SubtitleSet.DisplayNewEvent(nextSub));
    }
};

mirosubs.timeline.SubtitleSet.prototype.insertCaption_ = function(caption) {
    if (!this.isInsertable_(caption))
        return;
    var newSub = new mirosubs.timeline.Subtitle(
        caption, this.videoPlayer_);
    var index = goog.array.binarySearch(
        this.subsToDisplay_, newSub, 
        mirosubs.timeline.Subtitle.orderCompare);
    var insertionPoint = -index - 1;
    var previousSub = insertionPoint > 0 ? 
        this.subsToDisplay_[insertionPoint - 1] : null;
    var nextSub = insertionPoint < this.subsToDisplay_.length ? 
        this.subsToDisplay_[insertionPoint] : null;
    if (previousSub != null)
        previousSub.setNextSubtitle(newSub);
    if (nextSub != null) {
        if (caption.getStartTime() == -1) {
            goog.array.removeAt(this.subsToDisplay_, insertionPoint);
            this.dispatchEvent(new mirosubs.timeline.SubtitleSet.RemoveEvent(nextSub));
            nextSub.dispose();
        }
        else
            newSub.setNextSubtitle(nextSub);
    }
    goog.array.insertAt(this.subsToDisplay_, newSub, insertionPoint);
    this.dispatchEvent(
        new mirosubs.timeline.SubtitleSet.DisplayNewEvent(newSub));
};

mirosubs.timeline.SubtitleSet.prototype.isInsertable_ = function(caption) {
    return caption.getStartTime() != -1 ||
        caption.getPreviousCaption() == null ||
        (caption.getPreviousCaption() != null &&
         caption.getPreviousCaption().getStartTime() != -1);
};

mirosubs.timeline.SubtitleSet.prototype.captionChange_ = function(e) {
    if (e.timesFirstAssigned && e.target.getNextCaption() != null) {
        var newSub = new mirosubs.timeline.Subtitle(
            e.target.getNextCaption(), this.videoPlayer_);
        var lastSub = null;
        if (this.subsToDisplay_.length > 0)
            lastSub = this.subsToDisplay_[this.subsToDisplay_.length - 1];
        this.subsToDisplay_.push(newSub);
        if (lastSub != null)
            lastSub.setNextSubtitle(newSub);
        this.dispatchEvent(
            new mirosubs.timeline.SubtitleSet.DisplayNewEvent(newSub));
    }
};

mirosubs.timeline.SubtitleSet.prototype.getEditableCaptionSet = function() {
    return this.editableCaptionSet_;
};

mirosubs.timeline.SubtitleSet.prototype.disposeSubsToDisplay_ = function() {
    goog.array.forEach(this.subsToDisplay_, function(s) { s.dispose(); });    
};

mirosubs.timeline.SubtitleSet.prototype.disposeInternal = function() {
    mirosubs.timeline.SubtitleSet.superClass_.disposeInternal.call(this);
    this.eventHandler_.dispose();
    this.disposeSubsToDisplay_();
};

mirosubs.timeline.SubtitleSet.DisplayNewEvent = function(subtitle) {
    this.type = mirosubs.timeline.SubtitleSet.DISPLAY_NEW;
    this.subtitle = subtitle;
};

mirosubs.timeline.SubtitleSet.RemoveEvent = function(subtitle) {
    this.type = mirosubs.timeline.SubtitleSet.REMOVE;
    this.subtitle = subtitle;
};