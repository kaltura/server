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

goog.provide('mirosubs.video.YoutubeVideoSource');

/**
 * @constructor
 */
mirosubs.video.YoutubeVideoSource = function(youtubeVideoID) {
    this.youtubeVideoID_ = youtubeVideoID;
    this.uuid_ = mirosubs.randomString();
};

mirosubs.video.YoutubeVideoSource.counter_ = 0;

mirosubs.video.YoutubeVideoSource.prototype.createPlayer = function() {
    return this.createPlayer_(false);
};

mirosubs.video.YoutubeVideoSource.prototype.createControlledPlayer = 
    function() 
{
    return new mirosubs.video.ControlledVideoPlayer(this.createPlayer_(true));
};

mirosubs.video.YoutubeVideoSource.prototype.createPlayer_ = function(chromeless) {
    return new mirosubs.video.YoutubeVideoPlayer(
        new mirosubs.video.YoutubeVideoSource(
            this.youtubeVideoID_), chromeless);
};

mirosubs.video.YoutubeVideoSource.prototype.getYoutubeVideoID = function() {
    return this.youtubeVideoID_;
};

mirosubs.video.YoutubeVideoSource.prototype.getUUID = function() {
    return this.uuid_;
};