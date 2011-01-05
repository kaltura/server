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

goog.provide('mirosubs.widget.EditTranslationState');

mirosubs.widget.EditTranslationState = function(widget, videoID, languageCode, baseState) {
    mirosubs.widget.WidgetState.call(this, widget);
    this.videoID_ = videoID;
    this.languageCode_ = languageCode;
    this.baseState_ = baseState;
};
goog.inherits(mirosubs.widget.EditTranslationState, mirosubs.widget.WidgetState);

mirosubs.widget.EditTranslationState.prototype.initialize = function(callback) {
    mirosubs.Rpc.call(
        "start_editing",
        { "video_id": this.videoID_,
          "language_code": this.languageCode_,
          "editing": true,
          "base_version_no": this.baseState_.REVISION},
        callback);
};

mirosubs.widget.EditTranslationState.prototype.getVideoTabText = function() {
    return "Editing translations";
};