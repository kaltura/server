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

goog.provide('mirosubs.widget.Controller');

/**
 * @constructor
 *
 */
mirosubs.widget.Controller = function(
    videoID, videoSource, videoPlayer, videoTab, dropDown) 
{
    this.videoID_ = videoID;
    this.videoSource_ = videoSource;
    this.videoPlayer_ = videoPlayer;
    this.videoTab_ = videoTab;
    this.mainMenu_ = dropDown;
    /**
     * Set to a non-null value if and only if subs 
     * are loaded for the video in the widget.
     */
    this.playManager_ = null;
    this.initialized_ = false;
};

mirosubs.widget.Controller.prototype.initializeWithBaseState = function(
    baseState, subtitles) 
{
    goog.asserts.assert(!this.initialized_);
    this.initialized_ = true;
    this.playSubs_(baseState, subtitles);
};

mirosubs.widget.Controller.prototype.initializeNoBaseState = 
    function(subtitleCount) 
{
    goog.asserts.assert(!this.initialized_);
    this.initialized_ = true;
    this.baseState_ new 
    var m = mirosubs.widget.VideoTab.Messages;
    this.videoTab_.setText(
        subtitleCount > 0 ? m.CHOOSE_LANGUAGE : m.SUBTITLE_ME);
};

mirosubs.widget.Controller.playSubs_ = function(baseState, subtitles) {
    this.disposePlayManager_();
    this.playManager_ = new mirosubs.play.Manager(
        this.videoPlayer_, baseState, subtitles);
    // TODO: set language in tab.
    
};

mirosubs.widget.Controller.prototype.openSubtitleDialog = function() {
    
};

mirosubs.widget.Controller.prototype.openNewTranslationDialog = function() {
    
};