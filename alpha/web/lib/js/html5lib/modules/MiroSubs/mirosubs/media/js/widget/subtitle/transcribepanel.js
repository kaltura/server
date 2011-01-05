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

goog.provide('mirosubs.subtitle.TranscribePanel');

/**
 * @param {mirosubs.subtitle.EditableCaptionSet} captions
 * @param {mirosubs.VideoPlayer} videoPlayer Used to update subtitle 
 *     preview on top of the video
 * @param {mirosubs.ServerModel} serverModel Used to create RightPanel, which 
 *     needs access to server to login.
 */
mirosubs.subtitle.TranscribePanel = function(captionSet, videoPlayer, serverModel) {
    goog.ui.Component.call(this);

    this.captionSet_ = captionSet;
    this.videoPlayer_ = videoPlayer;
    this.serverModel_ = serverModel;

    /**
     * @type {?goog.events.KeyHandler}
     * @private
     */
    this.keyHandler_ = null;
    this.keyEventsSuspended_ = false;
};
goog.inherits(mirosubs.subtitle.TranscribePanel, goog.ui.Component);

mirosubs.subtitle.TranscribePanel.logger_ =
    goog.debug.Logger.getLogger('mirosubs.subtitle.TranscribePanel');

mirosubs.subtitle.TranscribePanel.PlayMode = {
    PLAY_STOP : 'pl',
    AUTOPAUSE : 'au',
    NO_AUTOPAUSE : 'no'
};

mirosubs.subtitle.TranscribePanel.prototype.getContentElement = function() {
    return this.contentElem_;
};

mirosubs.subtitle.TranscribePanel.prototype.createDom = function() {
    mirosubs.subtitle.TranscribePanel.superClass_.createDom.call(this);
    this.addElems_(this.getElement());
};
mirosubs.subtitle.TranscribePanel.prototype.decorateInternal = function(el) {
    mirosubs.subtitle.TranscribePanel.superClass_.decorateInternal.call(this, el);
    this.addElems_(el);
};
mirosubs.subtitle.TranscribePanel.prototype.addElems_ = function(el) {
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.getElement().appendChild(this.contentElem_ = $d('div'));
    this.addChild(this.lineEntry_ = new mirosubs.subtitle.TranscribeEntry(
        this.videoPlayer_), true);
    this.addChild(this.subtitleList_ = new mirosubs.subtitle.SubtitleList(
        this.videoPlayer_, this.captionSet_, false, true), true);
    mirosubs.subtitle.TranscribePanel.logger_.info('setting play mode');
    this.setPlayMode(mirosubs.UserSettings.getStringValue(
        mirosubs.UserSettings.Settings.VIDEO_SPEED_MODE) ||
                     mirosubs.subtitle.TranscribePanel.PlayMode.PLAY_STOP);
};
mirosubs.subtitle.TranscribePanel.prototype.suspendKeyEvents = function(suspended) {
    this.keyEventsSuspended_ = suspended;
};
mirosubs.subtitle.TranscribePanel.prototype.getRightPanel = 
    function(serverModel) 
{
    if (!this.rightPanel_) {
        this.rightPanel_ = this.createRightPanel_();
        this.listenToRightPanel_();
    }
    return this.rightPanel_;
};
mirosubs.subtitle.TranscribePanel.prototype.listenToRightPanel_ = function() {
    if (this.rightPanel_ && this.isInDocument()) {
        this.getHandler().listen(this.rightPanel_,
                                 mirosubs.RightPanel.EventType.RESTART,
                                 this.startOverClicked);
        var that = this;
        this.getHandler().listen(
            this.rightPanel_,
            mirosubs.subtitle.TranscribeRightPanel.PLAYMODE_CHANGED,
            function(event) {
                that.setPlayMode(event.mode);
            });        
    }
};
mirosubs.subtitle.TranscribePanel.prototype.createRightPanel_ = function() {
    var helpContents = new mirosubs.RightPanel.HelpContents(
        "Typing",
        [["Thanks for making subtitles!! It's easy to learn ", 
          "and actually fun to do."].join(''),
         ["While you watch the video, type everything people ",
          "say and all important text that appears ",
          "on-screen."].join(''),
         ["Use the key controls below to pause and jump back, ", 
          "which will help you keep up."].join('')],
         3, 0);
    var extraHelp = [
        "Press play, then type everything people say in the text " +
            "entry below the video.",
        "Don't let subtitles get too long. Hit Enter for a new line."
    ];
    var KC = goog.events.KeyCodes;
    var keySpecs = [
        new mirosubs.RightPanel.KeySpec(
            'mirosubs-play', 'mirosubs-tab', 'tab', 'Play/Pause', KC.TAB, 0),
        new mirosubs.RightPanel.KeySpec(
            'mirosubs-skip', 'mirosubs-control', 'shift\n+\ntab', 
            'Skip Back 8 Seconds', KC.TAB,
            mirosubs.RightPanel.KeySpec.Modifier.SHIFT)
    ];
    return new mirosubs.subtitle.TranscribeRightPanel(
        this.serverModel_, helpContents, extraHelp, keySpecs, 
        true, "Done?", "Next Step: Syncing");
};
mirosubs.subtitle.TranscribePanel.prototype.enterDocument = function() {
    mirosubs.subtitle.TranscribePanel.superClass_.enterDocument.call(this);
    this.getHandler().listen(this.lineEntry_,
                             mirosubs.subtitle.TranscribeEntry.NEWTITLE,
                             this.newTitle_);
    this.getHandler().listen(this.videoPlayer_,
                             mirosubs.video.AbstractVideoPlayer.EventType.PLAY,
                             this.videoPlaying_);
    this.listenToRightPanel_();
};
mirosubs.subtitle.TranscribePanel.prototype.videoPlaying_ = function(event) {
    this.lineEntry_.focus();
};
mirosubs.subtitle.TranscribePanel.prototype.newTitle_ = function(event) {
    var newEditableCaption = this.captionSet_.addNewCaption();
    this.subtitleList_.addSubtitle(newEditableCaption, true);
    newEditableCaption.setText(event.title);
};
/**
 *
 * @param {boolean} mode True to turn repeat on, false to turn it off.
 */
mirosubs.subtitle.TranscribePanel.prototype.setPlayMode = function(mode) {
    this.lineEntry_.setPlayMode(mode);
};

mirosubs.subtitle.TranscribePanel.prototype.startOverClicked = function() {
    var answer = confirm(
        "Are you sure you want to start over? All subtitles will be deleted.");
    if (answer) {
        this.captionSet_.clear();
        this.videoPlayer_.setPlayheadTime(0);
    }
};

mirosubs.subtitle.TranscribePanel.prototype.disposeInternal = function() {
    mirosubs.subtitle.TranscribePanel.superClass_.disposeInternal.call(this);
    if (this.rightPanel_)
        this.rightPanel_.dispose();
};