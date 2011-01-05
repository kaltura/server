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

goog.provide('mirosubs.video.YoutubeVideoPlayer');

/**
 *
 * @param {mirosubs.video.YoutubeVideoSource} videoSource
 * @param {boolean=} opt_chromeless
 */
mirosubs.video.YoutubeVideoPlayer = function(videoSource, opt_chromeless) {
    mirosubs.video.AbstractVideoPlayer.call(this, videoSource);
    this.videoSource_ = videoSource;
    this.playerAPIID_ = [videoSource.getUUID(),
                         '' + new Date().getTime()].join('');
    this.playerElemID_ = videoSource.getUUID() + "_ytplayer";
    this.eventFunction_ = 'event' + videoSource.getUUID();
    this.chromeless_ = !!opt_chromeless;
    this.width_ = mirosubs.video.YoutubeVideoPlayer.REGULAR_WIDTH;
    this.height_ = mirosubs.video.YoutubeVideoPlayer.REGULAR_HEIGHT;

    var readyFunc = goog.bind(this.onYouTubePlayerReady_, this);
    var ytReady = "onYouTubePlayerReady";
    if (window[ytReady]) {
        var oldReady = window[ytReady];
        window[ytReady] = function(playerAPIID) {
            oldReady(playerAPIID);
            readyFunc(playerAPIID);
        };
    }
    else
        window[ytReady] = readyFunc;

    this.player_ = null;
    /**
     * Array of functions to execute once player is ready.
     */
    this.commands_ = [];
    this.swfEmbedded_ = false;
    this.progressTimer_ = new goog.Timer(
        mirosubs.video.AbstractVideoPlayer.PROGRESS_INTERVAL);
    this.timeUpdateTimer_ = new goog.Timer(
        mirosubs.video.AbstractVideoPlayer.TIMEUPDATE_INTERVAL);
};
goog.inherits(mirosubs.video.YoutubeVideoPlayer, mirosubs.video.AbstractVideoPlayer);

mirosubs.video.YoutubeVideoPlayer.REGULAR_HEIGHT = 360;
mirosubs.video.YoutubeVideoPlayer.REGULAR_WIDTH = 480;
mirosubs.video.YoutubeVideoPlayer.SMALL_HEIGHT = 350;
mirosubs.video.YoutubeVideoPlayer.SMALL_WIDTH = 400;


mirosubs.video.YoutubeVideoPlayer.prototype.enterDocument = function() {
    mirosubs.video.YoutubeVideoPlayer.superClass_.enterDocument.call(this);
    if (!this.swfEmbedded_) {
        this.swfEmbedded_ = true;
        var videoDiv = this.getDomHelper().createDom('div');
        videoDiv.id = mirosubs.randomString();
        this.getElement().appendChild(videoDiv);
        var params = { 'allowScriptAccess': 'always', 'wmode' : 'opaque' };
        var atts = { 'id': this.playerElemID_ };
        var baseURL;
        if (this.chromeless_)
            baseURL = 'http://www.youtube.com/apiplayer';
        else
            baseURL = ['http://www.youtube.com/v/',
                       this.videoSource_.getYoutubeVideoID()].join('');
        var queryString =
            ['?enablejsapi=1&version=3&disablekb=1&playerapiid=',
             this.playerAPIID_].join('');
        this.width_ = 
            (this.chromeless_ ? 
             mirosubs.video.YoutubeVideoPlayer.SMALL_WIDTH : 
             mirosubs.video.YoutubeVideoPlayer.REGULAR_WIDTH);
        this.height_ = 
            (this.chromeless_ ? 
             mirosubs.video.YoutubeVideoPlayer.SMALL_HEIGHT : 
             mirosubs.video.YoutubeVideoPlayer.REGULAR_HEIGHT);
        goog.style.setSize(this.getElement(), this.width_, this.height_);
        this.setDimensionsKnownInternal();
        window["swfobject"]["embedSWF"](
            [baseURL, queryString].join(''),
            videoDiv.id, this.width_ + '', this.height_ + '', "8",
            null, null, params, atts);
    }
    this.getHandler().
        listen(this.progressTimer_, goog.Timer.TICK, this.progressTick_).
        listen(this.timeUpdateTimer_, goog.Timer.TICK, this.timeUpdateTick_);
    this.progressTimer_.start();
};
mirosubs.video.YoutubeVideoPlayer.prototype.exitDocument = function() {
    mirosubs.video.YoutubeVideoPlayer.superClass_.exitDocument.call(this);
    this.progressTimer_.stop();
    this.timeUpdateTimer_.stop();
};
mirosubs.video.YoutubeVideoPlayer.prototype.progressTick_ = function(e) {
    if (this.getDuration() > 0)
        this.dispatchEvent(
            mirosubs.video.AbstractVideoPlayer.EventType.PROGRESS);
};
mirosubs.video.YoutubeVideoPlayer.prototype.timeUpdateTick_ = function(e) {
    if (this.getDuration() > 0)
        this.sendTimeUpdateInternal();
};
mirosubs.video.YoutubeVideoPlayer.prototype.onYouTubePlayerReady_ =
    function(playerAPIID)
{
    if (playerAPIID == this.playerAPIID_) {
        this.player_ = goog.dom.$(this.playerElemID_);
        goog.style.setSize(this.player_, this.width_, this.height_);
        if (this.chromeless_)
            this.player_['cueVideoById'](this.videoSource_.getYoutubeVideoID());
        goog.array.forEach(this.commands_, function(cmd) { cmd(); });
        this.commands_ = [];
        window[this.eventFunction_] = goog.bind(this.playerStateChange_, this);
        this.player_.addEventListener('onStateChange', this.eventFunction_);
    }
};
mirosubs.video.YoutubeVideoPlayer.prototype.playerStateChange_ = function(newState) {
    var s = mirosubs.video.YoutubeVideoPlayer.State_;
    var et = mirosubs.video.AbstractVideoPlayer.EventType;
    if (newState == s.PLAYING) {
        this.dispatchEvent(et.PLAY);
        this.timeUpdateTimer_.start();
    }
    else if (newState == s.PAUSED) {
        this.dispatchEvent(et.PAUSE);
        this.timeUpdateTimer_.stop();
    }
    else if (newState == s.ENDED)
        this.dispatchEndedEvent();
};
mirosubs.video.YoutubeVideoPlayer.prototype.getBufferedLength = function() {
    return this.getDuration() > 0  ? 1 : 0;
};
mirosubs.video.YoutubeVideoPlayer.prototype.getBufferedStart = function(index) {
    var startBytes = this.getStartBytes_();
    return this.getDuration() * startBytes / (startBytes + this.getBytesTotal_());
};
mirosubs.video.YoutubeVideoPlayer.prototype.getBufferedEnd = function(index) {
    var startBytes = this.getStartBytes_();
    return this.getDuration() *
        (startBytes + this.player_['getVideoBytesLoaded']()) /
        (startBytes + this.getBytesTotal_());
};
mirosubs.video.YoutubeVideoPlayer.prototype.getStartBytes_ = function() {
    return this.player_ ? this.player_['getVideoStartBytes']() : 0;
};
mirosubs.video.YoutubeVideoPlayer.prototype.getBytesTotal_ = function() {
    return this.player_ ? this.player_['getVideoBytesTotal']() : 0;
};
mirosubs.video.YoutubeVideoPlayer.prototype.getDuration = function() {
    if (!this.duration_)
        this.duration_ = this.player_ ? this.player_['getDuration']() : 0;
    return this.duration_;
};
mirosubs.video.YoutubeVideoPlayer.prototype.getVolume = function() {
    return this.player_ ? (this.player_['getVolume']() / 100) : 0;
};
mirosubs.video.YoutubeVideoPlayer.prototype.setVolume = function(vol) {
    if (this.player_)
        this.player_['setVolume'](vol * 100);
    else
        this.commands_.push(goog.bind(this.setVolume_, this, vol));
};
mirosubs.video.YoutubeVideoPlayer.prototype.isPausedInternal = function() {
    return this.getPlayerState_() == mirosubs.video.YoutubeVideoPlayer.State_.PAUSED;
};
mirosubs.video.YoutubeVideoPlayer.prototype.videoEndedInternal = function() {
    return this.getPlayerState_() == mirosubs.video.YoutubeVideoPlayer.State_.ENDED;
};
mirosubs.video.YoutubeVideoPlayer.prototype.isPlayingInternal = function() {
    return this.getPlayerState_() == mirosubs.video.YoutubeVideoPlayer.State_.PLAYING;
};
mirosubs.video.YoutubeVideoPlayer.prototype.playInternal = function () {
    if (this.player_)
        this.player_['playVideo']();
    else
        this.commands_.push(goog.bind(this.playInternal, this));
};
mirosubs.video.YoutubeVideoPlayer.prototype.pauseInternal = function() {
    if (this.player_)
        this.player_['pauseVideo']();
    else
        this.commands_.push(goog.bind(this.pauseInternal, this));
};
mirosubs.video.YoutubeVideoPlayer.prototype.stopLoadingInternal = function() {
    if (this.player_) {
        this.player_['stopVideo']();
	this.setLoadingStopped(true);
	return true;	
    }
    else {
	this.commands_.push(goog.bind(this.stopLoadingInternal, this));
	return false;
    }
};
mirosubs.video.YoutubeVideoPlayer.prototype.resumeLoadingInternal = function(playheadTime) {
    if (this.player_) {
        this.player_['cueVideoById'](this.videoSource_.getYoutubeVideoID(), playheadTime);
	this.setLoadingStopped(false);
    }
    else
        this.commands_.push(goog.bind(this.resumeLoadingInternal, this, playheadTime));
};
mirosubs.video.YoutubeVideoPlayer.prototype.getPlayheadTime = function() {
    return this.player_ ? this.player_['getCurrentTime']() : 0;
};
mirosubs.video.YoutubeVideoPlayer.prototype.setPlayheadTime = function(playheadTime)
{
    if (this.player_) {
        this.player_['seekTo'](playheadTime, true);
        this.sendTimeUpdateInternal();
    }
    else
        this.commands_.push(goog.bind(this.setPlayheadTime,
                                      this, playheadTime));
};
mirosubs.video.YoutubeVideoPlayer.prototype.getPlayerState_ = function() {
    return this.player_ ? this.player_['getPlayerState']() : -1;
};
mirosubs.video.YoutubeVideoPlayer.prototype.needsIFrame = function() {
    return goog.userAgent.LINUX;
};
mirosubs.video.YoutubeVideoPlayer.prototype.getVideoSize = function() {
    return new goog.math.Size(this.width_, this.height_);
};
mirosubs.video.YoutubeVideoPlayer.prototype.disposeInternal = function() {
    mirosubs.video.YoutubeVideoPlayer.superClass_.disposeInternal.call(this);
    this.progressTimer_.dispose();
    this.timeUpdateTimer_.dispose();
};
/**
 * http://code.google.com/apis/youtube/js_api_reference.html#getPlayerState
 * @enum
 */
mirosubs.video.YoutubeVideoPlayer.State_ = {
    UNSTARTED: -1,
    ENDED: 0,
    PLAYING: 1,
    PAUSED: 2,
    BUFFERING: 3,
    VIDEO_CUED: 5
};