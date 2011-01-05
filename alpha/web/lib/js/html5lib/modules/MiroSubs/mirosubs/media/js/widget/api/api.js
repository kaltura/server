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

goog.provide('mirosubs.api');

mirosubs.api.openDialog = function(config) {
    if (config['mediaURL'])
        mirosubs.Config.siteConfig['mediaURL'] = config['mediaURL'];
    var subtitles = config['subtitles'];
    var closeListener = config['closeListener'];
    var videoURL = config['videoURL'];
    var videoSource = new mirosubs.video.Html5VideoSource(
        videoURL, mirosubs.video.Html5VideoType.OGG);
    var serverModel = new mirosubs.api.ServerModel(config);
    var subDialog = new mirosubs.subtitle.Dialog(
        videoSource, serverModel, subtitles, 
        config['skipFinished']);
    mirosubs.currentUsername = config['username'];
    subDialog.setVisible(true);
    goog.events.listenOnce(
        subDialog,
        goog.ui.Dialog.EventType.AFTER_HIDE,
        closeListener);
    return {
        "close": function() { subDialog.setVisible(false); }
    };
};

mirosubs.api.toSRT = function(jsonSubs) {
    var stringBuffer = new goog.string.StringBuffer();
    for (var i = 0; i < jsonSubs.length; i++)
        mirosubs.api.subToSrt_(jsonSubs[i], i, stringBuffer);
    return stringBuffer.toString();
};

mirosubs.api.loggedIn = function(username) {
    mirosubs.loggedIn(username);
};

mirosubs.api.subToSrt_ = function(sub, index, stringBuffer) {
    stringBuffer.
        append(index + 1).
        append("\n");
    mirosubs.api.writeSrtTimeLine_(sub, stringBuffer);
    stringBuffer.
        append(sub['text']).
        append("\n\n");
};

mirosubs.api.writeSrtTimeLine_ = function(sub, stringBuffer) {
    mirosubs.api.writeSrtTime_(sub['start_time'], stringBuffer);
    stringBuffer.append(' --> ');
    mirosubs.api.writeSrtTime_(sub['end_time'], stringBuffer);
    stringBuffer.append('\n');
};

mirosubs.api.writeSrtTime_ = function(seconds, stringBuffer) {
    var secondsInt = Math.floor(seconds);
    var p = goog.string.padNumber;
    stringBuffer.
        append(p(Math.floor(secondsInt / 3600) % 60, 2)).
        append(':').
        append(p(Math.floor(secondsInt / 60) % 60, 2)).
        append(':').
        append(p(secondsInt % 60, 2)).
        append(',').
        append(p(Math.floor(seconds * 1000) % 1000, 3));
};

if (!window['MiroCSSLoading']) {
    window['MiroCSSLoading'] = true;
    var css = goog.dom.createDom(
        'link',
        {
            'type': 'text/css',
            'rel': 'stylesheet',
            'href': mirosubs.Config.siteConfig['mediaURL'] + 
                'css/mirosubs-widget.css',
            'media': 'screen'
        }
    );
    goog.dom.getDocument().getElementsByTagName('head')[0].appendChild(css);
}


mirosubs.siteConfig = mirosubs.Config.siteConfig;

goog.exportSymbol(
    'mirosubs.api.openDialog',
    mirosubs.api.openDialog);

goog.exportSymbol(
    'mirosubs.api.toSRT',
    mirosubs.api.toSRT);

goog.exportSymbol(
    'mirosubs.api.loggedIn',
    mirosubs.api.loggedIn);