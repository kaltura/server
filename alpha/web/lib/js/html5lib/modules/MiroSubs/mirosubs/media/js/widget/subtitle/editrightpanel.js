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

goog.provide('mirosubs.subtitle.EditRightPanel');

mirosubs.subtitle.EditRightPanel = function(serverModel,
                                            helpContents,
                                            extraHelp,
                                            legendKeySpecs,
                                            showRestart,
                                            doneStrongText,
                                            doneText) {
    mirosubs.RightPanel.call(this, serverModel, helpContents, 
                             extraHelp, legendKeySpecs, 
                             showRestart, doneStrongText, 
                             doneText);
};
goog.inherits(mirosubs.subtitle.EditRightPanel, mirosubs.RightPanel);
mirosubs.subtitle.EditRightPanel.prototype.appendHelpContentsInternal = function($d, el) {
    var backLink = $d('a', {'href': '#'}, 'click here');
    this.getHandler().listenOnce(
        backLink, 'click', this.backClickedInternal);
    var helpDiv = $d('div', 'mirosubs-help-heading',
                     $d('h2', null, "EDIT: Edit existing subtitles"));
    el.appendChild(helpDiv);
    el.appendChild($d('p', null, 
                      goog.dom.createTextNode(
                          'Double click on any subtitle to edit its text. To add more text, '),
                      backLink,
                      goog.dom.createTextNode(' for TYPING mode.')));
    el.appendChild($d('p', null, 'Adjust subtitle timing by dragging their edges in the timeline to the left and watching the results.'));
    el.appendChild($d('p', null, 'You can also edit timing by rolling over any timestamp, and clicking the left/right buttons that appear.  After you click, your change will play back.'));
    el.appendChild($d('p', null, 'Hitting the DOWN ARROW will set the start of the next subtitle.'));
};
