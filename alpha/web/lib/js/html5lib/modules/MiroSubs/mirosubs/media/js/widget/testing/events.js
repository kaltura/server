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
 * @fileoverview Event Simulation.
 *
 * Utilities to complement those in goog.testing.events for testing 
 * events at the Closure level. In particular, provides fireKeyDown 
 * and fireKeyUp functions, which are not provided by 
 * goog.testing.events.
 */

goog.provide('mirosubs.testing.events');

/**
 * Simulates a keydown event.
 *
 * @param {EventTarget} target The target for the event.
 * @param {number} keyCode The keycode of the key pressed.
 * @return {boolean} The returnValue of the event: false 
 *     if preventDefault() was called, true otherwise.
 */
mirosubs.testing.events.fireKeyDown = function(target, keyCode) {
    var keydown =
    new goog.testing.events.Event(goog.events.EventType.KEYDOWN, 
                                  target);
    keydown.keyCode = keyCode;
    return goog.testing.events.fireBrowserEvent(keydown);
};

/**
 * Simulates a keyup event.
 *
 * @param {EventTarget} target The target for the event.
 * @param {number} keyCode The keycode of the key pressed.
 * @return {boolean} The returnValue of the event: false 
 *     if preventDefault() was called, true otherwise.
 */
mirosubs.testing.events.fireKeyUp = function(target, keyCode) {
    var keyup =
    new goog.testing.events.Event(goog.events.EventType.KEYUP, 
                                  target);
    keyup.keyCode = keyCode;
    return goog.testing.events.fireBrowserEvent(keyup);
};