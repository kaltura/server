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

/**
 * @fileoverview A model in true MVC sense: dispatches events when model 
 *     changes. This keeps disparate parts of the UI which are interested 
 *     in model state (e.g. timeline, sync panel, video) informed when 
 *     alterations are made to subtitles.
 */

goog.provide('mirosubs.subtitle.EditableCaptionSet');

/**
 * @constructor
 * @param {array.<object.<string, *>>} existingJsonCaptions No sort order necessary.
 * @param {mirosubs.UnitOfWork=} opt_unitOfWork Unit of work, only provided 
 *     if this EditableCaptionSet is not read-only
 */
mirosubs.subtitle.EditableCaptionSet = function(
    existingJsonCaptions, opt_unitOfWork) 
{
    goog.events.EventTarget.call(this);
    this.unitOfWork_ = opt_unitOfWork;
    var that = this;
    var c;
    this.captions_ = goog.array.map(
        existingJsonCaptions, function(caption) { 
            c = new mirosubs.subtitle.EditableCaption(
                opt_unitOfWork, null, caption);
            c.setParentEventTarget(that);
            return c;
        });
    goog.array.sort(
        this.captions_, 
        mirosubs.subtitle.EditableCaption.orderCompare);
    var i;
    for (i = 1; i < this.captions_.length; i++) {
        this.captions_[i - 1].setNextCaption(this.captions_[i]);
        this.captions_[i].setPreviousCaption(this.captions_[i - 1]);
    }
};
goog.inherits(mirosubs.subtitle.EditableCaptionSet, goog.events.EventTarget);

mirosubs.subtitle.EditableCaptionSet.EventType = {
    CLEAR_ALL: 'clearall',
    CLEAR_TIMES: 'cleartimes',
    ADD: 'addsub',
    DELETE: 'deletesub'
};

/**
 * Always in ascending order by start time.
 */
mirosubs.subtitle.EditableCaptionSet.prototype.captionsWithTimes =
    function() 
{
    return goog.array.filter(
        this.captions_, function(c) { return c.getStartTime() != -1; });
};
/**
 * Always in ascending order by start time.
 */
mirosubs.subtitle.EditableCaptionSet.prototype.timelineCaptions = 
    function() 
{
    return goog.array.filter(
        this.captions_,
        function(c) {
            return c.getStartTime() != -1 || 
                (c.getPreviousCaption() != null &&
                 c.getPreviousCaption().getStartTime() != -1) ||
                (c.getPreviousCaption() == null &&
                 c.getStartTime() == -1);
        });
};
mirosubs.subtitle.EditableCaptionSet.prototype.clear = function() {
    var caption;
    while (this.captions_.length > 0) {
        caption = this.captions_.pop();
        this.unitOfWork_.registerDeleted(caption);
    }
    this.dispatchEvent(
        mirosubs.subtitle.EditableCaptionSet.EventType.CLEAR_ALL);
};
mirosubs.subtitle.EditableCaptionSet.prototype.clearTimes = function() {
    goog.array.forEach(this.captions_, function(c) { c.clearTimes(); });
    
    this.dispatchEvent(
        mirosubs.subtitle.EditableCaptionSet.EventType.CLEAR_TIMES);
};
mirosubs.subtitle.EditableCaptionSet.prototype.count = function() {
    return this.captions_.length;
};
mirosubs.subtitle.EditableCaptionSet.prototype.caption = function(index) {
    return this.captions_[index];
};
mirosubs.subtitle.EditableCaptionSet.prototype.makeJsonSubs = function() {
    return goog.array.map(this.captions_, function(c) { return c.json; });
};
/**
 *
 * @param {Number} nextSubOrder The next subtitle's subOrder 
 *     (returned by EditableCaption#getSubOrder())
 */
mirosubs.subtitle.EditableCaptionSet.prototype.insertCaption = 
    function(nextSubOrder) 
{
    var index = this.findSubIndex_(nextSubOrder);
    var nextSub = this.captions_[index];
    prevSub = nextSub.getPreviousCaption();
    var order = ((prevSub ? prevSub.getSubOrder() : 0.0) + 
                 nextSub.getSubOrder()) / 2.0;
    var c = new mirosubs.subtitle.EditableCaption(
        this.unitOfWork_, order);
    this.unitOfWork_.registerNew(c);
    goog.array.insertAt(this.captions_, c, index);
    if (prevSub) {
        prevSub.setNextCaption(c);
        c.setPreviousCaption(prevSub);
    }
    c.setNextCaption(nextSub);
    nextSub.setPreviousCaption(c);
    this.setTimesOnInsertedSub_(c, prevSub, nextSub);
    c.setParentEventTarget(this);
    this.dispatchEvent(
        new mirosubs.subtitle.EditableCaptionSet.CaptionEvent(
            mirosubs.subtitle.EditableCaptionSet.EventType.ADD,
            c));
    return c;
};
mirosubs.subtitle.EditableCaptionSet.prototype.setTimesOnInsertedSub_ =
    function(insertedSub, prevSub, nextSub)
{
    var startTime = -1, endTime = -1;
    if (nextSub.getStartTime() != -1) {
        startTime = nextSub.getStartTime();
        endTime = (nextSub.getEndTime() + nextSub.getStartTime()) / 2.0;
    }
    else if (prevSub && prevSub.getEndTime() != -1) {
        startTime = prevSub.getEndTime();
    }
    if (startTime != -1) {
        insertedSub.setStartTime(startTime);
        if (endTime != -1)
            insertedSub.setEndTime(endTime);
    }
};
/**
 * 
 * @param {mirosubs.subtitle.EditableCaption} caption
 */
mirosubs.subtitle.EditableCaptionSet.prototype.deleteCaption = function(caption) {
    var index = this.findSubIndex_(caption.getSubOrder());
    var sub = this.captions_[index];
    var prevSub = sub.getPreviousCaption();
    var nextSub = sub.getNextCaption();
    goog.array.removeAt(this.captions_, index);
    if (prevSub)
        prevSub.setNextCaption(nextSub);
    if (nextSub)
        nextSub.setPreviousCaption(prevSub);
    this.unitOfWork_.registerDeleted(sub);
    this.dispatchEvent(
        new mirosubs.subtitle.EditableCaptionSet.CaptionEvent(
            mirosubs.subtitle.EditableCaptionSet.EventType.DELETE,
            sub));
};
mirosubs.subtitle.EditableCaptionSet.prototype.findSubIndex_ = function(order) {
    return goog.array.binarySearch(
        this.captions_, 42, 
        function(x, caption) {
            return order - caption.getSubOrder();
        });
};
mirosubs.subtitle.EditableCaptionSet.prototype.addNewCaption = function(opt_dispatchEvent) {
    var lastSubOrder = 0.0;
    if (this.captions_.length > 0)
        lastSubOrder = this.captions_[this.captions_.length - 1].getSubOrder();
    var c = new mirosubs.subtitle.EditableCaption(
        this.unitOfWork_, lastSubOrder + 1.0);
    c.setParentEventTarget(this);
    this.captions_.push(c);
    if (this.captions_.length > 1) {
        var previousCaption = this.captions_[this.captions_.length - 2];
        previousCaption.setNextCaption(c);
        c.setPreviousCaption(previousCaption);
    }
    this.unitOfWork_.registerNew(c);
    if (opt_dispatchEvent) {
        this.dispatchEvent(
            new mirosubs.subtitle.EditableCaptionSet.CaptionEvent(
                mirosubs.subtitle.EditableCaptionSet.EventType.ADD,
                c));
    }
    return c;
};
/**
 * Find the last subtitle with a start time at or before time.
 * @param {number} time
 * @return {?mirosubs.subtitle.EditableCaption} null if before first 
 *     sub start time, or last subtitle with start time 
 *     at or before playheadTime.
 */
mirosubs.subtitle.EditableCaptionSet.prototype.findLastForTime = 
    function(time) 
{
    var i;
    // TODO: write unit test then get rid of linear search in future.
    for (i = 0; i < this.captions_.length; i++)
        if (this.captions_[i].getStartTime() != -1 &&
            this.captions_[i].getStartTime() <= time &&
            (i == this.captions_.length - 1 ||
             this.captions_[i + 1].getStartTime() == -1 ||
             this.captions_[i + 1].getStartTime() > time))
            return this.captions_[i];
    return null;
};

/**
 * Used for both add and delete.
 */
mirosubs.subtitle.EditableCaptionSet.CaptionEvent = 
    function(type, caption) 
{
    this.type = type;
    /**
     * @type {mirosubs.subtitle.EditableCaption}
     */
    this.caption = caption;
};