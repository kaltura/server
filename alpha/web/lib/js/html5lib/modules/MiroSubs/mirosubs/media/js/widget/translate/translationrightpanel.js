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

goog.provide('mirosubs.translate.TranslationRightPanel');

mirosubs.translate.TranslationRightPanel = function(serverModel,
                                                    helpContents,
                                                    extraHelp,
                                                    legendKeySpecs,
                                                    showRestart,
                                                    doneStrongText,
                                                    doneText,
                                                    extraHelpHeader) {
    this.extraHelpHeader_ = extraHelpHeader;
    mirosubs.RightPanel.call(this, serverModel, helpContents, extraHelp,
                             legendKeySpecs, 
                             showRestart, doneStrongText, doneText);
};
goog.inherits(mirosubs.translate.TranslationRightPanel, mirosubs.RightPanel);

mirosubs.translate.TranslationRightPanel.prototype.appendExtraHelpInternal =
    function($d, el)
{
    var extraDiv = $d('div', 'mirosubs-extra mirosubs-translationResources');
    extraDiv.appendChild($d('h3', {'className': 'mirosubs-resources'}, this.extraHelpHeader_));
    
    var lst = $d('ul', {'className': 'mirosubs-resourceList'});
    for (var i = 0; i < this.extraHelp_.length; i++) {
        var linkText = this.extraHelp_[i][0];
        var linkHref = this.extraHelp_[i][1];
        lst.appendChild($d('li', {'className': 'mirosubs-resource'},
                           $d('a', {'target':'_blank', 'href': linkHref,
                                    'className': 'mirosubs-resourceLink' },
                              linkText)));
    }
    extraDiv.appendChild(lst);
    el.appendChild(extraDiv);
};
