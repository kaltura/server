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

goog.provide('mirosubs.subtitle.FinishedPanel');

/**
 * 
 * @param {mirosubs.subtitle.ServerModel} serverModel
 */
mirosubs.subtitle.FinishedPanel = function(serverModel) {
    goog.ui.Component.call(this);
    this.serverModel_ = serverModel;
};
goog.inherits(mirosubs.subtitle.FinishedPanel, goog.ui.Component);
mirosubs.subtitle.FinishedPanel.prototype.createDom = function() {
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.embedCodeInput = $d('input', {'type':'text'});
    var embedCode = ['<sc', 'ript type="text/javascript" src="',
                     this.serverModel_.getEmbedCode(),
                     '"></script>'].join('');
    this.embedCodeInput['value'] = embedCode;
    var flashSpan;
    var helpLi = $d('li', null, 
                    $d('p', null, 
                       ['Thanks for submitting. Now anyone else who views ',
                        'this video can see your work.'].join('')),
                    $d('p', null, 
                       ['To share it with others, or post on your site, ',
                        'use this embed code'].join('')),
                    this.embedCodeInput,
                    flashSpan = $d('span'));
    flashSpan.innerHTML = mirosubs.Clippy.getHTML(embedCode);
    this.setElementInternal($d('ul', {'className':'mirosubs-titlesList'},
                               helpLi));
};
mirosubs.subtitle.FinishedPanel.prototype.enterDocument = function() {
    mirosubs.subtitle.FinishedPanel.superClass_.enterDocument.call(this);
    var that = this;
    this.getHandler().listen(this.embedCodeInput,
                             ['focus', 'click'],
                             this.focusEmbed);
};
mirosubs.subtitle.FinishedPanel.prototype.focusEmbed = function() {
    var that = this;
    goog.Timer.callOnce(function() {
        that.embedCodeInput.select();
    });
};