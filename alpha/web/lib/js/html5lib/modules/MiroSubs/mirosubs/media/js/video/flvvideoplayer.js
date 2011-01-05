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

goog.provide('mirosubs.video.FlvVideoPlayer');

mirosubs.video.FlvVideoPlayer = function(videoSource, opt_chromeless) {
    mirosubs.video.AbstractVideoPlayer.call(this, videoSource);

    this.chromeless_ = !!opt_chromeless;
    this.videoSource_ = videoSource;
    this.swfEmbedded_ = false;
    this.swfLoaded_ = false;
    /**
     * 
     */
    this.commands_ = [];
    this.progressTimer_ = new goog.Timer(
        mirosubs.video.AbstractVideoPlayer.PROGRESS_INTERVAL);
    this.timeUpdateTimer_ = new goog.Timer(
        mirosubs.video.AbstractVideoPlayer.TIMEUPDATE_INTERVAL);
};
goog.inherits(mirosubs.video.FlvVideoPlayer,
              mirosubs.video.AbstractVideoPlayer);

// FIXME: these are duplicated in youtubevideoplayer.js
mirosubs.video.FlvVideoPlayer.REGULAR_HEIGHT = 360;
mirosubs.video.FlvVideoPlayer.REGULAR_WIDTH = 480;
mirosubs.video.FlvVideoPlayer.SMALL_HEIGHT = 350;
mirosubs.video.FlvVideoPlayer.SMALL_WIDTH = 400;

mirosubs.video.FlvVideoPlayer.prototype.enterDocument = function() {
    mirosubs.video.FlvVideoPlayer.superClass_.enterDocument.call(this);
    if (!this.swfEmbedded_) {
        this.swfEmbedded_ = true;
        var videoDiv = this.getDomHelper().createDom('div');
        videoDiv.id = mirosubs.randomString();
        this.getElement().appendChild(videoDiv);
        this.width_ = 
            (this.chromeless_ ?
             mirosubs.video.FlvVideoPlayer.SMALL_WIDTH :
             mirosubs.video.FlvVideoPlayer.REGULAR_WIDTH);
        this.height_ =
            (this.chromeless_ ?
             mirosubs.video.FlvVideoPlayer.SMALL_HEIGHT :
             mirosubs.video.FlvVideoPlayer.REGULAR_HEIGHT);
        this.setDimensionsKnownInternal();
        var flashEmbedParams = {
            'src': mirosubs.mediaURL() + 'flowplayer/flowplayer-3.2.2.swf',
            'width': this.width_,
            'height': this.height_,
            'wmode': 'opaque'
        };
        var that = this;
        var config = {
            'playlist': [{ 
                'url': this.videoSource_.getFlvURL(), 
                'autoPlay': false
            }],
            'onLoad': function() {
                that.swfFinishedLoading_();
            }
        };
        if (this.chromeless_)
            config['plugins'] = { 'controls': null };
        this.player_ = $f(
            videoDiv.id, flashEmbedParams, config);
    }
    this.getHandler().
        listen(this.progressTimer_, goog.Timer.TICK, this.progressTick_).
        listen(this.timeUpdateTimer_, goog.Timer.TICK, this.timeUpdateTick_);
    this.progressTimer_.start();
};

mirosubs.video.FlvVideoPlayer.prototype.exitDocument = function() {
    mirosubs.video.FlvVideoPlayer.superClass_.exitDocument.call(this);
    this.timeUpdateTimer_.stop();
    this.progressTimer_.stop();
};

mirosubs.video.FlvVideoPlayer.prototype.swfFinishedLoading_ = function() {
    this.swfLoaded_ = true;
    goog.array.forEach(this.commands_, function(c) { c(); });
    this.commands_ = [];
    var that = this;
    this.getClip_()['onStart'](function() {
        that.onPlay_();
    });
    this.getClip_()['onResume'](function() {
        that.onPlay_();
    });
    this.getClip_()['onPause'](function() {
        that.onPause_();
    });
    this.getClip_()['onFinish'](function() {
        that.dispatchEndedEvent();
    });
};

mirosubs.video.FlvVideoPlayer.prototype.progressTick_ = function(e) {
    if (this.getDuration() > 0) {
        this.refreshStatus_();
        if (this.status_['bufferEnd'] >= this.getDuration() - 0.10)
            this.progressTimer_.stop();
        this.dispatchEvent(mirosubs.video.AbstractVideoPlayer.EventType.PROGRESS);
    }
};

mirosubs.video.FlvVideoPlayer.prototype.refreshStatus_ = function() {
    this.status_ = this.player_['getStatus']();
};

mirosubs.video.FlvVideoPlayer.prototype.timeUpdateTick_ = function(e) {
    if (this.getDuration() > 0)
        this.sendTimeUpdateInternal();
};

mirosubs.video.FlvVideoPlayer.prototype.onPlay_ = function() {
    this.dispatchEvent(mirosubs.video.AbstractVideoPlayer.EventType.PLAY);
    this.timeUpdateTimer_.start();
};

mirosubs.video.FlvVideoPlayer.prototype.onPause_ = function() {
    this.dispatchEvent(mirosubs.video.AbstractVideoPlayer.EventType.PAUSE);
    this.timeUpdateTimer_.stop();
};

mirosubs.video.FlvVideoPlayer.prototype.getClip_ = function() {
    return this.player_['getClip'](0);
};

mirosubs.video.FlvVideoPlayer.prototype.getBufferedLength = function() {
    return this.getDuration() > 0 ? 1 : 0;
};

mirosubs.video.FlvVideoPlayer.prototype.getBufferedStart = function(index) {
    if (!this.status_)
        this.refreshStatus_();
    return this.status_['bufferStart'];
};

mirosubs.video.FlvVideoPlayer.prototype.getBufferedEnd = function(index) {
    if (!this.status_)
        this.refreshStatus_();
    return this.status_['bufferEnd'];
};

mirosubs.video.FlvVideoPlayer.prototype.getDuration = function() {
    if (!this.duration_) {
        this.duration_ = this.swfLoaded_ ? this.getClip_()['fullDuration'] : 0;
        if (isNaN(this.duration_))
            this.duration_ = 0;
    }
    return this.duration_;
};

mirosubs.video.FlvVideoPlayer.prototype.getVolume = function() {
    return this.swfLoaded_ ? (this.player_['getVolume']() / 100) : 0;
};

mirosubs.video.FlvVideoPlayer.prototype.setVolume = function(vol) {
    if (this.swfLoaded_)
        this.player_['setVolume'](vol * 100);
    else
        this.commands_.push(goog.bind(this.setVolume_, this, vol));
};

mirosubs.video.FlvVideoPlayer.prototype.isPausedInternal = function() {
    return this.swfLoaded_ ? this.player_['isPaused']() : false;
};

mirosubs.video.FlvVideoPlayer.prototype.videoEndedInternal = function() {
    return this.swfLoaded_ ? (this.player_['getState']() == 5) : false;
};

mirosubs.video.FlvVideoPlayer.prototype.isPlayingInternal = function() {
    return this.swfLoaded_ ? this.player_['isPlaying']() : false;
};

mirosubs.video.FlvVideoPlayer.prototype.playInternal = function() {
    if (this.swfLoaded_) {
        if (!this.isPlaying())
            this.player_['play']();
    }
    else
        this.commands_.push(goog.bind(this.playInternal, this));
};

mirosubs.video.FlvVideoPlayer.prototype.pauseInternal = function() {
    if (this.swfLoaded_)
        this.player_['pause']();
    else
        this.commands_.push(goog.bind(this.pauseInternal, this));
};

mirosubs.video.FlvVideoPlayer.prototype.stopLoadingInternal = function() {
    if (this.swfLoaded_) {
        this.player_['stopBuffering']();
	this.setLoadingStopped(true);
	return true;
    }
    else {
        this.commands_.push(goog.bind(this.stopLoadingInternal, this));
	return false;
    }
};

mirosubs.video.FlvVideoPlayer.prototype.resumeLoadingInternal = function(playheadTime) {
    if (this.swfLoaded_) {
        this.player_['startBuffering']();
	this.setLoadingStopped(false);
    }
    else
        this.commands_.push(goog.bind(this.resumeLoadingInternal, this, playheadTime));
};

mirosubs.video.FlvVideoPlayer.prototype.getPlayheadTimeInternal = function() {
    return this.swfLoaded_ ? this.player_['getTime']() : 0;
};

mirosubs.video.FlvVideoPlayer.prototype.setPlayheadTime = function(time) {
    if (this.swfLoaded_) {
        this.player_['seek'](time);
        this.sendTimeUpdateInternal();
    }
    else
        this.commands_.push(goog.bind(this.setPlayheadTime, this, time));
};

mirosubs.video.FlvVideoPlayer.prototype.needsIFrame = function() {
    return goog.userAgent.LINUX;
};

mirosubs.video.FlvVideoPlayer.prototype.getVideoSize = function() {
    return new goog.math.Size(this.width_, this.height_);
};

mirosubs.video.FlvVideoPlayer.prototype.disposeInternal = function() {
    mirosubs.video.FlvVideoPlayer.superClass_.disposeInternal.call(this);
    this.progressTimer_.dispose();
    this.timeUpdateTimer_.dispose();
};