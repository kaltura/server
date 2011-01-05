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

/**
 * @fileoverview Definition of the the mirosubs.subtitle.ServerModel 
 *     interface
 *
 */

goog.provide('mirosubs.subtitle.ServerModel');

/**
 * Interface for interaction with server during subtitling work.
 * @interface
 */
mirosubs.subtitle.ServerModel = function() {};

/**
 * Initializes this ServerModel with a UnitOfWork. The server model then 
 * proceeds to save periodically (or not) using the work recorded by the 
 * UnitOfWork. This method can only be called once.
 * @param {mirosubs.UnitOfWork} unitOfWork
 */
mirosubs.subtitle.ServerModel.prototype.init = function(unitOfWork) {};

/**
 * Announces to the server that subtitling is finished. Also frees timers, 
 * etc. This method can only be called after init, and can only be called 
 * once.
 * @param {function()} callback
 */
mirosubs.subtitle.ServerModel.prototype.finish = function(
    jsonSubs, callback, opt_cancelCallback) {};

/**
 * Instances implementing this interface must extend goog.Disposable
 */
mirosubs.subtitle.ServerModel.prototype.dispose = function() {};

mirosubs.subtitle.ServerModel.prototype.getEmbedCode = function() {};

mirosubs.subtitle.ServerModel.prototype.currentUsername = function() {};

mirosubs.subtitle.ServerModel.prototype.logIn = function() {};

mirosubs.subtitle.ServerModel.prototype.getPermalink = function() {};