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

goog.provide('mirosubs.subtitle.Dialog');

/**
 * @constructor
 * @param {mirosubs.subtitle.ServerModel} serverModel
 * @param {Array.<Object.<string, *>>} existingCaptions existing captions in
 *     json object format.
 */
mirosubs.subtitle.Dialog = function(videoSource, serverModel,
                                    existingCaptions, opt_skipFinished) {
    mirosubs.Dialog.call(this, videoSource);
    this.serverModel_ = serverModel;
    this.skipFinished_ = !!opt_skipFinished;
    var uw = this.unitOfWork_ = new mirosubs.UnitOfWork();
    this.captionSet_ =
        new mirosubs.subtitle.EditableCaptionSet(existingCaptions, uw);
    this.captionManager_ =
        new mirosubs.CaptionManager(
            this.getVideoPlayerInternal(), this.captionSet_);
    this.serverModel_ = serverModel;
    this.serverModel_.init(uw, goog.bind(this.showLoginNag_, this));
    /**
     * @type {?boolean} True iff we pass into FINISHED state.
     */
    this.saved_ = false;
    /**
     *
     * @type {?mirosubs.subtitle.Dialog.State_}
     */
    this.state_ = null;
    this.currentSubtitlePanel_ = null;
    this.rightPanelListener_ = new goog.events.EventHandler(this);
    this.doneButtonEnabled_ = true;
    this.addingTranslations_ = false;

    this.keyEventsSuspended_ = false;
};
goog.inherits(mirosubs.subtitle.Dialog, mirosubs.Dialog);

/**
 *
 * @enum
 */
mirosubs.subtitle.Dialog.State_ = {
    TRANSCRIBE: 0,
    SYNC: 1,
    REVIEW: 2,
    FINISHED: 3
};
mirosubs.subtitle.Dialog.prototype.captionReached_ = function(event) {
    var c = event.caption;
    this.getVideoPlayerInternal().showCaptionText(c ? c.getText() : '');
};
mirosubs.subtitle.Dialog.prototype.createDom = function() {
    mirosubs.subtitle.Dialog.superClass_.createDom.call(this);
    this.enterState_(mirosubs.subtitle.Dialog.State_.TRANSCRIBE);
};
mirosubs.subtitle.Dialog.prototype.enterDocument = function() {
    mirosubs.subtitle.Dialog.superClass_.enterDocument.call(this);
    var doc = this.getDomHelper().getDocument();
    this.getHandler().
        listen(
            doc,
            goog.events.EventType.KEYDOWN,
            this.handleKeyDown_, true).
        listen(
            doc,
            goog.events.EventType.KEYUP,
            this.handleKeyUp_).
        listen(
            this.captionManager_,
            mirosubs.CaptionManager.CAPTION,
            this.captionReached_);
};
mirosubs.subtitle.Dialog.prototype.setExtraClass_ = function() {
    var extraClasses = goog.array.map(
        ['transcribe', 'sync', 'review', 'finished'],
        function(suffix) { return 'mirosubs-modal-widget-' + suffix; });
    var currentClass = "";
    var s = mirosubs.subtitle.Dialog.State_;
    if (this.state_ == s.TRANSCRIBE)
        currentClass = extraClasses[0];
    else if (this.state_ == s.SYNC)
        currentClass = extraClasses[1];
    else if (this.state_ == s.REVIEW)
        currentClass = extraClasses[2];
    else if (this.state_ == s.FINISHED)
        currentClass = extraClasses[3];
    goog.array.remove(extraClasses, currentClass);
    goog.dom.classes.addRemove(this.getContentElement(), extraClasses, currentClass);
};
mirosubs.subtitle.Dialog.prototype.setState_ = function(state) {
    this.state_ = state;

    this.suspendKeyEvents_(false);

    var s = mirosubs.subtitle.Dialog.State_;

    this.setExtraClass_();

    var nextSubPanel = this.makeCurrentStateSubtitlePanel_();
    var captionPanel = this.getCaptioningAreaInternal();
    captionPanel.removeChildren(true);
    captionPanel.addChild(nextSubPanel, true);

    var rightPanel = nextSubPanel.getRightPanel();
    this.setRightPanelInternal(rightPanel);

    this.getTimelinePanelInternal().removeChildren(true);

    this.disposeCurrentPanels_();
    this.currentSubtitlePanel_ = nextSubPanel;

    var et = mirosubs.RightPanel.EventType;
    this.rightPanelListener_.listen(
        rightPanel, et.LEGENDKEY, this.handleLegendKeyPress_);
    this.rightPanelListener_.listen(
        rightPanel, et.DONE, this.handleDoneKeyPress_);
    this.rightPanelListener_.listen(
        rightPanel, et.GOTOSTEP, this.handleGoToStep_);
    if (state == s.SYNC || state == s.REVIEW) {
        rightPanel.showBackLink(
            state == s.SYNC ? "Back to Typing" : "Back to Sync");
        this.rightPanelListener_.listen(
            rightPanel, et.BACK, this.handleBackKeyPress_);
        this.timelineSubtitleSet_ =
            new mirosubs.timeline.SubtitleSet(
                this.captionSet_, this.getVideoPlayerInternal());
        this.getTimelinePanelInternal().addChild(
            new mirosubs.timeline.Timeline(
                1, this.timelineSubtitleSet_,
                this.getVideoPlayerInternal()), true);
    }

    var videoPlayer = this.getVideoPlayerInternal();
    if (this.isInDocument()) {
        videoPlayer.setPlayheadTime(0);
        videoPlayer.pause();
    }
};
mirosubs.subtitle.Dialog.prototype.suspendKeyEvents_ = function(suspended) {
    this.keyEventsSuspended_ = suspended;
    if (this.currentSubtitlePanel_)
        this.currentSubtitlePanel_.suspendKeyEvents(suspended);
};
mirosubs.subtitle.Dialog.prototype.setFinishedState_ = function() {
    if (this.skipFinished_)
        this.setVisible(false);
    if (!mirosubs.isFromDifferentDomain()) {
        window.location.assign(this.serverModel_.getPermalink() + '?saved=true');
        return;
    }
    this.state_ = mirosubs.subtitle.Dialog.State_.FINISHED;
    this.setExtraClass_();
    var sharePanel = new mirosubs.subtitle.SharePanel(
        this.serverModel_);
    this.setRightPanelInternal(sharePanel);
    this.getTimelinePanelInternal().removeChildren(true);
    this.getCaptioningAreaInternal().removeChildren(true);
    var bottomContainer = this.getBottomPanelContainerInternal();
    var bottomFinishedPanel = new mirosubs.subtitle.BottomFinishedPanel(
        this, this.serverModel_.getPermalink());
    bottomContainer.addChild(bottomFinishedPanel, true);

    var videoPlayer = this.getVideoPlayerInternal();
    if (this.isInDocument()) {
        // TODO: make video player stop loading here?
        videoPlayer.setPlayheadTime(0);
        videoPlayer.pause();
    }
};
mirosubs.subtitle.Dialog.prototype.handleGoToStep_ = function(event) {
    this.setState_(event.stepNo);
};
mirosubs.subtitle.Dialog.prototype.handleKeyDown_ = function(event) {
    if (this.keyEventsSuspended_)
        return;
    var s = mirosubs.subtitle.Dialog.State_;
    if (event.keyCode == goog.events.KeyCodes.TAB) {
        if (event.shiftKey) {
            this.skipBack_();
            this.getRightPanelInternal().setKeyDown(event.keyCode,
                mirosubs.RightPanel.KeySpec.Modifier.SHIFT, true);
        }
        else {
            this.togglePause_();
            this.getRightPanelInternal().setKeyDown(event.keyCode, 0, true);
        }
        event.preventDefault();
    }
};
mirosubs.subtitle.Dialog.prototype.handleKeyUp_ = function(event) {
    if (event.keyCode == goog.events.KeyCodes.TAB) {
        var modifier = 0;
        if (event.shiftKey)
            modifier = mirosubs.RightPanel.KeySpec.Modifier.SHIFT;
        this.getRightPanelInternal().setKeyDown(event.keyCode, modifier, false);
    }
    else if (event.keyCode == goog.events.KeyCodes.SHIFT) {
        // if shift is released before tab, we still need to untoggle the legend
        this.getRightPanelInternal().setKeyDown(goog.events.KeyCodes.TAB,
            mirosubs.RightPanel.KeySpec.Modifier.SHIFT, false);
    }
};
mirosubs.subtitle.Dialog.prototype.handleBackKeyPress_ = function(event) {
    var s = mirosubs.subtitle.Dialog.State_;
    if (this.state_ == s.SYNC)
        this.setState_(s.TRANSCRIBE);
    else if (this.state_ == s.REVIEW)
        this.setState_(s.SYNC);
};
mirosubs.subtitle.Dialog.prototype.handleLegendKeyPress_ = function(event) {
    if (event.keyCode == goog.events.KeyCodes.TAB &&
        event.keyEventType == goog.events.EventType.CLICK) {
        if (event.modifiers == mirosubs.RightPanel.KeySpec.Modifier.SHIFT)
            this.skipBack_();            
        else
            this.togglePause_();
    }
};
mirosubs.subtitle.Dialog.prototype.handleDoneKeyPress_ = function(event) {
    if (!this.doneButtonEnabled_)
        return;
    if (this.state_ == mirosubs.subtitle.Dialog.State_.REVIEW)
        this.saveWork(false);
    else
        this.enterState_(this.nextState_());
};

mirosubs.subtitle.Dialog.prototype.isWorkSaved = function() {
    return !this.unitOfWork_.everContainedWork() || this.saved_;
};

mirosubs.subtitle.Dialog.prototype.saveWorkInternal = function(closeAfterSave) {
    this.doneButtonEnabled_ = false;
    this.getRightPanelInternal().showLoading(true);
    var that = this;
    this.serverModel_.finish(
        this.captionSet_.makeJsonSubs(),
        function(dropDownContents) {
            that.saved_ = true;
            that.setDropDownContentsInternal(dropDownContents);
            if (closeAfterSave)
                that.setVisible(false);
            else {
                that.doneButtonEnabled_ = true;
                that.getRightPanelInternal().showLoading(false);
                that.setFinishedState_();
            }
        },
        function() {
            that.doneButtonEnabled_ = true;
            that.getRightPanelInternal().showLoading(false);
        });
};

mirosubs.subtitle.Dialog.prototype.enterState_ = function(state) {
    if (mirosubs.UserSettings.getBooleanValue(
        mirosubs.UserSettings.Settings.SKIP_HOWTO_VIDEO))
        this.setState_(state);
    else
        this.showHowToForState_(state);
};

mirosubs.subtitle.Dialog.prototype.showHowToForState_ = function(state) {
    this.suspendKeyEvents_(true);
    this.getVideoPlayerInternal().pause();
    var s = mirosubs.subtitle.Dialog.State_;
    var vc = mirosubs.HowToVideoPanel.VideoChoice;
    var videoChoice;
    if (state == s.TRANSCRIBE)
        videoChoice = vc.TRANSCRIBE;
    else if (state == s.SYNC)
        videoChoice = vc.SYNC;
    else if (state == s.REVIEW)
        videoChoice = vc.REVIEW;
    var howToPanel = new mirosubs.HowToVideoPanel(videoChoice);
    this.showTemporaryPanel(howToPanel);
    this.displayingHowTo_ = true;
    var that = this;
    this.getHandler().listenOnce(
        howToPanel, mirosubs.HowToVideoPanel.CONTINUE,
        function(e) {
            goog.Timer.callOnce(function() {
                that.displayingHowTo_ = false;
                that.hideTemporaryPanel();
                that.setState_(state);
            });
        });
};
mirosubs.subtitle.Dialog.prototype.skipBack_ = function() {
    var videoPlayer = this.getVideoPlayerInternal();
    var now = videoPlayer.getPlayheadTime();
    videoPlayer.setPlayheadTime(Math.max(now - 8, 0));
    videoPlayer.play();
};
mirosubs.subtitle.Dialog.prototype.togglePause_ = function() {
    this.getVideoPlayerInternal().togglePause();
};
mirosubs.subtitle.Dialog.prototype.makeCurrentStateSubtitlePanel_ = function() {
    var s = mirosubs.subtitle.Dialog.State_;
    if (this.state_ == s.TRANSCRIBE)
        return new mirosubs.subtitle.TranscribePanel(
            this.captionSet_,
            this.getVideoPlayerInternal(),
            this.serverModel_);
    else if (this.state_ == s.SYNC)
        return new mirosubs.subtitle.SyncPanel(
            this.captionSet_,
            this.getVideoPlayerInternal(),
            this.serverModel_,
            this.captionManager_);
    else if (this.state_ == s.REVIEW)
        return new mirosubs.subtitle.ReviewPanel(
            this.captionSet_,
            this.getVideoPlayerInternal(),
            this.serverModel_,
            this.captionManager_);
};
mirosubs.subtitle.Dialog.prototype.nextState_ = function() {
    var s = mirosubs.subtitle.Dialog.State_;
    if (this.state_ == s.TRANSCRIBE)
        return s.SYNC;
    else if (this.state_ == s.SYNC)
        return s.REVIEW;
    else if (this.state_ == s.REVIEW)
        return s.FINISHED;
};
mirosubs.subtitle.Dialog.prototype.showLoginNag_ = function() {
    // not doing anything here right now.
};
/**
 * Did we ever pass into finished state?
 */
mirosubs.subtitle.Dialog.prototype.isSaved = function() {
    return this.saved_;
};
mirosubs.subtitle.Dialog.prototype.disposeCurrentPanels_ = function() {
    if (this.currentSubtitlePanel_) {
        this.currentSubtitlePanel_.dispose();
        this.currentSubtitlePanel_ = null;
    }
    this.rightPanelListener_.removeAll();
    if (this.timelineSubtitleSet_ != null) {
        this.timelineSubtitleSet_.dispose();
        this.timelineSubtitleSet_ = null;
    }
};
mirosubs.subtitle.Dialog.prototype.disposeInternal = function() {
    mirosubs.subtitle.Dialog.superClass_.disposeInternal.call(this);
    this.disposeCurrentPanels_();
    this.captionManager_.dispose();
    this.serverModel_.dispose();
    this.rightPanelListener_.dispose();
    this.captionSet_.dispose();
};
mirosubs.subtitle.Dialog.prototype.setVisible = function(visible) {
    if (this.addingTranslations_) {
        goog.Timer.callOnce(function() {
            window.location = window.location.href.replace("subtitle_immediately", "translate_immediately");
            mirosubs.returnURL = null;
            mirosubs.subtitle.Dialog.superClass_.setVisible.call(this, visible);
        });
    }
    else {
        mirosubs.subtitle.Dialog.superClass_.setVisible.call(this, visible);
    }
};
mirosubs.subtitle.Dialog.prototype.addTranslationsAndClose = function() {
    this.addingTranslations_ = true;
    this.setVisible(false);
};
mirosubs.subtitle.Dialog.prototype.isAddingTranslations = function() {
    return this.addingTranslations_;
};