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

goog.provide('mirosubs.BrokenWarning');

/**
 * @constructor
 */
mirosubs.BrokenWarning = function() {
    goog.ui.Dialog.call(this, null, true);
    this.setButtonSet(null);
    this.setDisposeOnHide(true);
};
goog.inherits(mirosubs.BrokenWarning, goog.ui.Dialog);

mirosubs.BrokenWarning.logger_ =
    goog.debug.Logger.getLogger('mirosubs.BrokenWarning');

mirosubs.BrokenWarning.prototype.createDom = function() {
    mirosubs.BrokenWarning.superClass_.createDom.call(this);
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    var text = "Sorry, <a href='http://universalsubtitles.org'>Universal " +
        "Subtitles</a> doesn't " +
        "support your browser yet. Upgrade your browser or " +
        "<a href='http://getfirefox.com'>Try Firefox</a>.";
    this.okLink_ = $d('a', {'className':'mirosubs-link', 'href':'#'}, 'Okay');
    this.getElement().className = 'mirosubs-warning';
    var label = $d('div', 'mirosubs-label');
    label.innerHTML = text;
    this.getElement().appendChild(label);
    this.getElement().appendChild($d('div', 'mirosubs-buttons', this.okLink_));
};

mirosubs.BrokenWarning.prototype.enterDocument = function() {
    mirosubs.BrokenWarning.superClass_.enterDocument.call(this);
    var that = this;
    this.getHandler().listenOnce(
        this.okLink_, 'click', 
        function(e) {
            e.preventDefault();
            that.setVisible(false);
        });
};

mirosubs.BrokenWarning.needsWarning = function() {
    return goog.userAgent.IE && !goog.userAgent.isVersion(8);
};