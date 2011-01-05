/*
 * jSwipe - jQuery Plugin
 * http://plugins.jquery.com/project/swipe
 * http://www.ryanscherf.com/demos/swipe/
 *
 * Copyright (c) 2009 Ryan Scherf (www.ryanscherf.com)
 * Licensed under the MIT license
 *
 * $Date: 2009-07-14 (Tue, 14 Jul 2009) $
 * $version: 0.1
 * 
 * This jQuery plugin will only run on devices running Mobile Safari
 * on iPhone or iPod Touch devices running iPhone OS 2.0 or later. 
 * http://developer.apple.com/iphone/library/documentation/AppleApplications/Reference/SafariWebContent/HandlingEvents/HandlingEvents.html#//apple_ref/doc/uid/TP40006511-SW5
 * 
 * modified by michael dale 3 Aug 2010 ( michael.dale@kaltura.com ) to :
 * 	support swipeUp and swipeDown
 * 	threshold now takes primary direction, vertical direction arguments
 * 	minor code cleanup 
 */
(function($) {
	$.fn.swipe = function(options) {
		// Default thresholds & swipe functions
		var defaults = {
			threshold: [ 30,10 ],
			swipeLeft: function() { if( console.log ) console.log( 'swipe left') },
			swipeRight: function() { if( console.log ) console.log( 'swipe right') },
			swipeUp: function() { if( console.log ) console.log( 'swipe up') },
			swipeDown: function() { if( console.log ) console.log( 'swipe down') },
			preventDefaultEvents: true
		};
		
		var options = $.extend(defaults, options);
		
		if (!this) return false;
		
		return this.each(function() {
			
			var me = $(this)
			
			// Private variables for each element
			var originalCoord = { x: 0, y: 0 }
			var finalCoord = { x: 0, y: 0 }
			
			// Screen touched, store the original coordinate
			function touchStart(event) {				
				originalCoord.x = event.targetTouches[0].pageX
				originalCoord.y = event.targetTouches[0].pageY
			}
			
			// Store coordinates as finger is swiping
			function touchMove(event) {
				if ( options.preventDefaultEvents ){
				    event.preventDefault();
				}
				finalCoord.x = event.targetTouches[0].pageX // Updated X,Y coordinates
				finalCoord.y = event.targetTouches[0].pageY
			}
			
			// Done Swiping
			// Swipe should only be on X axis, ignore if swipe on Y axis
			// Calculate if the swipe was left or right
			function touchEnd(event) {				
				var changeY = originalCoord.y - finalCoord.y
				var changeX = originalCoord.x - finalCoord.x
				// see if dominated by vertical or horizontal swipe
				if( changeX  > changeY ){
					// horizontal swipe check if in range: 
					if( Math.abs( changeY ) < options.threshold[1] ) {						
						
						if(changeX > options.threshold[0]) {
							options.swipeLeft( changeX )
						}
						if(changeX < (options.threshold[0]*-1)) {
							options.swipeRight( changeX )
						}
					}
				} else {
					// vertical swap, check if horizontal move in range
					if( Math.abs( changeX ) < options.threshold[1] ) {							
						if(changeY > options.threshold[0]) {
							options.swipeUp( changeY )
						}
						if(changeY < (options.threshold[0]*-1)) {
							options.swipeDown( changeY )
						}
					}
				}
				
			}
			
			// Swipe was canceled
			function touchCancel(event) { 
				console.log('Canceling swipe gesture...')
			}
			
			// Add gestures to all swipable areas
			this.addEventListener("touchstart", touchStart, false);
			this.addEventListener("touchmove", touchMove, false);
			this.addEventListener("touchend", touchEnd, false);
			this.addEventListener("touchcancel", touchCancel, false);
				
		});
	};
})(jQuery);