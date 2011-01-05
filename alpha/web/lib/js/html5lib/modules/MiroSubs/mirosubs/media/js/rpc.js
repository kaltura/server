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


goog.provide('mirosubs.Rpc');

mirosubs.Rpc.logger_ =
    goog.debug.Logger.getLogger('mirosubs.Rpc');

mirosubs.Rpc.baseURL = function() {
    return [mirosubs.siteURL(), 
            '/widget/',
            mirosubs.IS_NULL ? 'null_' : '',
            'rpc/'].join('');
};

mirosubs.Rpc.callCrossDomain_ = function(methodName, serializedArgs, opt_callback) {
    var p = goog.json.parse;
    goog.net.CrossDomainRpc.
        send([mirosubs.Rpc.baseURL(), 'xd/', methodName].join(''),
             function(event) {
                 var responseText = event["target"]["responseText"];
                 mirosubs.Rpc.logResponse_(methodName, responseText);
                 if (opt_callback)
                     opt_callback(p(responseText));
             }, "POST", serializedArgs);
};

mirosubs.Rpc.callXhr_ = function(methodName, serializedArgs, opt_callback) {
    goog.net.XhrIo.send(
        [mirosubs.Rpc.baseURL(), 'xhr/', methodName].join(''),
        function(event) {
            mirosubs.Rpc.logResponse_(
                methodName, 
                event.target.getResponseText());
            if (opt_callback)
                opt_callback(event.target.getResponseJson());
        },
        "POST", mirosubs.Rpc.encodeKeyValuePairs_(serializedArgs));
};

mirosubs.Rpc.encodeKeyValuePairs_ = function(serializedArgs) {
    var queryData = new goog.Uri.QueryData();
    for (var param in serializedArgs)
        queryData.set(param, serializedArgs[param]);
    return queryData.toString();
};

mirosubs.Rpc.callWithJsonp_ = function(methodName, serializedArgs, opt_callback) {
    var jsonp = new goog.net.Jsonp(
        [mirosubs.Rpc.baseURL(), 'jsonp/', methodName].join(''));
    jsonp.send(serializedArgs,
               function(result) {
                   if (mirosubs.DEBUG)
                       mirosubs.Rpc.logResponse_(
                           methodName, goog.json.serialize(result));
                   if (opt_callback)
                       opt_callback(result);
               });
};

mirosubs.Rpc.logCall_ = function(methodName, args, channel) {
    if (mirosubs.DEBUG)
        mirosubs.Rpc.logger_.info(
            ['calling ', methodName, ' with ', channel,
             ': ', goog.json.serialize(args)].join(''));
};

mirosubs.Rpc.logResponse_ = function(methodName, response) {
    if (mirosubs.DEBUG)
        mirosubs.Rpc.logger_.info(
            [methodName, ' response: ', response].join(''));
};

mirosubs.Rpc.call = function(methodName, args, opt_callback) {
    var s = goog.json.serialize;
    var serializedArgs = {};
    var arg;
    var totalSize = 0;
    for (var param in args) {
        arg = s(args[param]);
        serializedArgs[param] = arg;
        totalSize += arg.length;
    }
    var callType = ''
    if (mirosubs.isEmbeddedInDifferentDomain()) {
        if (totalSize < 2000) {
            callType = 'jsonp';
            mirosubs.Rpc.callWithJsonp_(
                methodName, serializedArgs, opt_callback);
        }
        else {
            callType = 'xd-rpc';
            mirosubs.Rpc.callCrossDomain_(
                methodName, serializedArgs, opt_callback);
        }
    } else {
        callType = 'xhr';
        mirosubs.Rpc.callXhr_(
            methodName, serializedArgs, opt_callback);
    }
    mirosubs.Rpc.logCall_(methodName, args, callType);
};

