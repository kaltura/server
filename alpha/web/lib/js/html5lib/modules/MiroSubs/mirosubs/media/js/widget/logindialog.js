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

goog.provide('mirosubs.LoginDialog');

/**
 * @constructor 
 * @param {function(boolean)=} Called when login process completes.
 *     Passed true if logged in successfully, false otherwise.
 * @param {String} Optional message to show at the top of the login dialog.
 */
mirosubs.LoginDialog = function(opt_finishFn, opt_message) {
    goog.ui.Dialog.call(this, 'mirosubs-modal-login', true);
    this.setBackgroundElementOpacity(0);
    this.finishFn_ = opt_finishFn;
    this.message_ = opt_message;
    this.loggedIn_ = mirosubs.currentUsername != null;
    this.setButtonSet(null);
    this.setDisposeOnHide(true);
    this.imageLoader_ = new goog.net.ImageLoader();
    this.bigSpinnerGifURL_ = mirosubs.imageAssetURL('big_spinner.gif');
    this.imageLoader_.addImage('bigSpinner', this.bigSpinnerGifURL_);
    this.imageLoader_.start();
};
goog.inherits(mirosubs.LoginDialog, goog.ui.Dialog);
/**
 * The currently-opened login dialog.
 */
mirosubs.LoginDialog.currentDialog_ = null;

mirosubs.LoginDialog.prototype.createDom = function() {
    mirosubs.LoginDialog.superClass_.createDom.call(this);
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.loginLink_ = 
        $d('a', {'className': 'mirosubs-log', 'href': '#'},
           $d('span', null, 'Log in or Create an Account'));
    this.twitterLink_ = 
        $d('a', {'className': 'mirosubs-twitter', 'href': '#'},
           $d('span', null, 'Twitter'));
    this.openidLink_ =
        $d('a', {'className': 'mirosubs-openid', 'href': '#'},
           $d('span', null, 'OpenID'));
    this.googleLink_ =
        $d('a', {'className': 'mirosubs-google', 'href': '#'},
           $d('span', null, 'Google'));
    var el = this.getContentElement();
    if (this.message_)
        goog.dom.appendChild(el,
            $d('h4', {'className': 'mirosubs-login-message'},
               this.message_));
    goog.dom.appendChild(
        el, $d('h4', null, 'Login using any of these options'));
    goog.dom.appendChild(el, this.loginLink_);
    goog.dom.appendChild(el, this.twitterLink_);
    goog.dom.appendChild(el, this.openidLink_);
    goog.dom.appendChild(el, this.googleLink_);
    goog.dom.appendChild(
        el, 
        $d('p', 'mirosubs-small', 
           'For security, the login prompt will open in a separate window.'));
};

mirosubs.LoginDialog.prototype.showLoading_ = function() {
    goog.dom.removeChildren(this.getElement());
    this.getElement().appendChild(
        this.getDomHelper().createDom(
            'img', {
                'className': 'big_spinner', 
                'src': this.bigSpinnerGifURL_
            }));
};

mirosubs.LoginDialog.prototype.enterDocument = function() {
    mirosubs.LoginDialog.superClass_.enterDocument.call(this);
    var that = this;
    this.getHandler().
        // for some reason, event.target doesn't get transmitted 
        // for this.loginLink_
        listen(this.loginLink_, 'click', this.siteLoginClicked_).
        listen(this.twitterLink_, 'click', this.clicked_).
        listen(this.openidLink_, 'click', this.clicked_).
        listen(this.googleLink_, 'click', this.clicked_);
};

mirosubs.LoginDialog.prototype.siteLoginClicked_ = function(e) {
    this.showLoading_();
    mirosubs.openLoginPopup(
        mirosubs.LoginPopupType.NATIVE,
        goog.bind(this.processCompleted_, this));
    e.preventDefault();
};

mirosubs.LoginDialog.prototype.clicked_ = function(e) {
    this.showLoading_();
    var type;
    if (e.target == this.loginLink_)
        type = mirosubs.LoginPopupType.NATIVE;
    else if (e.target == this.twitterLink_)
        type = mirosubs.LoginPopupType.TWITTER;
    else if (e.target == this.openidLink_)
        type = mirosubs.LoginPopupType.OPENID;
    else
        type = mirosubs.LoginPopupType.GOOGLE;
    mirosubs.openLoginPopup(
        type, goog.bind(this.processCompleted_, this));
    e.preventDefault();
};

mirosubs.LoginDialog.prototype.processCompleted_ = function(loggedIn) {
    this.loggedIn_ = loggedIn;
    this.setVisible(false);
};

mirosubs.LoginDialog.prototype.setVisible = function(visible) {
    mirosubs.LoginDialog.superClass_.setVisible.call(this, visible);
    mirosubs.LoginDialog.currentDialog_ = visible ? this : null;
    if (!visible && this.finishFn_)
        this.finishFn_(this.loggedIn_);
};

mirosubs.LoginDialog.prototype.disposeInternal = function() {
    mirosubs.LoginDialog.superClass_.disposeInternal.call(this);
    this.imageLoader_.dispose();
};

mirosubs.LoginDialog.isCurrentlyShown = function() {
    return mirosubs.LoginDialog.currentDialog_ != null;
};

mirosubs.LoginDialog.logout = function() {
    mirosubs.Rpc.call('logout', {}, function(result) {
        mirosubs.currentUsername = null;
        mirosubs.userEventTarget.dispatchEvent(mirosubs.EventType.LOGOUT);
    });
};
