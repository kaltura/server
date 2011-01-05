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

goog.provide('mirosubs.subtitle.BottomFinishedPanel');

/**
 * @constructor
 */
mirosubs.subtitle.BottomFinishedPanel = function(dialog, permalink) {
    goog.ui.Component.call(this);
    this.dialog_ = dialog;
    this.permalink_ = permalink;
};
goog.inherits(mirosubs.subtitle.BottomFinishedPanel, goog.ui.Component);

mirosubs.subtitle.BottomFinishedPanel.prototype.createDom = function() {
    mirosubs.subtitle.BottomFinishedPanel.superClass_.createDom.call(this);
    var elem = this.getElement();
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());

    var transBox = $d('div', 'mirosubs-translating');

    if (mirosubs.isFromDifferentDomain()) {
        
        this.closeLink_ = 
            $d('a', 
               {'className': 'mirosubs-otherClose', 'href':'#'}, 
               $d('span'),
               "Close and return to site");
        var otherDiv = 
            $d('div', 'mirosubs-otherButtons',
               this.closeLink_,
               this.getDomHelper().createTextNode("|"),
               $d('a',
                  {'className': 'mirosubs-goBack',
                   'href': this.permalink_},
                  "Go to Universal Subtitles video homepage"));
        elem.appendChild(otherDiv);
    }
    elem.appendChild(transBox);
    this.addTranslationLink_ = 
        $d('a', 
           {'className':'mirosubs-done', 'href':'#'}, 
           'Add a Translation Now');
    this.askAFriendLink_ = 
        $d('a', 
           {'className':'mirosubs-done', 
            'href':'#'},
           'Ask a Friend to Translate');
    transBox.appendChild(
        $d('div', 'mirosubs-buttons',
           this.addTranslationLink_,
           this.askAFriendLink_));
    transBox.appendChild(
        $d('p', null,
           ['Your subtitles are now ready to be translated -- by you ',
            'and by others.  The best way to get translation help is ',
            'to reach out to your friends or people in your community ',
            'or orgnization.'].join('')));
    transBox.appendChild(
        $d('p', null,
           ["Do you know someone who speaks a language that you’d like ",
            "to translate into?"].join('')));
};
mirosubs.subtitle.BottomFinishedPanel.prototype.enterDocument = function() {
    mirosubs.subtitle.BottomFinishedPanel.superClass_.enterDocument.call(this);
    this.getHandler().
        listen(this.addTranslationLink_, 'click',
               this.addTranslationClicked_).
        listen(this.askAFriendLink_, 'click',
               this.askAFriendClicked_);
    if (this.closeLink_)
        this.getHandler().listen(
            this.closeLink_, 'click',
            this.closeClicked_);
};
mirosubs.subtitle.BottomFinishedPanel.prototype.closeClicked_ = 
    function(event) 
{
    event.preventDefault();
    this.dialog_.setVisible(false);
};
mirosubs.subtitle.BottomFinishedPanel.prototype.addTranslationClicked_ =
    function(event)
{
    event.preventDefault();
    this.dialog_.addTranslationsAndClose();
};

mirosubs.subtitle.BottomFinishedPanel.prototype.askAFriendClicked_ = function(e) {
    e.preventDefault();
    window.open(
        this.makeAskFriendURL_(),
        mirosubs.randomString(),
        'scrollbars=yes,width=900,height=600');
};

mirosubs.subtitle.BottomFinishedPanel.ASK_A_FRIEND_TEXT_ = 
    "Hey-- I just created subtitles using universalsubtitles.org, and I was hoping " +
    "you'd be able to use your awesome language skills to translate them.  It's " +
    "easy, and it would be a huge help to me. ";

mirosubs.subtitle.BottomFinishedPanel.prototype.makeAskFriendURL_ =
    function()
{
    var queryData = new goog.Uri.QueryData();
    var message = mirosubs.subtitle.BottomFinishedPanel.ASK_A_FRIEND_TEXT_ +
        this.permalink_ + "?translate_immediately=true";
    queryData.set('text', message);
    return mirosubs.siteURL() + '/videos/email_friend/?' + queryData.toString();
};