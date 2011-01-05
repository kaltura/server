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

goog.provide('mirosubs.Flash');

mirosubs.Flash.FF_HTML_ =
    '<embed quality="high"' +
    ' id="%s"' +
    ' name="%s"' +
    ' class="%s"' +
    ' width="%s"' +
    ' height="%s"' +
    ' src="%s"' +
    ' FlashVars="%s"' +
    ' bgcolor="%s"' +
    ' AllowScriptAccess="sameDomain"' +
    ' allowFullScreen="true"' +
    ' SeamlessTabbing="false"' +
    ' type="application/x-shockwave-flash"' +
    ' pluginspage="http://www.macromedia.com/go/getflashplayer"' +
    ' wmode="transparent">' +
    '</embed>';

mirosubs.Flash.IE_HTML_ =
    '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"' +
    ' id="%s"' +
    ' name="%s"' +
    ' class="%s"' +
    ' width="%s"' +
    ' height="%s"' +
    '>' +
    '<param name="movie" value="%s"/>' +
    '<param name="quality" value="high"/>' +
    '<param name="FlashVars" value="%s"/>' +
    '<param name="bgcolor" value="%s"/>' +
    '<param name="AllowScriptAccess" value="sameDomain"/>' +
    '<param name="allowFullScreen" value="true"/>' +
    '<param name="SeamlessTabbing" value="false"/>' +
    '<param name="wmode" value="transparent"/>' +
    '</object>';


/**
 *
 * @param {goog.structs.Map} flashVars
 */
mirosubs.Flash.getHTML = function(id, swfURL, width, height, flashVars) {
    var template = goog.userAgent.IE ? 
        mirosubs.Flash.IE_HTML_ : mirosubs.Flash.FF_HTML_;
    var keys = flashVars.getKeys();
    var values = flashVars.getValues();
    var flashVarsArr = [];
    for (var i = 0; i < keys.length; i++)
        flashVarsArr.push(
            [goog.string.urlEncode(keys[i]), 
             '=', 
             goog.string.urlEncode(values[i])].join(''));
    return goog.string.subs(
        template,
        id, id,
        'mirosubs-flash',
        '' + width,
        '' + height,
        goog.string.htmlEscape(swfURL),
        goog.string.htmlEscape(flashVarsArr.join('&')),
        "#000000");
};