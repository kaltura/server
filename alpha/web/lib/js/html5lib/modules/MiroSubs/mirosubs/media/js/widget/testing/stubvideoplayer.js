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

goog.provide('mirosubs.testing.StubVideoPlayer');

/**
 * @fileoverview This is for testing components that interact with 
 *     the video player.
 *
 */

mirosubs.testing.StubVideoPlayer = function() {
    mirosubs.video.AbstractVideoPlayer.call(this);
    /**
     * Can be set to artificial values for the purpose of unit 
     * testing components.
     */
    this.playheadTime = 0;
    this.playing = false;
};
goog.inherits(mirosubs.testing.StubVideoPlayer, 
              mirosubs.video.AbstractVideoPlayer);

mirosubs.testing.StubVideoPlayer.prototype.getPlayheadTime = function() {
    return this.playheadTime;
};
mirosubs.testing.StubVideoPlayer.prototype.play = function() {
    this.playing = true;
};
mirosubs.testing.StubVideoPlayer.prototype.pause = function() {
    this.playing = false;
};
mirosubs.testing.StubVideoPlayer.prototype.isPlaying = function() {
    return this.playing;
};
mirosubs.testing.StubVideoPlayer.prototype.playWithNoUpdateEvents = 
    function(timeToStart, secondsToPlay) 
{
    // do nothing!
};
mirosubs.testing.StubVideoPlayer.prototype.dispatchTimeUpdate = function() {
    this.dispatchEvent(mirosubs.video.AbstractVideoPlayer.EventType.TIMEUPDATE);
};