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

goog.provide('mirosubs.video.AbstractVideoPlayer');

/**
 * Abstract base class for video player implementations. Any video player 
 * used with MiroSubs must implement the abstract methods defined by this 
 * class.
 * @constructor
 */
mirosubs.video.AbstractVideoPlayer = function(videoSource) {
    goog.ui.Component.call(this);
    this.videoSource_ = videoSource;
    this.noUpdateEvents_ = false;
    this.dimensionsKnown_ = false;
    this.isLoadingStopped_ = false;
    /**
     * If the video's loading is stopped (i.e., it is no longer buffering),
     * we may have had to alter the video source or playhead time to force
     * the buffering to actually cease.  While stopped, this variable will
     * store the playhead time we were previously at, so it can be restored.
     * @type{number}
     */
    this.storedPlayheadTime_ = 0;
};
goog.inherits(mirosubs.video.AbstractVideoPlayer, goog.ui.Component);
mirosubs.video.AbstractVideoPlayer.PROGRESS_INTERVAL = 500;
mirosubs.video.AbstractVideoPlayer.TIMEUPDATE_INTERVAL = 80;

mirosubs.video.AbstractVideoPlayer.prototype.getPlayheadFn = function() {
    return goog.bind(this.getPlayheadTime, this);
};
mirosubs.video.AbstractVideoPlayer.prototype.createDom = function() {
    mirosubs.video.AbstractVideoPlayer.superClass_.createDom.call(this);
    this.getElement().className = 'mirosubs-videoDiv';
};
mirosubs.video.AbstractVideoPlayer.prototype.isPaused = function() {
    if (this.isLoadingStopped_)
	throw new "can't check if paused, loading is stopped";
    return this.isPausedInternal();
};
mirosubs.video.AbstractVideoPlayer.prototype.isPausedInternal = goog.abstractMethod;
mirosubs.video.AbstractVideoPlayer.prototype.isPlaying = function() {
    if (this.isLoadingStopped_)
	throw new "can't check if playing, loading is stopped";
    return this.isPlayingInternal();
};
mirosubs.video.AbstractVideoPlayer.prototype.isPlayingInternal = goog.abstractMethod;
mirosubs.video.AbstractVideoPlayer.prototype.videoEnded = function() {
    if (this.isLoadingStopped_)
	throw new "can't check if video ended, loading is stopped";
    return this.videoEndedInternal();
};
mirosubs.video.AbstractVideoPlayer.prototype.videoEndedInternal = goog.abstractMethod;
mirosubs.video.AbstractVideoPlayer.prototype.play = function(opt_suppressEvent) {
    if (this.isLoadingStopped_)
	throw new "can't play, loading is stopped";
    if (!opt_suppressEvent)
        this.dispatchEvent(
            mirosubs.video.AbstractVideoPlayer.EventType.PLAY_CALLED);
    this.playInternal();
};
mirosubs.video.AbstractVideoPlayer.prototype.playInternal = goog.abstractMethod;
mirosubs.video.AbstractVideoPlayer.prototype.pause = function(opt_suppressEvent) {
    if (this.isLoadingStopped_)
	throw new "can't pause, loading is stopped";
    if (!opt_suppressEvent)
        this.dispatchEvent(
            mirosubs.video.AbstractVideoPlayer.EventType.PAUSE_CALLED);
    this.pauseInternal();
};
mirosubs.video.AbstractVideoPlayer.prototype.pauseInternal = goog.abstractMethod;
mirosubs.video.AbstractVideoPlayer.prototype.togglePause = function() {
    if (this.isLoadingStopped_)
	throw new "can't toggle pause, loading is stopped";

    if (!this.isPlaying())
        this.play();
    else
        this.pause();
};
mirosubs.video.AbstractVideoPlayer.prototype.isLoadingStopped = function() {
  return this.isLoadingStopped_;  
};
/**
 * @protected
 */
mirosubs.video.AbstractVideoPlayer.prototype.setLoadingStopped = function(isLoadingStopped) {
  this.isLoadingStopped_ = isLoadingStopped;  
};
mirosubs.video.AbstractVideoPlayer.prototype.stopLoading = function() {
    if (!this.isLoadingStopped_) {
	this.pause();
	this.storedPlayheadTime_ = this.getPlayheadTime();
	if (this.stopLoadingInternal()) {
	    this.isLoadingStopped_ = true;
	}
    }
};
mirosubs.video.AbstractVideoPlayer.prototype.stopLoadingInternal = goog.abstractMethod;
mirosubs.video.AbstractVideoPlayer.prototype.resumeLoading = function() {
    if (this.isLoadingStopped_) {
	this.resumeLoadingInternal(this.storedPlayheadTime_);
	this.storedPlayheadTime_ = null;
    }
};
mirosubs.video.AbstractVideoPlayer.prototype.resumeLoadingInternal = function(playheadTime) {
    goog.abstractMethod();
};
/**
 * @protected
 * Must be called by subclasses once they know their dimensions.
 */
mirosubs.video.AbstractVideoPlayer.prototype.setDimensionsKnownInternal = function() {
    this.dimensionsKnown_ = true;
    this.dispatchEvent(
        mirosubs.video.AbstractVideoPlayer.EventType.DIMENSIONS_KNOWN);
};
mirosubs.video.AbstractVideoPlayer.prototype.areDimensionsKnown = function() {
    return this.dimensionsKnown_;
};
mirosubs.video.AbstractVideoPlayer.prototype.getPlayheadTime = function() {
    if (this.isLoadingStopped_)
	return this.storedPlayheadTime_;
    return this.getPlayheadTimeInternal();
};
mirosubs.video.AbstractVideoPlayer.prototype.getPlayheadTimeInternal = goog.abstractMethod;
/**
 * 
 *
 */
mirosubs.video.AbstractVideoPlayer.prototype.playWithNoUpdateEvents = 
    function(timeToStart, secondsToPlay) 
{
    this.noUpdateEvents_ = true;
    if (this.noUpdatePreTime_ == null)
        this.noUpdatePreTime_ = this.getPlayheadTime();
    this.setPlayheadTime(timeToStart);
    this.play();
    this.noUpdateStartTime_ = timeToStart;
    this.noUpdateEndTime_ = timeToStart + secondsToPlay;
};
/**
 * @protected
 */
mirosubs.video.AbstractVideoPlayer.prototype.dispatchEndedEvent = function() {
    this.dispatchEvent(mirosubs.video.AbstractVideoPlayer.EventType.PLAY_ENDED);
};
mirosubs.video.AbstractVideoPlayer.prototype.sendTimeUpdateInternal = 
    function() 
{
    if (!this.noUpdateEvents_)
        this.dispatchEvent(
            mirosubs.video.AbstractVideoPlayer.EventType.TIMEUPDATE);
    else {
        if (this.ignoreTimeUpdate_)
            return;
        if (this.getPlayheadTime() >= this.noUpdateEndTime_) {
            this.ignoreTimeUpdate_ = true;
            this.setPlayheadTime(this.noUpdatePreTime_);
            this.noUpdatePreTime_ = null;
            this.pause();
            this.ignoreTimeUpdate_ = false;
            this.noUpdateEvents_ = false;
        }
    }
}
/**
 * @returns {number} video duration in seconds. Returns 0 if duration isn't
 *     available yet.
 */
mirosubs.video.AbstractVideoPlayer.prototype.getDuration = goog.abstractMethod;
/**
 * @returns {int} Number of buffered ranges.
 */
mirosubs.video.AbstractVideoPlayer.prototype.getBufferedLength = goog.abstractMethod;
/**
 * @returns {number} Start of buffered range with index
 */
mirosubs.video.AbstractVideoPlayer.prototype.getBufferedStart = function(index) {
    goog.abstractMethod();
};
mirosubs.video.AbstractVideoPlayer.prototype.getBufferedEnd = function(index) {
    goog.abstractMethod();
};
/**
 * @returns {number} 0.0 to 1.0
 */
mirosubs.video.AbstractVideoPlayer.prototype.getVolume = goog.abstractMethod;
/**
 * 
 * @param {number} volume A number between 0.0 and 1.0
 */
mirosubs.video.AbstractVideoPlayer.prototype.setVolume = function(volume) {
    goog.abstractMethod();
};
mirosubs.video.AbstractVideoPlayer.prototype.getVideoSource = function() {
    return this.videoSource_;
};
mirosubs.video.AbstractVideoPlayer.prototype.setPlayheadTime = function(playheadTime) {
    goog.abstractMethod();
};
/**
 *
 * @param {String} text Caption text to display in video. null for blank.
 */
mirosubs.video.AbstractVideoPlayer.prototype.showCaptionText = function(text) {
    // TODO: shorten this method and use the google closure library!
    if (text == null || text == "") {
        if (this.captionElem_ != null) {
            this.getElement().removeChild(this.captionElem_);
            this.captionElem_ = null;
            if (this.captionBgElem_ != null) {
                this.getElement().removeChild(this.captionBgElem_);
                this.captionBgElem_ = null;
            }
        }
    }
    else {
        var needsIFrame = this.needsIFrame();
        text = goog.string.newLineToBr(goog.string.htmlEscape(text));
        var videoSize = this.getVideoSize();
        if (this.captionElem_ == null) {
            this.captionElem_ = document.createElement("div");
            this.captionElem_.setAttribute("class", "mirosubs-captionDiv");
            this.captionElem_.style.top = (videoSize.height - 100) + "px";
            this.captionElem_.style.width = (videoSize.width - 80) + "px";
            if (needsIFrame)
                this.captionElem_.style.visibility = 'hidden';
            this.getElement().appendChild(this.captionElem_);
        }
        this.captionElem_.innerHTML = text;
        this.captionElem_.style.top = (videoSize.height - this.captionElem_.offsetHeight - 40) + "px";
        if (needsIFrame) {
            var $d = goog.dom;
            var $s = goog.style;
            if (this.captionBgElem_ == null) {
                this.captionBgElem_ = document.createElement('iframe');
                this.captionBgElem_.setAttribute("class", "mirosubs-captionDivBg");
                this.captionBgElem_.style.visibility = 'hidden';
                this.captionBgElem_.style.top = this.captionElem_.style.top;
                // FIXME: get rid of hardcoded value
                this.captionBgElem_.style.left = "10px";
                $d.insertSiblingBefore(this.captionBgElem_, 
                                       this.captionElem_);
            }
            var size = $s.getSize(this.captionElem_);
            $s.setSize(this.captionBgElem_, size.width, size.height);
            this.captionBgElem_.style.visibility = 'visible';
            this.captionElem_.style.visibility = 'visible';
        }
    }
};
/**
 * Override for video players that need to show an iframe behind captions for 
 * certain browsers (e.g. flash on linux/ff)
 * @protected
 */
mirosubs.video.AbstractVideoPlayer.prototype.needsIFrame = function() {
    return false;
};
/**
 *
 * @protected
 * @return {goog.math.Size} size of the video
 */
mirosubs.video.AbstractVideoPlayer.prototype.getVideoSize = goog.abstractMethod;

/**
 * Video player events
 * @enum {string}
 */
mirosubs.video.AbstractVideoPlayer.EventType = {
    /** dispatched when playback starts or resumes. */
    PLAY : 'videoplay',
    PLAY_CALLED : 'videoplaycalled',
    /** dispatched when the video finishes playing. */
    PLAY_ENDED : 'videoplayended',
    /** dispatched when playback is paused. */
    PAUSE : 'videopause',
    PAUSE_CALLED : 'videopausecalled',
    /** dispatched when media data is fetched */
    PROGRESS : 'videoprogress',
    /** 
     * dispatched when playhead time changes, either as a result 
     *  of normal playback or otherwise. 
     */
    TIMEUPDATE : 'videotimeupdate',
    /**
     * dispatched when video dimensions are known.
     */
    DIMENSIONS_KNOWN: 'dimensionsknown'
};
