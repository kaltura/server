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

goog.provide('mirosubs.video');

mirosubs.video.Html5VideoType = {
    H264: 1,
    OGG: 2,
    WEBM: 3
};

mirosubs.video.supportsVideo = function() {
    return !!goog.dom.createElement('video')['canPlayType'];
};

mirosubs.video.supportsVideoType = function(html5VideoType) {
    var v = mirosubs.video;
    var vt = v.Html5VideoType;
    switch (html5VideoType) {
    case vt.H264:
        return v.supportsH264();
    case vt.OGG:
        return v.supportsOgg();
    case vt.WEBM:
        return v.supportsWebM();
    default:
        throw "unknown type";
    }
};

mirosubs.video.supports_ = function(playType) {
    var video = document.createElement('video');
    return !!(video['canPlayType'] &&
              video['canPlayType'](playType).replace(/no/, ''));
};

mirosubs.video.supportsH264 = function() {
    return mirosubs.video.supports_(
        'video/mp4; codecs="avc1.42E01E, mp4a.40.2"');
};

mirosubs.video.supportsOgg = function() {
    return mirosubs.video.supports_(
        'video/ogg; codecs="theora, vorbis"');
};

mirosubs.video.supportsWebM = function() {
    return mirosubs.video.supports_(
        'video/webm; codecs="vp8, vorbis"');
};
