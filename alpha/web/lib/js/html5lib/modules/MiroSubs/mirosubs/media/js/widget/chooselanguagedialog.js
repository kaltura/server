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

goog.provide('mirosubs.widget.ChooseLanguageDialog');

/**
 * @constructor
 */
mirosubs.widget.ChooseLanguageDialog = function(
    forAddTranslation, callback) 
{
    goog.ui.Dialog.call(this, 'mirosubs-modal-lang', true);
    this.setButtonSet(null);
    this.setDisposeOnHide(true);
    this.forAddTranslation_ = forAddTranslation;
    this.callback_ = callback;
    this.selectedOriginal_ = null;
    this.selectedLanguage_ = null;
};
goog.inherits(mirosubs.widget.ChooseLanguageDialog, goog.ui.Dialog);

mirosubs.widget.ChooseLanguageDialog.prototype.createDom = function() {
    mirosubs.widget.ChooseLanguageDialog.superClass_.createDom.call(this);
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.getElement().appendChild(
        $d('h3', null, 
           this.forAddTranslation_ ? "Add a new translation" : "Add subtitles"));
    var allLanguages = goog.array.concat(
        mirosubs.languages, mirosubs.metadataLanguages);
    this.subLangDropdown_ = this.makeDropdown_(
        $d, allLanguages);
    if (!this.forAddTranslation_) {
        this.originalLangDropdown_ = this.makeDropdown_(
            $d, mirosubs.languages);
        this.getElement().appendChild(
            $d('div', null,
               $d('p', null,
                  $d('div', null, 'This video is in:'),
                  this.originalLangDropdown_),
               $d('p', null,
                  $d('div', null, 'I am subtitling in:'),
                  this.subLangDropdown_)));
        this.originalLangDropdown_.value = 'en';
        this.subLangDropdown_.value = 'en';
    }
    else {
        this.getElement().appendChild(
            $d('div', null,
               $d('p', null,
                  $d('div', null, 'I am subtitling in:'),
                  this.subLangDropdown_)));
    }
    this.okButton_ = 
        $d('a', 
           {'href':'#', 
            'className': "mirosubs-green-button mirosubs-big"}, 
           'Continue');
    this.getElement().appendChild(this.okButton_);
    var clearDiv = $d('div', {'style': 'clear: both'});
    clearDiv.innerHTML = "&nbsp;";
    this.getElement().appendChild(clearDiv);
};

mirosubs.widget.ChooseLanguageDialog.prototype.enterDocument = function() {
    mirosubs.widget.ChooseLanguageDialog.superClass_.enterDocument.call(this);
    this.getHandler().
        listen(this.okButton_,
               'click',
               this.okClicked_);
    if (this.originalLangDropdown_) {
        this.getHandler().listen(
            this.originalLangDropdown_,
            'change',
            this.originalLanguageChanged_);
    }
};

mirosubs.widget.ChooseLanguageDialog.prototype.originalLanguageChanged_ = function() {
    this.subLangDropdown_.value = this.originalLangDropdown_.value;
};

mirosubs.widget.ChooseLanguageDialog.prototype.okClicked_ = function(e) {
    e.preventDefault();
    this.setVisible(false);
    this.callback_(
        this.subLangDropdown_.value, 
        this.originalLangDropdown_ ? this.originalLangDropdown_.value : null);
};

mirosubs.widget.ChooseLanguageDialog.prototype.makeDropdown_ = 
    function($d, contents) 
{
    var options = []
    for (var i = 0; i < contents.length; i++)
        options.push(
            $d('option', {'value': contents[i][0]}, contents[i][1]));
    return $d('select', null, options);
};

mirosubs.widget.ChooseLanguageDialog.show = 
    function(forAddTranslation, callback)
{
    var dialog = new mirosubs.widget.ChooseLanguageDialog(
        forAddTranslation, callback);
    dialog.setVisible(true);
};
