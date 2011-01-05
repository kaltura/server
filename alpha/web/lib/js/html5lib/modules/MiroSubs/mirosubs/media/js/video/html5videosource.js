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

goog.provide('mirosubs.video.Html5VideoSource');

/**
 * @constructor
 * @param {string} videoURL
 * @param {mirosubs.video.Html5VideoType} videoType
 */
mirosubs.video.Html5VideoSource = function(videoURL, videoType, opt_videoConfig) {
    this.videoURL_ = videoURL;
    this.videoType_ = videoType;
    this.videoConfig_ = opt_videoConfig;
};

mirosubs.video.Html5VideoSource.prototype.createPlayer = function() {
    return this.createPlayer_(false);
};

mirosubs.video.Html5VideoSource.prototype.createControlledPlayer = 
    function() 
{
    return new mirosubs.video.ControlledVideoPlayer(
        this.createPlayer_(true));
};

mirosubs.video.Html5VideoSource.prototype.createPlayer_ = 
    function(forSubDialog) 
{
    return new mirosubs.video.Html5VideoPlayer(
        new mirosubs.video.Html5VideoSource(
            this.videoURL_, this.videoType_), 
        forSubDialog, this.videoConfig_);
};

mirosubs.video.Html5VideoSource.prototype.getVideoURL = function() {
    return this.videoURL_;
};

mirosubs.video.Html5VideoSource.prototype.getVideoType = function() {
    return this.videoType_;
};