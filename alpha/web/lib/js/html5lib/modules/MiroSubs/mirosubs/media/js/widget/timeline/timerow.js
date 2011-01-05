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

goog.provide('mirosubs.timeline.TimeRow');

mirosubs.timeline.TimeRow = function(timelineInner, spacing) {
    goog.ui.Component.call(this);
    this.timelineInner_ = timelineInner;
    this.spacing_ = spacing;
    this.secondsPerUL_ = spacing * mirosubs.timeline.TimeRowUL.NUM_MAJOR_TICKS;
    this.pixelsPerUL_ = mirosubs.timeline.TimeRowUL.NUM_MAJOR_TICKS *
        mirosubs.timeline.TimeRowUL.PX_PER_TICK;
    this.uls_ = [];

    this.openHandStyle_ = goog.style.cursor.getDraggableCursorStyle("../../../images/", true);
    this.closedHandStyle_ = goog.style.cursor.getDraggingCursorStyle("../../../images/", true);
};
goog.inherits(mirosubs.timeline.TimeRow, goog.ui.Component);
mirosubs.timeline.TimeRow.prototype.createDom = function() {
    mirosubs.timeline.TimeRow.superClass_.createDom.call(this);
    var el = this.getElement();
    el.className = 'mirosubs-timerow';
    this.ensureVisible(0);

    el.style.cursor = this.openHandStyle_;

    // Dragger has a default action that cannot be overridden.  Kind of pointless
    // to subclass just to override that, so instead the variable is being
    // overwritten.
    this.dragger_ = new goog.fx.Dragger(el);
    this.dragger_.defaultAction = function(x,y) {};
};
mirosubs.timeline.TimeRow.prototype.enterDocument = function() {
    mirosubs.timeline.Timeline.superClass_.enterDocument.call(this);
    this.getHandler().
        listen(
            this.dragger_,
            goog.fx.Dragger.EventType.BEFOREDRAG,
            goog.bind(this.timelineInner_.beforeDrag, this.timelineInner_)).
        listen(
            this.dragger_,
            goog.fx.Dragger.EventType.START,
            goog.bind(this.timelineInner_.startDrag, this.timelineInner_)).
        listen(
            this.dragger_,
            goog.fx.Dragger.EventType.DRAG,
            goog.bind(this.timelineInner_.onDrag, this.timelineInner_)).
        listen(
            this.dragger_,
            goog.fx.Dragger.EventType.END,
            goog.bind(this.timelineInner_.endDrag, this.timelineInner_));
};
mirosubs.timeline.TimeRow.prototype.ensureVisible = function(time) {
    // always reaching 20 seconds into the future.
    var $d =
        goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    while (this.uls_.length * this.secondsPerUL_ < time + 20) {
        var row = new mirosubs.timeline.TimeRowUL(
            this.spacing_,
            this.uls_.length * this.secondsPerUL_);
        this.addChild(row, true);
        this.uls_.push(row);
    }
};
mirosubs.timeline.TimeRow.prototype.changeCursor = function(closed) {
    if (closed) {
        this.getElement().style.cursor = this.closedHandStyle_;
    }
    else {
        this.getElement().style.cursor = this.openHandStyle_;
    }
}