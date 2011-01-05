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

goog.provide('mirosubs.Spinner');

/**
 * 
 * @param {number} value
 * @param {function():number} minFn A function that returns the 
 *     min value for this spinner.
 * @param {function():number} maxFn A function that returns the 
 *     max value for this spinner.
 * @param {function(number):string} valueExpression
 */
mirosubs.Spinner = function(value, minFn, maxFn, valueExpression) {
    goog.ui.Component.call(this);
    this.timer_ = new goog.Timer(100);
    this.speed_ = mirosubs.Spinner.INITIAL_SPEED;
    this.counter_ = 0;
    this.value_ = value;
    this.minFn_ = minFn;
    this.maxFn_ = maxFn;
    this.maxStep_ = 0.10;
    this.minStep_ = 0.05;
    this.stepIncrease_ = 0.05;
    this.step_ = this.minStep_;
    this.enabled_ = true;
    this.increment_ = true;
    this.valueExpression_ = valueExpression;
    /**
     * True iff the user has mouse down on an arrow.
     */
    this.activated_ = false;
};
goog.inherits(mirosubs.Spinner, goog.ui.Component);

mirosubs.Spinner.EventType = {
    /**
     * Dispatched when arrow button is first pressed.
     */
    ARROW_PRESSED: "arrowPressed",
    /**
     * Dispatched when arrow button is let go of, either by 
     * mouseup or mouseout
     */
    VALUE_CHANGED: "valueChanged"
};
mirosubs.Spinner.INITIAL_SPEED = 4;
mirosubs.Spinner.prototype.createDom = function() {
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.valueSpan_ = $d('span', 'mirosubs-timestamp-time');
    this.upAnchor_ = 
        $d('a', {'className': 'mirosubs-up', 'href':'#'}, "Up");
    this.downAnchor_ = 
        $d('a', {'className': 'mirosubs-down', 'href':'#'}, "Down");
    this.setElementInternal(
        $d('span', null,
           this.valueSpan_,
           $d('span', 'mirosubs-changeTime',
              this.upAnchor_,
              this.downAnchor_)));
    this.updateText_();
};
mirosubs.Spinner.prototype.enterDocument = function() {
    mirosubs.Spinner.superClass_.enterDocument.call(this);
    goog.array.forEach(
        [this.upAnchor_, this.downAnchor_],
        this.addAnchorEventHandlers_, this);
    this.getHandler().listen(this.timer_, goog.Timer.TICK, this.timerTick_);
};
mirosubs.Spinner.prototype.addAnchorEventHandlers_ = function(elem) {
    var et = goog.events.EventType;
    this.getHandler().
        listen(elem, et.CLICK, function(e) { e.preventDefault(); }).
        listen(elem, et.MOUSEDOWN, this.mouseDown_).
        listen(elem, et.MOUSEUP, this.mouseUp_).
        listen(elem, et.MOUSEOUT, this.mouseOut_);
};
mirosubs.Spinner.prototype.updateText_ = function() {
    goog.dom.setTextContent(this.valueSpan_, 
                            this.valueExpression_(this.value_));
};
mirosubs.Spinner.prototype.cancelTimer_ = function() {
    this.activated_ = false;
    this.timer_.stop();
    this.speed_ = mirosubs.Spinner.INITIAL_SPEED;
    this.counter_ = 0;
    this.step_ = this.minStep_;
    this.dispatchEvent(new mirosubs.Spinner.ValueChangedEvent(this.value_));
};
mirosubs.Spinner.prototype.timerTick_ = function(event) {
    this.counter_++;
    if (this.speed_ <= 0 || this.counter_ % this.speed_ == 0) {
        if (this.counter_ > 10) {
            this.speed_--;
            this.counter_ = 0;
        }
        if (this.increment_)
            this.increase_();
        else
            this.decrease_();
    }
    if (this.speed_ < 0 && this.step_ < this.maxStep_)
        this.step_ += this.stepIncrease_;
};
mirosubs.Spinner.prototype.mouseDown_ = function(event) {
    if (this.enabled_) {
        this.dispatchEvent(mirosubs.Spinner.EventType.ARROW_PRESSED);
        this.activated_ = true;
        if (event.target == this.upAnchor_) {
            this.increment_ = true;
            this.increase_();
        }
        else {
            this.increment_ = false;
            this.decrease_();
        }
        this.timer_.start();
    };
};
mirosubs.Spinner.prototype.mouseUp_ = function(event) {
    if (this.activated_)
        this.cancelTimer_();
};
mirosubs.Spinner.prototype.mouseOut_ = function(event) {
    if (this.activated_)
        this.cancelTimer_();    
};
mirosubs.Spinner.prototype.setValue = function(value) {
    this.value_ = value;
    this.updateText_();
};
mirosubs.Spinner.prototype.decrease_ = function() {
    this.value_ -= this.step_;
    if (this.value_ < this.minFn_()) {
        this.value_ = this.minFn_();
        this.cancelTimer_();
    }
    this.updateText_();
};
mirosubs.Spinner.prototype.increase_ = function() {
    this.value_ += this.step_;
    if (this.value_ > this.maxFn_()) {
        this.value_ = this.maxFn_();
        this.cancelTimer_();
    }
    this.updateText_();
};
mirosubs.Spinner.prototype.disposeInternal = function() {
    mirosubs.Spinner.superClass_.disposeInternal.call(this);
    this.timer_.dispose();
    this.valueExpression_ = null;
};
mirosubs.Spinner.ValueChangedEvent = function(value) {
    this.type = mirosubs.Spinner.EventType.VALUE_CHANGED;
    this.value = value;
};
