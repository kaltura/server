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

goog.provide('mirosubs.subtitle.EditPanel');

mirosubs.subtitle.EditPanel = function(subtitles, videoPlayer, 
                                         serverModel, captionManager) {
    mirosubs.subtitle.SyncPanel.call(this, subtitles, videoPlayer, 
                                     serverModel, captionManager);
};
goog.inherits(mirosubs.subtitle.EditPanel, mirosubs.subtitle.SyncPanel);
mirosubs.subtitle.EditPanel.prototype.createRightPanelInternal =
    function()
{
    return new mirosubs.subtitle.EditRightPanel(
        this.serverModel, null, null,
        this.makeKeySpecsInternal(), false, "Done?",
        "Submit your work");
}