//MooCanvas, My Object Oriented Canvas Element. Copyright (c) 2007 Olmo Maldonado, <http://ibolmo.no-ip.info/sandbox/moocanvas/>, MIT Style License.
/*
Script: Canvas.js
	Contains the <Canvas> class.

Dependencies:
	MooTools, <http://mootools.net/>
		Element, and its dependencies

Author:
	Olmo Maldonado, <http://olmo-maldonado.com/>
	
Credits:
	Lightly based from Ralph Sommerer's work: <http://blogs.msdn.com/sompost/archive/2006/02/22/536967.aspx>
	Moderately based from excanvas: <http://excanvas.sourceforge.net/>
	Great thanks to Inviz, <http://inviz.ru/>, for his optimizing help.
	
License:
	MIT License, <http://en.wikipedia.org/wiki/MIT_License>
*/

/*
Class: Canvas
	Creates the element <canvas> and extends the element with getContext if not defined.

Arguments:
	id - The ID of the canvas element
	props - Optional properties for the canvas element, which also gets passed to the new Element
	
Example:
	> var cv = new Canvas('cv');
	> var ctx = cv.getContext('2d');
	> 
	> $(document.body).adopt(cv);
*/
var MooCanvas = new Class({
	
	initialize: function(id, props) {
		var el;
		if($type(id) == 'string') {
			props = $merge({width: 300, height: 150}, props, {'id': id});
			el = new Element('canvas', props);
			if(!el.getContext) {
				if(!CanvasRenderingContext2D.cssFixed) {
					document.createStyleSheet().cssText = 
						'canvas{display:inline-block;overflow:hidden;text-align:left;cursor:default;}' +
						'v\\:*{behavior:url(#default#VML)}' + 
						'o\\:*{behavior:url(#default#VML)}';
					CanvasRenderingContext2D.cssFixed = true;
				}
		
				el.set({
					styles: { 
						width: props.width,
						height: props.height,
						display: 'inline-block',
						overflow: 'hidden'
					},
					
					getContext: function() {
						this.context = this.context || new CanvasRenderingContext2D(el);
						return this.context;
					}
				});
				
			}
		}
		
		return el;
	}
	
});

/*
Class: CanvasRenderingContext2D
	Context2D class with all the Context methods specified by the WHATWG, <http://www.whatwg.org/specs/web-apps/current-work/#the-canvas>
	
Arguments:
	el - Element requesting the context2D
*/
var CanvasRenderingContext2D = new Class({

	initialize: function(el) {
		this.parent = el;
		this.fragment = document.createDocumentFragment();
		this.element = new Element('div', {
			styles: {
				width: el.clientWidth || el.width,
				height: el.clientHeight || el.height,
				overflow: 'hidden',
				position: 'absolute'	
			}
		});
		this.fragment.appendChild(this.element);

		this.m = [
			[1, 0, 0],
			[0, 1, 0],
			[0, 0, 1]
		];
		this.rot = 0;
		this.state = [];
		this.path = [];
		this.delay = 30;
		this.max = 10;
		this.i = 0;
		
		// from excanvas, subpixel rendering.
		this.Z = 10;
		this.Z2 = this.Z / 2;
		this.arcScaleX = 1;
		this.arcScaleY = 1;
		this.currentX = 0;
		this.currentY = 0;
		
		
		this.miterLimit = this.Z * 1;
	},
	
	lineWidth: 1,
	strokeStyle: '#000',
	fillStyle: '#fff',
	globalAlpha: 1,
	globalCompositeOperation: 'source-over',
	lineCap: 'butt',
	lineJoin: 'miter',
	shadowBlur: 0,
	shadowColor: '#000',
	shadowOffsetX: 0,
	shadowOffsetY: 0
	
});

/*
Script: Path.js

Dependencies:
	Canvas.js
	
Author:
	Olmo Maldonado, <http://olmo-maldonado.com/>
	
Credits:
	Lightly based from Ralph Sommerer's work: <http://blogs.msdn.com/sompost/archive/2006/02/22/536967.aspx>
	Moderately based from excanvas: <http://excanvas.sourceforge.net/>
	Great thanks to Inviz, <http://inviz.ru/>, for his optimizing help.
	
License:
	MIT License, <http://en.wikipedia.org/wiki/MIT_License>
*/

CanvasRenderingContext2D.implement({
	
	/*
		A path has a list of zero or more subpaths. 
		Each subpath consists of a list of one or more points, 
		connected by straight or curved lines, and a flag indicating whether
		the subpath is closed or not. A closed subpath is one where the
		last point of the subpath is connected to the first point of
		the subpath by a straight line. Subpaths with fewer than two
		points are ignored when painting the path.
	*/

	/*
	Property:
		Empties the list of subpaths so that the context once again has zero
		subpaths.
	*/
	beginPath: function() {
		this.path = [];
		this.moved = false;
	},
	
	/*
	Property:
		Creates a new subpath with the specified point as its first
		(and only) point.
	*/
	moveTo: function(x, y) {
		this.path.push('m', this.coord(x, y));
		this.currentX = x;
		this.currentY = y;
		this.moved = true;
	},
	
	/*
	Property:
		Does nothing if the context has no subpaths.
		Otherwise, marks the last subpath as closed, create a new
		subpath whose first point is the same as the previous 
		subpath's first point, and finally add this new subpath to the
		path.
	*/
	closePath: function() {
		this.path.push('x');
	},
	
	/*
	Property:
		Method must do nothing if the context has no subpaths. Otherwise, 
		it must connect the last point in the subpath to the given point 
		(x, y) using a straight line, and must then add the given point 
		(x, y) to the subpath.
	*/
	lineTo: function(x, y) {
		this.path.push((this.moved ? 'l' : ','), this.coord(x, y));
		this.currentX = x;
		this.currentY = y;
		this.moved = false;
	},

	/*
	Property:
		Method must do nothing if the context has no subpaths. Otherwise, 
		it must connect the last point in the subpath to the given point 
		(x, y) using a straight line, and must then add the given point 
		(x, y) to the subpath.
	*/
	quadraticCurveTo: function(cpx, cpy, x, y) {
		var cx = 2 * cpx,
			cy = 2 * cpy;
			
		this.bezierCurveTo(
			(cx + this.currentX) / 3, 
			(cy + this.currentY) / 3, 
			(cx + x) / 3, 
			(cy + y) / 3, 
			x, 
			y
		);
	},
	
	/*
	Property:
		Method must do nothing if the context has no subpaths. Otherwise, 
		it must connect the last point in the subpath to the given point 
		(x, y) using a bezier curve with control points (cp1x, cp1y) and 
		(cp2x, cp2y). Then, it must add the point (x, y) to the subpath.
	*/
	bezierCurveTo: function(cp0x, cp0y, cp1x, cp1y, x, y) {
		this.path.push(' c ',
			this.coord(cp0x, cp0y), ",",
			this.coord(cp1x, cp1y), ",",
			this.coord(x, y)
		);
		
		this.currentX = x;
		this.currentY = y;
	},
	
	/*
	Property:
		Method must do nothing if the context has no subpaths. If the context
		does have a subpath, then the behaviour depends on the arguments and 
		the last point in the subpath.
		
		Let the point (x0, y0) be the last point in the subpath. Let The Arc 
		be the shortest arc given by circumference of the circle that has one 
		point tangent to the line defined by the points (x0, y0) and (x1, y1), 
		another point tangent to the line defined by the points (x1, y1) and 
		(x2, y2), and that has radius radius. The points at which this circle 
		touches these two lines are called the start and end tangent points 
		respectively.
		
		If the point (x2, y2) is on the line defined by the points (x0, y0) 
		and (x1, y1) then the method must do nothing, as no arc would satisfy 
		the above constraints.
		
		Otherwise, the method must connect the point (x0, y0) to the start 
		tangent point by a straight line, then connect the start tangent point 
		to the end tangent point by The Arc, and finally add the start and end 
		tangent points to the subpath.
		
		Negative or zero values for radius must cause the implementation to 
		raise an INDEX_SIZE_ERR exception.
	*/
	arcTo: function(x, y, w, h) {

	},
	
	/*
	Property:
		Method draws an arc. If the context has any subpaths, then the method 
		must add a straight line from the last point in the subpath to the 
		start point of the arc. In any case, it must draw the arc between the 
		start point of the arc and the end point of the arc, and add the start 
		and end points of the arc to the subpath. The arc and its start and 
		end points are defined as follows:
		
		Consider a circle that has its origin at (x, y) and that has radius 
		radius. The points at startAngle and endAngle along the circle's 
		circumference, measured in radians clockwise from the positive x-axis, 
		are the start and end points respectively. The arc is the path along 
		the circumference of this circle from the start point to the end point, 
		going anti-clockwise if the anticlockwise argument is true, and 
		clockwise otherwise.
		
		Negative or zero values for radius must cause the implementation to 
		raise an INDEX_SIZE_ERR exception.
	*/
	arc: function(x, y, rad, a0, a1, cw) {
		if(this.rot === 0) rad *= this.Z;
		
		var x0 = Math.cos(a0) * rad,
			y0 = Math.sin(a0) * rad,
			x1 = Math.cos(a1) * rad,
			y1 = Math.sin(a1) * rad;
			
		if (this.rot !== 0) {
			var da = Math.PI / 24;
			this.lineTo(x0 + x, y0 + y);
			if(cw) {
				if (a0 < a1) a0 += 2 * Math.PI;
				while(a0 - da > a1) this.lineTo(x + Math.cos(a0 -= da) * rad, y + Math.sin(a0) * rad);
			} else {
				if (a1 < a0) a1 += 2 * Math.PI;
				while(a0 + da < a1) this.lineTo(x + Math.cos(a0 += da) * rad, y + Math.sin(a0) * rad);
			}
			this.lineTo(x1 + x, y1 + y);
			return;
		}
		
		if (x0 == x1 && !cw) x0 += 0.125;
		
		var c = this.getCoords(x, y);
		this.path.push(cw ? 'at ' : 'wa ',
			Math.round(c.x - this.arcScaleX * rad) + ',' + Math.round(c.y - this.arcScaleY * rad),  ' ',
			Math.round(c.x + this.arcScaleX * rad) + ',' + Math.round(c.y + this.arcScaleY * rad),  ' ',
			this.coord(x0 + x - this.Z2, y0 + y - this.Z2), ' ',
			this.coord(x1 + x - this.Z2, y1 + y - this.Z2)
		);
	},

	/*
	Property:
		method must create a new subpath containing just the four points 
		(x, y), (x+w, y), (x+w, y+h), (x, y+h), with those four points 
		connected by straight lines, and must then mark the subpath as 
		closed. It must then create a new subpath with the point (x, y) 
		as the only point in the subpath.
		
		Negative values for w and h must cause the implementation to raise 
		an INDEX_SIZE_ERR exception.
	*/
	rect: function(x, y, w, h) {
		this.moveTo(x, y);
		this.lineTo(x + w, y);
		this.lineTo(x + w, y + h);
		this.lineTo(x, y + h);
		this.closePath();
	},
	
	/*
	Property:
		Method must fill each subpath of the current path in turn, using 
		fillStyle, and using the non-zero winding number rule. Open subpaths 
		must be implicitly closed when being filled (without affecting the 
		actual subpaths).
	*/
	fill: function() {
		this.stroke(true);
	},


	/*
	Property:
		Method must stroke each subpath of the current path in turn, using 
		the strokeStyle, lineWidth, lineJoin, and (if appropriate) miterLimit 
		attributes.
		
		Paths, when filled or stroked, must be painted without affecting the 
		current path, and must be subject to transformations, shadow effects, 
		global alpha, clipping paths, and global composition operators.
		
		The transformation is applied to the path when it is drawn, not when 
		the path is constructed. Thus, a single path can be constructed and 
		then drawn according to different transformations without recreating 
		the path. 
	*/
	
	stroke: function(fill) {
		if(!this.path.length) return;
		var a, color;
		if (fill) {
			a = [1000, '<v:fill ' + this.processColorObject(this.fillStyle) + '></v:fill>'];
		} else {
			color = this.processColor(this.strokeStyle);
			a = [10, 
			'<v:stroke ' +
				'endcap="' + ((this.lineCap == 'butt') ? 'flat' : this.lineCap) + '" ' +
				'joinstyle="' + this.lineJoin + '" ' +
				'color="' + color.color + '" ' +
				'opacity="' + color.opacity + '"' +
			'/>'];
		}
		this.element.insertAdjacentHTML('beforeEnd', 
			'<v:shape ' +
				'path="' + this.path.join('') + 'e" ' +
				'stroked="' + !fill + '" ' +
				(!fill ? ('strokeweight="' + 0.8 * this.lineWidth * this.m[0][0] + '" ') : '') +
				'filled="' + !!fill + '" ' +
				'coordsize="' + this.Z * a[0] + ',' +  this.Z * a[0] + '" ' +
				'style="width:' + a[0] + 'px; height:' + a[0] + 'px; position: absolute;">' +
				a[1] +
			'</v:shape>'
		);
		
		this.parent.appendChild(this.fragment);
		
		if(fill && this.fillStyle.img) this.element.getLast().fill.alignshape = false; // not sure why this has to be called explicitly
											 
		this.path = [];
	},

	/*
	Property:
		Method must create a new clipping path by calculating the intersection 
		of the current clipping path and the area described by the current path 
		(after applying the current transformation), using the non-zero winding 
		number rule. Open subpaths must be implicitly closed when computing the 
		clipping path, without affecting the actual subpaths.
		
		When the context is created, the initial clipping path is the rectangle 
		with the top left corner at (0,0) and the width and height of the 
		coordinate space. 
	*/
	clip: function() {

	},
	
	/*
	Property:
		Method must return true if the point given by the x and y coordinates 
		passed to the method, when treated as coordinates in the canvas' 
		coordinate space unaffected by the current transformation, is within 
		the area of the canvas that is inside the current path; and must 
		return false otherwise.
	*/
	isPointInPath: function(x, y) {
	
	},
	
	processColor: function(col) { //path
		var a = this.globalAlpha;	
		if (col.substr(0, 3) == 'rgb') {
			if (col.charAt(3) == "a") {
				a*= col.match(/([\d.]*)\)$/)[1];
			}
			
			col = col.rgbToHex();
		}
		return {
			color: col,
			opacity: a
		};
	},
	
	/* 
		If a gradient has no stops defined, then the gradient must be treated as a 
		solid transparent black. Gradients are, naturally, only painted where the 
		stroking or filling effect requires that they be drawn.
	*/
	processColorObject: function(obj) {
		var ret = '', col;
		if(obj.addColorStop) {
			ret += ((obj.r0) ? (
				'type="gradientradial" ' +
				'focusposition="0.2, 0.2" ' +
				'focussize="0.2, 0.2" '
			) : (
				'type="gradient" ' +
				'focus="0" ' +
				'angle="' + (180 + (180 * obj.angle(obj.x0, obj.y0, obj.x1, obj.y1) / Math.PI)) + '" '
			)) +
				'color="' + obj.col0.color + '" ' +
				'opacity="' + obj.col0.opacity * 100 + '%" ' +
				'color2="' + obj.col1.color + '" ' +
				'o:opacity2="' + obj.col1.opacity * 100 + '%" ' +
				'colors="';
			if(obj.stops) {
				for (var i = 0, l = obj.stops.length; i < l; i++) {
					ret += Math.round(100 * obj.stops[i][0]) + '% ' + obj.stops[i][1];
				}
			}
			ret +=
				'" ';
		} else if(obj.img) { //pattern
			ret +=
				'type="tile" ' +
				'src="' + obj.img.src + '" ';
		} else {
			col = this.processColor(obj);
			ret +=
				'color="' + col.color + '" ' +
				'opacity="' + col.opacity + '" ';
		}
		
		return ret;
	},
	
	getCoords: function(x, y) {
		var m = this.m;
		return {
			x: this.Z * (x * m[0][0] + y * m[1][0] + m[2][0]) - this.Z2, 
			y: this.Z * (x * m[0][1] + y * m[1][1] + m[2][1]) - this.Z2
		};
	},

	coord: function(x, y) {
		var m = this.m;
		return  [
			 Math.round(this.Z * (x * m[0][0] + y * m[1][0] + m[2][0]) - this.Z2), ',',
			 Math.round(this.Z * (x * m[0][1] + y * m[1][1] + m[2][1]) - this.Z2)
		].join('');
	}
});
/*
Script: Rects.js

Dependencies: 
	Canvas.js, Path.js

Author:
	Olmo Maldonado, <http://olmo-maldonado.com/>
	
Credits:
	Lightly based from Ralph Sommerer's work: <http://blogs.msdn.com/sompost/archive/2006/02/22/536967.aspx>
	Moderately based from excanvas: <http://excanvas.sourceforge.net/>
	Great thanks to Inviz, <http://inviz.ru/>, for his optimizing help.
	
License:
	MIT License, <http://en.wikipedia.org/wiki/MIT_License>
*/

CanvasRenderingContext2D.implement({
	
	/*
	Property: clearRect
		Clears the pixels in the specified rectangle.
		If height or width are zero has no effect.
		
		If no arguments, clears all of the canvas
		
		Currently, clearRect clears all of the canvas.
	 */
	clearRect: function(x, y, w, h) {
		//if((x <= 0) && (y <= 0) && ( x + w >= this.element.width) && (y + h >= this.element.height)){
			this.element.innerHTML = '';
		//} else {
		//	var f0 = this.fillStyle;
		//	this.fillStyle = '#fff';
		//	this.fillRect(x, y, w, h);
		//	this.fillStyle = f0;
		//}
	},
	
	/*
	Property: fillRect
		Paints the specified rectangle using fillStyle.
		If height or width are zero, this method has no effect.
	 */
	fillRect: function(x, y, w, h) {
		this.rect(x, y, w, h);
		this.fill();
	},
	
	/*
		Draws a rectangular outline of the specified size.
		If width or height are zero: ??
	 */
	strokeRect: function(x, y, w, h) {
		this.rect(x, y, w, h);
		this.stroke();
	}
	
});
/*
Script: Transform.js

Dependencies:
	Canvas.js

Author:
	Olmo Maldonado, <http://olmo-maldonado.com/>
	
Credits:
	Lightly based from Ralph Sommerer's work: <http://blogs.msdn.com/sompost/archive/2006/02/22/536967.aspx>
	Moderately based from excanvas: <http://excanvas.sourceforge.net/>
	Great thanks to Inviz, <http://inviz.ru/>, for his optimizing help.
	
License:
	MIT License, <http://en.wikipedia.org/wiki/MIT_License>
*/

CanvasRenderingContext2D.implement({
	/*
		The transformation matrix is applied to all drawing operations prior 
		to their being rendered. It is also applied when creating the clip region.
		*  The transformations must be performed in reverse order. For instance, 
		if a scale transformation that doubles the width is applied, followed 
		by a rotation transformation that rotates drawing operations by a 
		quarter turn, and a rectangle twice as wide as it is tall is then 
		drawn on the canvas, the actual result will be a square.
	*/

  	/*
  	Property: scale
		Method must add the scaling transformation described by the arguments 
		to the transformation matrix. The x argument represents the scale factor 
		in the horizontal direction and the y argument represents the scale 
		factor in the vertical direction. The factors are multiples.
	*/
	scale: function(x, y) {
		this.arcScaleX *= x;
		this.arcScaleY *= y;
		
		this.matMult([
			[x, 0, 0],
			[0, y, 0],
			[0, 0, 1]
		]);
	},
	
  	/*
  	Property: rotate
		Method must add the rotation transformation described by the argument 
		to the transformation matrix. The angle argument represents a clockwise 
		rotation angle expressed in radians.
	*/
	rotate: function(ang) {
		this.rot += ang;
		var c = Math.cos(ang),
			s = Math.sin(ang);
		
		this.matMult([
			[ c, s, 0],
			[-s, c, 0],
			[ 0, 0, 1]
		]);
	},
	
  	/*
  	Property: translate
		Method must add the translation transformation described by the arguments 
		to the transformation matrix. The x argument represents the translation 
		distance in the horizontal direction and the y argument represents the 
		translation distance in the vertical direction. The arguments are in 
		coordinate space units.
	*/
	translate: function(x, y) {
		this.matMult([
			[1, 0, 0],
			[0, 1, 0],
			[x, y, 1]
		]);
	},
	
  	/*
  	Property: transform
		Method must multiply the current transformation matrix with the matrix described
		by the inputs.
	*/
 	transform: function(m11, m12, m21, m22, dx, dy) {
		this.matMult([
			[m11, m21, dx],
			[m12, m22, dy],
			[  0,   0,  1]
		]);		
	},
  
  	/*
  	Property: setTransform
  		Method must reset the current transform to the identity matrix, and then invoke 
  		the transform method with the same arguments.
  	*/
	setTransform: function(m11, m12, m21, m22, dx, dy) {
		this.m = [
			[1, 0, 0],
			[0, 1, 0],
			[0, 0, 1]
		];
		
		this.transform(m11, m12, m21, m22, dx, dy);
	},
	
	/*
		Property: matMult
			Method to multiply 3x3 matrice. Currently takes input and multiplies against
			the transform matrix and saves the result to the transform matrix.
			
			This is an optimized multiplication method. Will only multiply if the input
			value is not zero. Thus, minimizing multiplications and additions.
	*/
	matMult: function(b) {
		var m = this.m,
			o = [
				[0, 0, 0], 
				[0, 0, 0], 
				[0, 0, 0]
			];
		
		for(var i = 0; i < 3; i++) {
			if(b[0][i] !== 0) this.sum(o[0], this.mult(b[0][i], m[i]));
			if(b[1][i] !== 0) this.sum(o[1], this.mult(b[1][i], m[i]));
			if(b[2][i] !== 0) this.sum(o[2], this.mult(b[2][i], m[i]));
		}
		
		this.m = [o[0], o[1], o[2]];
	},

	mult: function(x, y) {
		return [x * y[0], x * y[1], x * y[2]];	
	},
	
	sum: function(o, v) {
		o[0] += v[0];
		o[1] += v[1];
		o[2] += v[2];
	}
});
/*
Script: Image.js

Dependencies:
	Canvas.js

Author:
	Olmo Maldonado, <http://olmo-maldonado.com/>
	
Credits:
	Lightly based from Ralph Sommerer's work: <http://blogs.msdn.com/sompost/archive/2006/02/22/536967.aspx>
	Moderately based from excanvas: <http://excanvas.sourceforge.net/>
	Great thanks to Inviz, <http://inviz.ru/>, for his optimizing help.
	
License:
	MIT License, <http://en.wikipedia.org/wiki/MIT_License>
*/

CanvasRenderingContext2D.implement({
	/*
	Property: drawImage
		This method is overloaded with three variants: drawImage(image, dx, dy),
		drawImage(image, dx, dy, dw, dh), and drawImage(image, sx, sy, sw, sh, 
		dx, dy, dw, dh). (Actually it is overloaded with six; each of those three 
		can take either an HTMLImageElement or an HTMLCanvasElement for the image 
		argument.) If not specified, the dw and dh arguments default to the values 
		of sw and sh, interpreted such that one CSS pixel in the image is treated 
		as one unit in the canvas coordinate space. If the sx, sy, sw, and sh 
		arguments are omitted, they default to 0, 0, the image's intrinsic width 
		in image pixels, and the image's intrinsic height in image pixels, 
		respectively.
		
		If the image is of the wrong type, the implementation must raise a 
		TYPE_MISMATCH_ERR exception. If one of the sy, sw, sw, and sh arguments 
		is outside the size of the image, or if one of the dw and dh arguments 
		is negative, the implementation must raise an INDEX_SIZE_ERR  exception.
		
		The specified region of the image specified by the source rectangle 
		(sx, sy, sw, sh) must be painted on the region of the canvas specified 
		by the destination rectangle (dx, dy, dw, dh).
		
		Images are painted without affecting the current path, and are subject to
		transformations, shadow effects, global alpha, clipping paths, and global 
		composition operators.
	*/
	drawImage: function (image, var_args) {
		var args = arguments, 
			length = args.length, 
			off = (length == 9) ? 4 : 0;
			
		if(!((length + '').test(/3|5|9/))) throw 'Wrong number of arguments';
		
		var w0 = image.runtimeStyle.width, 
			h0 = image.runtimeStyle.height;
		image.runtimeStyle.width = 'auto';
		image.runtimeStyle.height = 'auto';
	
		var w = image.width, 
			h = image.height;
		image.runtimeStyle.width = w0;
		image.runtimeStyle.height = h0;
		
		var sx = 0, 
			sy = 0, 
			sw = w, 
			sh = h, 
			dx = args[1 + off], 
			dy = args[2 + off], 
			dw = args[3 + off] || w, 
			dh = args[4 + off] || h;
		
		if (length == 9) {
			sx = args[1];
			sy = args[2];
			sw = args[3];
			sh = args[4];
		}

		var d = this.getCoords(dx, dy),
			vmlStr = 
			'<v:group coordsize="' + this.Z * 10 + ',' + this.Z * 10 + '" ' + 
				'coordorigin="0,0" ' +
				'style="width:10;height:10;position:absolute;';
		
		if (this.m[0][0] != 1 || this.m[0][1]) {
			var max = Math.max(
				this.getCoords(dx + dw, dy), 
				this.getCoords(dx, dy + dh), 
				this.getCoords(dx + dw, dy + dh)
			);
			
			vmlStr += 
				'padding:0;' +
				'padding-right:' + Math.round(Math.max(d.x, max) / this.Z) + 'px;' +
				'padding-bottom:' + Math.round(Math.max(d.y, max) / this.Z) + 'px;' +
				'filter:progid:DXImageTransform.Microsoft.Matrix(' +
					"M11='" + this.m[0][0] + "', M12='" + this.m[1][0] + "', " +
					"M21='" + this.m[0][1] + "', M22='" + this.m[1][1] + "', " +
					"Dx='" + Math.round(d.x / this.Z) + "', Dy='" + Math.round(d.y / this.Z) + "', " +
					"sizingmethod='clip'" +
				');';
		} else {
			vmlStr += 
				'top:' + Math.round(d.y / this.Z) + 'px;' +
				'left:' + Math.round(d.x / this.Z) + 'px;';
		}
		
		this.element.insertAdjacentHTML('BeforeEnd', vmlStr + 
			'"><v:image src="' + image.src + '" ' +
				'style="width:' + this.Z * dw + ';height:' + this.Z * dh + ';" ' +
				'cropleft="' + sx / w + '" ' +
				'croptop="' + sy / h + '" ' +
				'cropright="' + (w - sx - sw) / w + '" ' +
				'cropbottom="' + (h - sy - sh) / h + '" ' +
			'/></v:group>'
		);
		
		this.parent.appendChild(this.fragment);
	},

	drawImageFromRect: Function.empty,
	
	/*
	Property: getImageData
		Method must return an ImageData object representing the underlying 
		pixel data for the area of the canvas denoted by the rectangle which 
		has one corner at the (sx, sy) coordinate, and that has width sw and 
		height sh. Pixels outside the canvas must be returned as transparent 
		black.
	*/
	getImageData: function(sx, sy, sw, sh) {

	},
	
	/*
	Property: putImageData
		Method must take the given ImageData structure, and draw it at the 
		specified location dx,dy in the canvas coordinate space, mapping each 
		pixel represented by the ImageData structure into one device pixel.
	*/
	putImageData: function(image, dx, dy) {
	
	},

	getCoords: function(x, y) {
		var m = this.m;
		return {
			x: this.Z * (x * m[0][0] + y * m[1][0] + m[2][0]) - this.Z2, 
			y: this.Z * (x * m[0][1] + y * m[1][1] + m[2][1]) - this.Z2
		};
	}
	
});
