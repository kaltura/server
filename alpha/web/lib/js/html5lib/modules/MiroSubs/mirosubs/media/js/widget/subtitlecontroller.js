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

goog.provide('mirosubs.widget.SubtitleController');

/**
 * @constructor
 */
mirosubs.widget.SubtitleController = function(
    videoID, videoURL, playController, videoTab, dropDown) 
{
    this.videoID_ = videoID;
    this.videoURL_ = videoURL;
    this.videoTab_ = videoTab;
    this.dropDown_ = dropDown;
    this.playController_ = playController;
    this.playController_.setSubtitleController(this);
    this.handler_ = new goog.events.EventHandler(this);
    var s = mirosubs.widget.DropDown.Selection;
    this.handler_.
        listen(
            dropDown,
            s.ADD_LANGUAGE,
            this.openNewLanguageDialog).
        listen(
            dropDown,
            s.IMPROVE_SUBTITLES,
            this.openSubtitleDialog).
        listen(
            videoTab.getAnchorElem(), 'click',
            this.videoAnchorClicked_
        );
};

mirosubs.widget.SubtitleController.prototype.videoAnchorClicked_ = 
    function(e) 
{
    if (!this.dropDown_.hasSubtitles())
        this.openSubtitleDialog();
    else
        this.dropDown_.toggleShow();
    e.preventDefault();
};

/**
 * Corresponds to "Add new subs" or "Improve these subs" in menu.
 */
mirosubs.widget.SubtitleController.prototype.openSubtitleDialog = 
    function() 
{
    var forNewSubs = !this.dropDown_.hasSubtitles();
    var subtitleState = this.playController_.getSubtitleState();
    if (subtitleState != null && 
        !subtitleState.IS_LATEST && 
        !mirosubs.returnURL) {
        var msg =
            ["You're about to edit revision ", 
             subtitleState.REVISION, ", an old revision. ",
             "Changes may have been made since this revision, and your edits ",
             "will override those changes. Are you sure you want to do this?"].
            join('');
        if (confirm(msg))
            this.possiblyRedirect_(false, goog.bind(this.subtitle_, this, forNewSubs));
    }
    else
        this.possiblyRedirect_(false, goog.bind(this.subtitle_, this, forNewSubs));
};

/**
 * Correponds to "Add new subtitles" in menu. Don't call this if there 
 * are no subtitles yet.
 */
mirosubs.widget.SubtitleController.prototype.openNewLanguageDialog = 
    function() 
{
    if (!this.dropDown_.hasSubtitles())
        throw new Error();
    if (this.dropDown_.getSubtitleCount() > 0)
        this.possiblyRedirect_(true, goog.bind(this.openNewTranslationDialog_, this));
    else
        this.possiblyRedirect_(false, goog.bind(this.subtitle_, this, true));
};

mirosubs.widget.SubtitleController.prototype.openNewTranslationDialog_ =
    function()
{
    var that = this;
    mirosubs.widget.ChooseLanguageDialog.show(
        true, function(subLanguage, originalLanguage, forked) {
            that.startEditing_(null, subLanguage, null, 
                               mirosubs.isForkedLanguage(subLanguage));
        });
};

mirosubs.widget.SubtitleController.prototype.possiblyRedirect_ = 
    function(addNewTranslation, callback)
{
    if (mirosubs.DEBUG || !goog.userAgent.GECKO || mirosubs.returnURL)
        callback();
    else {
        var uri = new goog.Uri(mirosubs.siteURL() + '/onsite_widget/');
        uri.setParameterValue('video_url', this.videoURL_);
        if (mirosubs.IS_NULL)
            uri.setParameterValue('null_widget', 'true');
        if (addNewTranslation)
            uri.setParameterValue('translate_immediately', 'true');
        else
            uri.setParameterValue('subtitle_immediately', 'true');
        var subState = this.playController_.getSubtitleState();
        if (subState)
            uri.setParameterValue(
                'base_state', 
                goog.json.serialize(subState.baseParams()));
        uri.setParameterValue('return_url', window.location.href);
        window.location.assign(uri.toString());
    }
};

/**
 *
 * @param {boolean} newLanguage
 */
mirosubs.widget.SubtitleController.prototype.subtitle_ = function(newLanguage) {
    var that = this;
    if (newLanguage)
        mirosubs.widget.ChooseLanguageDialog.show(
            false,
            function(subLanguage, originalLanguage, forked) {
                that.startEditing_(null, subLanguage, originalLanguage, true);
            });
    else {
        var subState = this.playController_.getSubtitleState();
        if (!subState || !subState.LANGUAGE)
            this.startEditing_(null, null, null, false);
        else if (subState && subState.FORKED)
            this.startEditing_(
                subState.VERSION, subState.LANGUAGE, null, true);
        else
            this.startEditing_(
                subState.VERSION, subState.LANGUAGE, null, false);
    }
};

mirosubs.widget.SubtitleController.prototype.startEditing_ = 
    function(baseVersionNo, subLanguageCode, originalLanguageCode, fork) 
{
    this.videoTab_.showLoading();
    mirosubs.Rpc.call(
        'start_editing', 
        {'video_id': this.videoID_,
         'language_code': subLanguageCode,
         'original_language_code': originalLanguageCode,
         'base_version_no': baseVersionNo,
         'fork': fork},
        goog.bind(this.startEditingResponseHandler_, this));
};

mirosubs.widget.SubtitleController.prototype.startEditingResponseHandler_ =
    function(result)
{
    this.videoTab_.stopLoading();
    if (result['can_edit']) {
        subtitles = mirosubs.widget.SubtitleState.fromJSON(
            result['subtitles']);
        originalSubtitles = mirosubs.widget.SubtitleState.fromJSON(
            result['original_subtitles']);
        if (!subtitles.LANGUAGE || subtitles.FORKED)
            this.openSubtitlingDialog_(subtitles);
        else
            this.openDependentTranslationDialog_(subtitles, originalSubtitles);
    }
    else {
        var username = 
            (result['locked_by'] == 
             'anonymous' ? 'Someone else' : ('The user ' + result['locked_by']));
        alert(username + ' is currently editing these subtitles. Please wait and try again later.');
    }
};

mirosubs.widget.SubtitleController.prototype.openSubtitlingDialog_ = 
    function(subtitleState) 
{
    this.playController_.stopForDialog();
    var subDialog = new mirosubs.subtitle.Dialog(
        this.playController_.getVideoSource(),
        new mirosubs.subtitle.MSServerModel(
            this.videoID_, this.videoURL_, 
            subtitleState.LANGUAGE),
        subtitleState.SUBTITLES);
    subDialog.setVisible(true);
    this.handler_.listenOnce(
        subDialog, goog.ui.Dialog.EventType.AFTER_HIDE,
        this.subtitleDialogClosed_);
};

mirosubs.widget.SubtitleController.prototype.openDependentTranslationDialog_ = 
    function(subtitleState, originalSubtitleState)
{
    this.playController_.stopForDialog();
    var transDialog = new mirosubs.translate.Dialog(
        new mirosubs.subtitle.MSServerModel(
            this.videoID_, this.videoURL_, subtitleState.LANGUAGE),
        this.playController_.getVideoSource(),
        subtitleState, originalSubtitleState);
    transDialog.setVisible(true);
    this.handler_.listenOnce(
        transDialog,
        goog.ui.Dialog.EventType.AFTER_HIDE,
        this.subtitleDialogClosed_);
};

mirosubs.widget.SubtitleController.prototype.subtitleDialogClosed_ = function(e) {
    var dropDownContents = e.target.getDropDownContents();
    this.playController_.dialogClosed();
    this.videoTab_.showContent(
        this.dropDown_.hasSubtitles(),
        this.playController_.getSubtitleState());
    this.dropDown_.setCurrentSubtitleState(
        this.playController_.getSubtitleState());
    if (dropDownContents != null) {
        this.dropDown_.updateContents(dropDownContents);
    }

};