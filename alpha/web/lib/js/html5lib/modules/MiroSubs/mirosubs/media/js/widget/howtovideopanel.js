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

goog.provide('mirosubs.HowToVideoPanel');

/**
 * @constructor
 * @param {mirosubs.HowToVideoPanel.VideoChoice} videoChoice
 */
mirosubs.HowToVideoPanel = function(videoChoice) {
    goog.ui.Component.call(this);
    if (mirosubs.video.supportsOgg())
        this.videoPlayer_ = (new mirosubs.video.Html5VideoSource(
            videoChoice.videos.ogg)).createPlayer();
    else if (mirosubs.video.supportsH264())
        this.videoPlayer_ = (new mirosubs.video.Html5VideoSource(
            videoChoice.videos.h264)).createPlayer();
    else
        this.videoPlayer_ = (new mirosubs.video.YoutubeVideoSource(
            videoChoice.videos.yt)).createPlayer();
    this.howToImageURL_ = mirosubs.imageAssetURL(videoChoice.image);
    this.usingHtml5Video_ = 
        mirosubs.video.supportsOgg() ||
        mirosubs.video.supportsH264();
};
goog.inherits(mirosubs.HowToVideoPanel, goog.ui.Component);

mirosubs.HowToVideoPanel.CONTINUE = 'continue';
mirosubs.HowToVideoPanel.VideoChoice = {
    TRANSCRIBE: {
        videos: {
            ogg: 'http://blip.tv/file/get/Miropcf-tutorialstep1573.ogv',
            h264: 'http://blip.tv/file/get/Miropcf-tutorialstep1328.mp4',
            yt: '0MCpmace_lc'
        },
        image: 'howto-step1.png'
    },
    SYNC: {
        videos: {
            ogg: 'http://blip.tv/file/get/Miropcf-tutorialstep2876.ogv',
            h264: 'http://blip.tv/file/get/Miropcf-tutorialstep2530.mp4',
            yt: 'bkwiFF-I2nI'
        },
        image: 'howto-step2.png'
    },
    REVIEW: {
        videos: {
            ogg: 'http://blip.tv/file/get/Miropcf-tutorialstep3571.ogv',
            h264: 'http://blip.tv/file/get/Miropcf-tutorialstep3146.mp4',
            yt: 'Y5vGEGKMkMk'
        },
        image: 'howto-step3.png'
    }
};

mirosubs.HowToVideoPanel.HTML5_VIDEO_SIZE_ =
    new goog.math.Size(512, 384);

mirosubs.HowToVideoPanel.prototype.getContentElement = function() {
    return this.contentElement_;
};

mirosubs.HowToVideoPanel.prototype.createDom = function() {
    mirosubs.HowToVideoPanel.superClass_.createDom.call(this);
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.contentElement_ = $d('div');
    var el = this.getElement();
    el.className = 'mirosubs-howtopanel';
    el.appendChild(this.contentElement_);
    this.skipVideosSpan_ = $d('span');
    el.appendChild($d('div', null, this.skipVideosSpan_,
                      goog.dom.createTextNode(' Skip these videos')));
    this.continueLink_ = 
        $d('a', 
           {'className': 'mirosubs-done', 
            'href': '#'}, 
           $d('span', null, 'Continue'))
    el.appendChild(this.continueLink_);
    var vidPlayer = new goog.ui.Component();
    vidPlayer.addChild(this.videoPlayer_, true);
    this.howToImage_ = $d('img', 
                          {'src': this.howToImageURL_, 
                           'className': 'mirosubs-howto-image'});
    vidPlayer.getElement().appendChild(this.howToImage_);
    this.addChild(vidPlayer, true);
    vidPlayer.getElement().className = 'mirosubs-howto-videocontainer';
    var videoSize;
    if (this.usingHtml5Video_) {
        var viewportSize = goog.dom.getViewportSize();
        var videoTop = 
            Math.max(0, goog.style.getClientLeftTop(
                this.videoPlayer_.getElement()).y);
        videoSize = mirosubs.HowToVideoPanel.HTML5_VIDEO_SIZE_;
        if (videoTop + videoSize.height > viewportSize.height - 60) {
            var newVideoHeight = 
                Math.max(270, viewportSize.height - videoTop - 60);
            var newVideoWidth = 
                videoSize.width * newVideoHeight / videoSize.height;
            videoSize = new goog.math.Size(
                newVideoWidth, newVideoHeight);
        }
        this.videoPlayer_.setVideoSize(videoSize.width, videoSize.height);
    }
    else
        videoSize = this.videoPlayer_.getVideoSize();
    goog.style.setSize(vidPlayer.getElement(), videoSize.width, videoSize.height);
    goog.style.setSize(this.howToImage_, videoSize.width, videoSize.height);
};

mirosubs.HowToVideoPanel.prototype.enterDocument = function() {
    mirosubs.HowToVideoPanel.superClass_.enterDocument.call(this);
    if (!this.skipVideosCheckbox_) {
        this.skipVideosCheckbox_ = new goog.ui.Checkbox();
        this.skipVideosCheckbox_.decorate(this.skipVideosSpan_);
        this.skipVideosCheckbox_.setLabel(
            this.skipVideosCheckbox_.getElement().parentNode);
    }
    this.getHandler().listen(this.skipVideosCheckbox_,
                             goog.ui.Component.EventType.CHANGE,
                             this.skipVideosCheckboxChanged_);
    this.getHandler().listen(this.continueLink_, 'click', this.continue_);
    this.getHandler().listen(this.howToImage_, 'click', this.startPlaying_);
};

mirosubs.HowToVideoPanel.prototype.startPlaying_ = function(e) {
    e.preventDefault();
    goog.dom.removeNode(this.howToImage_);
    this.videoPlayer_.play();
};

mirosubs.HowToVideoPanel.prototype.skipVideosCheckboxChanged_ = function(e) {
    mirosubs.UserSettings.setBooleanValue(
        mirosubs.UserSettings.Settings.SKIP_HOWTO_VIDEO,
        this.skipVideosCheckbox_.getChecked());
};

mirosubs.HowToVideoPanel.prototype.continue_ = function(e) {
    e.preventDefault();
    this.dispatchEvent(mirosubs.HowToVideoPanel.CONTINUE);
};

mirosubs.HowToVideoPanel.prototype.stopVideo = function() {
    this.videoPlayer_.pause();
    this.videoPlayer_.dispose();
};

mirosubs.HowToVideoPanel.prototype.disposeInternal = function() {
    mirosubs.HowToVideoPanel.superClass_.disposeInternal.call(this);
};