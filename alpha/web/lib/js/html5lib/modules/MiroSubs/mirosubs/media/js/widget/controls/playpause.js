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

goog.provide('mirosubs.controls.PlayPause');

mirosubs.controls.PlayPause = function(videoPlayer, opt_domHelper) {
    goog.ui.Component.call(this, opt_domHelper);
    this.videoPlayer_ = videoPlayer;
    this.state_ = null;
};
goog.inherits(mirosubs.controls.PlayPause, goog.ui.Component);
mirosubs.controls.PlayPause.State_ = {
    PLAYING : 'playing',
    PAUSED : 'paused'
};
mirosubs.controls.PlayPause.prototype.createDom = function() {
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.setElementInternal($d('span', 'mirosubs-playPause', $d('span')));
    goog.dom.a11y.setRole(this.getElement(), goog.dom.a11y.Role.BUTTON);
    this.setPaused_();;
};
mirosubs.controls.PlayPause.prototype.enterDocument = function() {
    mirosubs.controls.PlayPause.superClass_.enterDocument.call(this);
    var et = mirosubs.video.AbstractVideoPlayer.EventType;
    this.getHandler().
        listen(this.videoPlayer_, et.PLAY_CALLED, this.setPlaying_).
        listen(this.videoPlayer_, et.PAUSE_CALLED, this.setPaused_).
        listen(this.getElement(), 'click', this.clicked_);
};
mirosubs.controls.PlayPause.prototype.setPlaying_ = function() {
    goog.dom.classes.addRemove(this.getElement(), 'play', 'pause');
    this.state_ = mirosubs.controls.PlayPause.State_.PLAYING;
};
mirosubs.controls.PlayPause.prototype.setPaused_ = function() {
    goog.dom.classes.addRemove(this.getElement(), 'pause', 'play');
    this.state_ = mirosubs.controls.PlayPause.State_.PAUSED;
};
mirosubs.controls.PlayPause.prototype.clicked_ = function() {
    if (this.state_ == null)
        return;
    else if (this.state_ == mirosubs.controls.PlayPause.State_.PLAYING)
        this.videoPlayer_.pause();
    else
        this.videoPlayer_.play();
};