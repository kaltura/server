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

goog.provide('mirosubs.UnitOfWork');
goog.provide('mirosubs.UnitOfWork.EventType');

mirosubs.UnitOfWork = function() {
    goog.events.EventTarget.call(this);
    this.instantiateLists_();
    this.everContainedWork_ = false;
};
goog.inherits(mirosubs.UnitOfWork, goog.events.EventTarget);

mirosubs.UnitOfWork.EventType = {
    WORK_PERFORMED: 'workperformed'
};

mirosubs.UnitOfWork.prototype.instantiateLists_ = function() {
    this.updated = [];
    this.deleted = [];
    this.neu = [];
};

mirosubs.UnitOfWork.prototype.registerNew = function(obj) {
    if (goog.array.contains(this.updated, obj) ||
        goog.array.contains(this.deleted, obj) ||
        goog.array.contains(this.neu, obj))
        throw new "registerNew failed";
    this.everContainedWork_ = true;
    this.neu.push(obj);
    this.issueWorkEvent_();
};

mirosubs.UnitOfWork.prototype.registerUpdated = function(obj) {
    if (goog.array.contains(this.deleted, obj))
        throw new "registerUpdated failed";
    if (!goog.array.contains(this.neu, obj) && 
        !goog.array.contains(this.updated, obj)) {
        this.everContainedWork_ = true;
        this.updated.push(obj);
        this.issueWorkEvent_();
    }
};

mirosubs.UnitOfWork.prototype.registerDeleted = function(obj) {
    if (goog.array.contains(this.neu, obj))
        goog.array.remove(this.neu, obj);
    else {
        this.everContainedWork_ = true;
        goog.array.remove(this.updated, obj);
        if (!goog.array.contains(this.deleted))
            this.deleted.push(obj);
        this.issueWorkEvent_();
    }
};

mirosubs.UnitOfWork.prototype.everContainedWork = function() {
    return this.everContainedWork_;
};

mirosubs.UnitOfWork.prototype.containsWork = function() {
    return this.updated.length > 0 || 
        this.deleted.length > 0 || 
        this.neu.length > 0;
};

mirosubs.UnitOfWork.prototype.clear = function() {
    this.instantiateLists_();
};

mirosubs.UnitOfWork.prototype.issueWorkEvent_ = function() {
    this.dispatchEvent(mirosubs.UnitOfWork.EventType.WORK_PERFORMED);
};

mirosubs.UnitOfWork.prototype.getWork = function() {
    return {
        neu: goog.array.clone(this.neu),
        updated: goog.array.clone(this.updated),
        deleted: goog.array.clone(this.deleted) 
    };
};
