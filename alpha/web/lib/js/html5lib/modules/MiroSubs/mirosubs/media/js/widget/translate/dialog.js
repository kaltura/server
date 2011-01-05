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

goog.provide('mirosubs.translate.Dialog');

/**
 * @constructor
 * 
 */
mirosubs.translate.Dialog = function(serverModel,
                                     videoSource, 
                                     subtitleState, 
                                     standardSubState) {
    mirosubs.Dialog.call(this, videoSource);
    this.subtitleState_ = subtitleState;
    this.standardSubState_ = standardSubState;
    this.unitOfWork_ = new mirosubs.UnitOfWork();
    this.serverModel_ = serverModel;
    this.serverModel_.init(this.unitOfWork_);
    this.saved_ = false;
};
goog.inherits(mirosubs.translate.Dialog, mirosubs.Dialog);
mirosubs.translate.Dialog.prototype.createDom = function() {
    mirosubs.translate.Dialog.superClass_.createDom.call(this);
    this.translationPanel_ = new mirosubs.translate.TranslationPanel(
        this.subtitleState_, this.standardSubState_,
        this.unitOfWork_);
    this.getCaptioningAreaInternal().addChild(
        this.translationPanel_, true);
    var rightPanel = this.createRightPanel_();
    this.setRightPanelInternal(rightPanel);
    this.getHandler().listen(
        rightPanel, mirosubs.RightPanel.EventType.DONE,
        this.handleDoneKeyPress_);
    goog.dom.classes.add(this.getContentElement(),
                         'mirosubs-modal-widget-translate');
};
mirosubs.translate.Dialog.prototype.createRightPanel_ = function() {
    var title = this.subtitleState_.VERSION > 0 ? 
        "Editing Translation" : "Adding a New Translation";
    var helpContents = new mirosubs.RightPanel.HelpContents(
        title,
        [["Thanks for volunteering to translate! As soon as you submit ",
          "your translation, it will be available to everyone watching the ",
          "video in our widget."].join(''),
         ["Choose a language from the menu to the left. Then translate each  ", 
          "line, one by one, in the white space below each line."].join(''),
         ["If you need to rearrange the order of words or split a phrase ",
          "differently, that's okay."].join(''),
         ["As you're translating, you can use the \"TAB\" key to advance to ",
          "the next line, and \"Shift-TAB\" to go back."].join('')
        ]);
    var extraHelp = [
        ["Google Translate", "http://translate.google.com/"],
        ["List of dictionaries", "http://yourdictionary.com/languages.html"],
        ["Firefox spellcheck dictionaries", 
         "https://addons.mozilla.org/en-US/firefox/browse/type:3"]
    ];
    return new mirosubs.translate.TranslationRightPanel(
        this.serverModel_, helpContents, extraHelp, [], false, "Done?", 
        "Submit final translation", "Resources for Translators");
};
mirosubs.translate.Dialog.prototype.handleDoneKeyPress_ = function(event) {
    this.saveWork(true);
    event.preventDefault();
};
mirosubs.translate.Dialog.prototype.isWorkSaved = function() {
    return !this.unitOfWork_.everContainedWork() || this.saved_;
};
mirosubs.translate.Dialog.prototype.saveWorkInternal = function(closeAfterSave) {
    var that = this;
    this.serverModel_.finish(
        this.translationPanel_.makeJsonSubs(),
        function(dropDownContents) {
            that.setDropDownContentsInternal(dropDownContents);
            that.saved_ = true;
            that.setVisible(false);
        });
};
mirosubs.translate.Dialog.prototype.disposeInternal = function() {
    mirosubs.translate.Dialog.superClass_.disposeInternal.call(this);
    this.unitOfWork_.dispose();
    this.serverModel_.dispose();
};
