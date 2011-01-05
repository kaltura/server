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

goog.provide('mirosubs.subtitle.ReviewPanel');

mirosubs.subtitle.ReviewPanel = function(subtitles, videoPlayer,
                                         serverModel, captionManager) {
    mirosubs.subtitle.SyncPanel.call(this, subtitles, videoPlayer,
                                     serverModel, captionManager);
};
goog.inherits(mirosubs.subtitle.ReviewPanel, mirosubs.subtitle.SyncPanel);
/**
 * @override
 */
mirosubs.subtitle.ReviewPanel.prototype.createRightPanelInternal =
    function()
{
    var helpContents = new mirosubs.RightPanel.HelpContents(
        "Review and make corrections",
        null, 3, 2);
    helpContents.html = 
        "<p>Watch the video one more time and correct any mistakes in text or timing. Tips for making high quality subtitles:</p>" +
        "<ul>" +
        "<li>Include text that appears in the video (signs, etc.)</li>" +
        "<li>Include important sounds in [brackets]</li>" +  
        "<li>It's best to split subtitles at the end of a sentence or a long phrase.</li>" +
        "</ul>";
    return new mirosubs.subtitle.ReviewRightPanel(
        this.serverModel, helpContents, [],
        this.makeKeySpecsInternal(), false, "Done?",
        "Submit your work");
};