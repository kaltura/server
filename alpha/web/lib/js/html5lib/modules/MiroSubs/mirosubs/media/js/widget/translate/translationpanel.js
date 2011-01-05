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

goog.provide('mirosubs.translate.TranslationPanel');

// FIXME: I think that since the latest translation changes, this class no 
//     longer really does anything. Probably just go straight to TranslationList
//     instead of using this as an intermediary.

/**
 *
 *
 * @constructor
 */
mirosubs.translate.TranslationPanel = function(subtitleState,
                                               standardSubState,
                                               unitOfWork) {
    goog.ui.Component.call(this);
    this.subtitleState_ = subtitleState;
    this.standardSubState_ = standardSubState;
    this.unitOfWork_ = unitOfWork;
    this.contentElem_ = null;
    this.editableTranslations_ = goog.array.map(
        this.subtitleState_.SUBTITLES,
        function(transJson) {
            return new mirosubs.translate.EditableTranslation(
                uw, transJson['subtitle_id'], transJson);
        });
};
goog.inherits(mirosubs.translate.TranslationPanel, goog.ui.Component);

mirosubs.translate.TranslationPanel.prototype.getContentElement = function() {
    return this.contentElem_;
};
mirosubs.translate.TranslationPanel.prototype.createDom = function() {
    mirosubs.translate.TranslationPanel.superClass_.createDom.call(this);
    var el = this.getElement();
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    
    el.appendChild(this.contentElem_ = $d('div'));
    this.translationList_ =
        new mirosubs.translate.TranslationList(
            this.standardSubState_.SUBTITLES, this.unitOfWork_);
    this.addChild(this.translationList_, true);
    this.translationList_.getElement().className =
        "mirosubs-titlesList";
    var uw = this.unitOfWork_;
    this.translationList_.setTranslations(this.editableTranslations_);
};
mirosubs.translate.TranslationPanel.prototype.makeJsonSubs = function() {
    return goog.array.map(
        this.editableTranslations_, function(t) { return t.json; });
};
