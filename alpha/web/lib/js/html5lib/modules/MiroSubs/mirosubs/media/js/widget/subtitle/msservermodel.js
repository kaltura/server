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
 * @fileoverview ServerModel implementation for MiroSubs server.
 *
 */

goog.provide('mirosubs.subtitle.MSServerModel');

/**
 *
 *
 * @constructor
 * @implements {mirosubs.subtitle.ServerModel}
 * @extends {goog.Disposable}
 * @param {string} videoID MiroSubs videoid
 * @param {string=} language language code
 */
mirosubs.subtitle.MSServerModel = function(videoID, videoURL, language) {
    goog.Disposable.call(this);
    this.videoID_ = videoID;
    this.videoURL_ = videoURL;
    this.language_ = language;
    this.initialized_ = false;
    this.finished_ = false;
};
goog.inherits(mirosubs.subtitle.MSServerModel, goog.Disposable);

/*
 * URL for the widget's embed javascript.
 * Set by mirosubs.EmbeddableWidget when widget first loads.
 * @type {string} 
 */
mirosubs.subtitle.MSServerModel.EMBED_JS_URL = null;

mirosubs.subtitle.MSServerModel.logger_ = 
    goog.debug.Logger.getLogger('mirosubs.subtitle.MSServerModel');

// updated by values from server when widgets load.
mirosubs.subtitle.MSServerModel.LOCK_EXPIRATION = 0;

mirosubs.subtitle.MSServerModel.prototype.init = function(unitOfWork) {
    goog.asserts.assert(!this.initialized_);
    mirosubs.subtitle.MSServerModel.logger_.info(
        'init for ' + mirosubs.currentUsername);
    this.unitOfWork_ = unitOfWork;
    this.initialized_ = true;
    this.timerRunning_ = true;
    var that = this;
    this.timerInterval_ = 
    window.setInterval(function() {
            that.timerTick_();
        }, 
        (mirosubs.subtitle.MSServerModel.LOCK_EXPIRATION - 5) * 1000);
};

mirosubs.subtitle.MSServerModel.prototype.finish = 
    function(jsonSubs, successCallback, opt_cancelCallback) 
{
    goog.asserts.assert(this.initialized_);
    goog.asserts.assert(!this.finished_);
    this.stopTimer_();
    var that = this;
    this.loginThenAction_(function() {
        var $e = goog.json.serialize;
        var saveArgs = that.makeSaveArgs_();
        mirosubs.Rpc.call(
            'finished_subtitles', 
            saveArgs,
            function(result) {
                if (result['response'] != 'ok')
                    // this should never happen.
                    alert('Problem saving subtitles. Response: ' +
                          result["response"]);
                this.finished_ = true;
                successCallback(mirosubs.widget.DropDownContents.fromJSON(
                    result['drop_down_contents']));
            });
    }, opt_cancelCallback, true);
};

mirosubs.subtitle.MSServerModel.prototype.timerTick_ = function() {
    this.loginThenAction_(goog.bind(this.saveImpl_, this));
};

mirosubs.subtitle.MSServerModel.prototype.loginThenAction_ = 
    function(successAction, opt_cancelAction, opt_forceLogin) {

    mirosubs.subtitle.MSServerModel.logger_.info(
        "loginThenAction_ for " + mirosubs.currentUsername);
    if (mirosubs.currentUsername == null) {
        // first update lock anyway.
        if (!mirosubs.IS_NULL)
            mirosubs.Rpc.call("update_lock", 
                              { 'video_id': this.videoID_,
                                'language_code': this.language_});
        if (mirosubs.isLoginAttemptInProgress())
            return;
        if (opt_forceLogin) {
            mirosubs.login(function(loggedIn) {
                if (loggedIn)
                    successAction();
                else if (opt_cancelAction)
                    opt_cancelAction();
            }, "In order to finish and save your work, you need to log in.");
        }
    }
    else
        successAction();
};

mirosubs.subtitle.MSServerModel.prototype.saveImpl_ = function() {
    // TODO: at some point in future, account for possibly failed save.
    var $e = goog.json.serialize;
    var saveArgs = this.makeSaveArgs_();
    mirosubs.Rpc.call(
        'save_subtitles',
        saveArgs, 
        function(result) {
            if (result['response'] != 'ok')
                // this should never happen.
                alert('Problem saving subtitles. Response: ' + 
                      result['response']);
        });
};

mirosubs.subtitle.MSServerModel.prototype.makeSaveArgs_ = function() {
    var work = this.unitOfWork_.getWork();
    this.unitOfWork_.clear();
    var toJsonCaptions = function(arr) {
        return goog.array.map(arr, function(editableCaption) {
                return editableCaption.json;
            });
    };
    return {
        'video_id': this.videoID_,
        'language_code': this.language_,
        'deleted': toJsonCaptions(work.deleted),
        'inserted': toJsonCaptions(work.neu),
        'updated': toJsonCaptions(work.updated)
    };
};

mirosubs.subtitle.MSServerModel.prototype.getEmbedCode = function() {
    return [
        '<sc',
        'ript type="text/javascript" src="',
        mirosubs.mediaURL(),
        'embed', mirosubs.embedVersion, '.js',
        '">\n',
        '({\n',
        '   video_url: "', this.videoURL_, '"\n',
        '})\n',
        '</script>'].join('');
};

mirosubs.subtitle.MSServerModel.prototype.stopTimer_ = function() {
    if (this.timerRunning_) {
        window.clearInterval(this.timerInterval_);
        this.timerRunning_ = false;
    }
};

mirosubs.subtitle.MSServerModel.prototype.disposeInternal = function() {
    this.stopTimer_();
};

mirosubs.subtitle.MSServerModel.prototype.currentUsername = function() {
    return mirosubs.currentUsername;
};

mirosubs.subtitle.MSServerModel.prototype.logIn = function() {
    mirosubs.login();
};

mirosubs.subtitle.MSServerModel.prototype.getPermalink = function() {
    return [mirosubs.siteURL(), "/videos/", this.videoID_, "/"].join('');
};