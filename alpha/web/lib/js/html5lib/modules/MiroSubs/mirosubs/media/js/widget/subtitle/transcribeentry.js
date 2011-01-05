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

goog.provide('mirosubs.subtitle.TranscribeEntry');

mirosubs.subtitle.TranscribeEntry = function(videoPlayer) {
    goog.ui.Component.call(this);
    this.videoPlayer_ = videoPlayer;
    this.endOfPPlayheadTime_ = null;

    this.wasPlaying_ = false;
    this.continuouslyTyping_ = false;
    this.continuousTypingTimer_ = new goog.Timer(
        mirosubs.subtitle.TranscribeEntry.P * 1000);
    this.typingPauseTimer_ = new goog.Timer(
        mirosubs.subtitle.TranscribeEntry.S * 1000);
    this.playStopTimer_ = new goog.Timer(8000);
};
goog.inherits(mirosubs.subtitle.TranscribeEntry, goog.ui.Component);
mirosubs.subtitle.TranscribeEntry.logger_ =
    goog.debug.Logger.getLogger('mirosubs.subtitle.TranscribeEntry');

mirosubs.subtitle.TranscribeEntry.P = 4;
mirosubs.subtitle.TranscribeEntry.R = 3;
mirosubs.subtitle.TranscribeEntry.S = 1;

mirosubs.subtitle.TranscribeEntry.prototype.createDom = function() {
    mirosubs.subtitle.TranscribeEntry.superClass_.createDom.call(this);
    this.getElement().setAttribute('class', 'mirosubs-transcribeControls');
    this.addChild(this.labelInput_ = new goog.ui.LabelInput(
        'Type subtitle and press enter'), true);
    this.labelInput_.LABEL_CLASS_NAME = 'mirosubs-label-input-label';
    goog.dom.classes.add(this.labelInput_.getElement(), 'trans');
};
mirosubs.subtitle.TranscribeEntry.prototype.enterDocument = function() {
    mirosubs.subtitle.TranscribeEntry.superClass_.enterDocument.call(this);
    this.keyHandler_ = new goog.events.KeyHandler(this.labelInput_.getElement());
    this.getHandler().
        listen(this.keyHandler_,
               goog.events.KeyHandler.EventType.KEY,
               this.handleKey_).
        listen(this.labelInput_.getElement(),
               goog.events.EventType.KEYUP,
               this.handleKeyUp_).
        listen(this.typingPauseTimer_,
               goog.Timer.TICK,
               this.typingPauseTimerTick_).
        listen(this.continuousTypingTimer_,
               goog.Timer.TICK,
               this.continuousTypingTimerTick_).
        listen(this.playStopTimer_,
               goog.Timer.TICK,
               this.playStopTimerTick_).
        listen(this.videoPlayer_,
               mirosubs.video.AbstractVideoPlayer.EventType.PLAY,
              this.startPlaying_);
    mirosubs.subtitle.TranscribeEntry.logger_.info(
        "P is set to " + mirosubs.subtitle.TranscribeEntry.P);
    mirosubs.subtitle.TranscribeEntry.logger_.info(
        "R is set to " + mirosubs.subtitle.TranscribeEntry.R);
    mirosubs.subtitle.TranscribeEntry.logger_.info(
        "S is set to " + mirosubs.subtitle.TranscribeEntry.S);
};
mirosubs.subtitle.TranscribeEntry.prototype.startPlaying_ = function() {
    if (this.playMode_ == mirosubs.subtitle.TranscribePanel.PlayMode.PLAY_STOP) {
        mirosubs.subtitle.TranscribeEntry.logger_.info(
            "Starting play/stop timer in response to play");
        this.playStopTimer_.start();
    }
};
mirosubs.subtitle.TranscribeEntry.prototype.focus = function() {
    if (this.labelInput_.getValue() == '')
        this.labelInput_.focusAndSelect();
    else
        this.labelInput_.getElement().focus();
};
mirosubs.subtitle.TranscribeEntry.prototype.handleKey_ = function(event) {
    if (event.keyCode == goog.events.KeyCodes.ENTER) {
        event.preventDefault();
        this.addNewTitle_();
    }
    else if (event.keyCode != goog.events.KeyCodes.TAB &&
             this.playMode_ == mirosubs.subtitle.TranscribePanel.PlayMode.AUTOPAUSE) {
        this.typingPauseTimer_.stop();
        this.typingPauseTimer_.start();
        if (!this.continuouslyTyping_) {
            mirosubs.subtitle.TranscribeEntry.logger_.info(
                "Continuous typing started.");
            this.continuousTypingTimer_.start();
            this.continuouslyTyping_ = true;
        }
    }
};
mirosubs.subtitle.TranscribeEntry.prototype.continuousTypingTimerTick_ = function() {
    // P seconds since continuous typing was started.
    this.continuousTypingTimer_.stop();
    this.wasPlaying_ = this.videoPlayer_.isPlaying();
    this.videoPlayer_.pause();
    mirosubs.subtitle.TranscribeEntry.logger_.info(
        ["Continuous typing has now progressed for P ",
         "seconds. Pausing video at playhead time ",
         this.videoPlayer_.getPlayheadTime() + ''].join(''));
};
mirosubs.subtitle.TranscribeEntry.prototype.typingPauseTimerTick_ = function() {
    // S seconds since last keystroke!
    var pSecondsElapsed = !this.continuousTypingTimer_.enabled;
    var newPlayheadTime = this.videoPlayer_.getPlayheadTime() -
        mirosubs.subtitle.TranscribeEntry.R;
    mirosubs.subtitle.TranscribeEntry.logger_.info(
        ["Continuous typing ended.",
         pSecondsElapsed ?
         (" Restarting video at playhead time " + newPlayheadTime) :
         ""].join(''));
    this.continuouslyTyping_ = false;
    this.typingPauseTimer_.stop();
    this.continuousTypingTimer_.stop();
    if (pSecondsElapsed && this.wasPlaying_) {
        this.videoPlayer_.setPlayheadTime(newPlayheadTime);
        this.videoPlayer_.play();
    }
};
mirosubs.subtitle.TranscribeEntry.prototype.playStopTimerTick_ = function() {
    mirosubs.subtitle.TranscribeEntry.logger_.info(
        "Pausing the video for play/stop mode");
    this.playStopTimer_.stop();
    this.videoPlayer_.pause();
};
/**
 * 
 * @param {mirosubs.subtitle.TranscribePanel.PlayMode} mode 
 */
mirosubs.subtitle.TranscribeEntry.prototype.setPlayMode = function(mode) {
    mirosubs.subtitle.TranscribeEntry.logger_.info("mode set to " + mode);
    this.playMode_ = mode;
    this.continuouslyTyping_ = false;
    this.continuousTypingTimer_.stop();
    this.typingPauseTimer_.stop();
    this.playStopTimer_.stop();
    if (this.playMode_ == mirosubs.subtitle.TranscribePanel.PlayMode.PLAY_STOP &&
        this.videoPlayer_.isPlaying())
        this.playStopTimer_.start();
};
mirosubs.subtitle.TranscribeEntry.prototype.handleKeyUp_ = function(event) {
    this.videoPlayer_.showCaptionText(this.labelInput_.getValue());
    this.issueLengthWarning_(this.insertsBreakableChar_(event.keyCode));
};
mirosubs.subtitle.TranscribeEntry.prototype.addNewTitle_ = function() {
    var value = this.labelInput_.getValue();
    // FIXME: accessing private member of goog.ui.LabelInput
    this.labelInput_.label_ = '';
    this.labelInput_.setValue('');
    this.labelInput_.focusAndSelect();
    this.dispatchEvent(new mirosubs.subtitle.TranscribeEntry
                       .NewTitleEvent(value));
};
mirosubs.subtitle.TranscribeEntry.prototype.issueLengthWarning_ = 
    function(breakable) 
{
    var MAX_CHARS = 100;
    var length = this.labelInput_.getValue().length;
    if (breakable && length > MAX_CHARS)
        this.addNewTitle_();
    else
        this.getElement().style.background = this.warningColor_(length, 50, MAX_CHARS);
};
mirosubs.subtitle.TranscribeEntry.prototype.warningColor_ =
    function(length, firstChars, maxChars) {

    if (length < firstChars)
        return "#ddd";

    length -= firstChars;
    var r = 15;
    var g = 16 - 16 * length / (maxChars - firstChars);
    var b = 12 - 12 * length / (maxChars - firstChars);
    return ["#", this.hex_(r), this.hex_(g), this.hex_(b)].join('');
};
mirosubs.subtitle.TranscribeEntry.prototype.hex_ = function(num) {
    return goog.math.clamp(Math.floor(num), 0, 15).toString(16);
};

mirosubs.subtitle.TranscribeEntry.prototype.insertsBreakableChar_ = 
    function(key) 
{
    return key == goog.events.KeyCodes.SPACE;
};
mirosubs.subtitle.TranscribeEntry.prototype.disposeInternal = function() {
    mirosubs.subtitle.TranscribeEntry.superClass_.disposeInternal.call(this);
    if (this.keyHandler_)
        this.keyHandler_.dispose();
    this.typingPauseTimer_.dispose();
    this.continuousTypingTimer_.dispose();
};

mirosubs.subtitle.TranscribeEntry.NEWTITLE = 'newtitle';

mirosubs.subtitle.TranscribeEntry.NewTitleEvent = function(title) {
    this.type = mirosubs.subtitle.TranscribeEntry.NEWTITLE;
    this.title = title;
};
