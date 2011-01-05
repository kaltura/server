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

goog.provide('mirosubs.widget.NewTranslationState');

mirosubs.widget.NewTranslationState = function(widget, videoID) {
    mirosubs.widget.WidgetState.call(this, widget);
    this.videoID_ = videoID;
};
goog.inherits(mirosubs.widget.NewTranslationState, mirosubs.widget.WidgetState);

mirosubs.widget.NewTranslationState.prototype.initialize = function(callback) {
    mirosubs.Rpc.call(
        "fetch_subtitles_and_open_languages",
        { "video_id": this.videoID_ },
        callback);
};

mirosubs.widget.NewTranslationState.prototype.getVideoTabText = function() {
    return "Translating...";
};