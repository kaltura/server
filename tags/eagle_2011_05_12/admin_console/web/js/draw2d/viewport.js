draw2d.Html = function(htmlContent) {
	this.htmlContent = htmlContent;
	this.bgColor = null;
	this.color = new draw2d.Color(0, 0, 0);
	this.fontSize = 10;
	this.htmlContentNode = null;
	this.align = "center";
	draw2d.Figure.call(this);
};
draw2d.Html.prototype = new draw2d.Figure();
draw2d.Html.prototype.type = "draw2d.Html";
draw2d.Html.prototype.createHTMLElement = function() {
	var item = draw2d.Figure.prototype.createHTMLElement.call(this);

	this.htmlContentNode = document.createElement("div");
	this.htmlContentNode.style.whiteSpace = "nowrap";
	this.htmlContentNode.innerHTML = this.htmlContent;
	item.appendChild(this.htmlContentNode);

	item.style.color = this.color.getHTMLStyle();
	item.style.fontSize = this.fontSize + "pt";
	item.style.width = "auto";
	item.style.height = "auto";
	item.style.paddingLeft = "3px";
	item.style.paddingRight = "3px";
	item.style.textAlign = this.align;
	item.style.MozUserSelect = "none";
	this.disableTextSelection(item);
	if (this.bgColor !== null) {
		item.style.backgroundColor = this.bgColor.getHTMLStyle();
	}
	return item;
};
draw2d.Html.prototype.isResizeable = function() {
	return false;
};
draw2d.Html.prototype.setWordwrap = function(flag) {
	this.html.style.whiteSpace = flag ? "wrap" : "nowrap";
};
draw2d.Html.prototype.setAlign = function(align) {
	this.align = align;
	this.html.style.textAlign = align;
};
draw2d.Html.prototype.setBackgroundColor = function(color) {
	this.bgColor = color;
	if (this.bgColor !== null) {
		this.html.style.backgroundColor = this.bgColor.getHTMLStyle();
	} else {
		this.html.style.backgroundColor = "transparent";
	}
};
draw2d.Html.prototype.setColor = function(color) {
	this.color = color;
	this.html.style.color = this.color.getHTMLStyle();
};
draw2d.Html.prototype.setFontSize = function(size) {
	this.fontSize = size;
	this.html.style.fontSize = this.fontSize + "pt";
};
draw2d.Html.prototype.setDimension = function(w, h) {
};
draw2d.Html.prototype.getWidth = function() {
	if (window.getComputedStyle) {
		return parseInt(getComputedStyle(this.html, "").getPropertyValue(
				"width"));
	}
	return parseInt(this.html.clientWidth);
};
draw2d.Html.prototype.getHeight = function() {
	if (window.getComputedStyle) {
		return parseInt(getComputedStyle(this.html, "").getPropertyValue(
				"height"));
	}
	return parseInt(this.html.clientHeight);
};
draw2d.Html.prototype.getHtmlContent = function() {
	return this.htmlContent;
};
draw2d.Html.prototype.setHtmlContent = function(htmlContent) {
	this.htmlContent = htmlContent;
	this.html.removeChild(this.htmlContentNode);
	this.htmlContentNode = document.createElement("div");
	this.htmlContentNode.style.whiteSpace = "nowrap";
	this.htmlContentNode.innerHTML = htmlContent;
	this.html.appendChild(this.htmlContentNode);
};




draw2d.Diamond = function(width, _5fcd) {
	draw2d.VectorFigure.call(this);
	if (width && _5fcd) {
		this.setDimension(width, _5fcd);
	}
};
draw2d.Diamond.prototype = new draw2d.VectorFigure();
draw2d.Diamond.prototype.paint = function() {
	draw2d.VectorFigure.prototype.paint.call(this);
	var x = new Array(this.getWidth() / 2, this.getWidth(), this.getWidth() / 2, 0);
	var y = new Array(0, this.getHeight() / 2, this.getHeight(), this.getHeight() / 2);
	this.graphics.setStroke(this.stroke);
	if (this.bgColor !== null) {
		this.graphics.setColor(this.bgColor.getHTMLStyle());
		this.graphics.fillPolygon(x, y);
	}
	if (this.lineColor !== null) {
		this.graphics.setColor(this.lineColor.getHTMLStyle());
		this.graphics.drawPolygon(x, y);
	}
	this.graphics.paint();
};



draw2d.ContextmenuConnection = function() {
	draw2d.Connection.call(this);
	this.sourcePort = null;
	this.targetPort = null;
	this.lineSegments = [];
	this.setColor(new draw2d.Color(128, 128, 255));
	this.setLineWidth(1);
	this.setRouter(new draw2d.ManhattanConnectionRouter());
};
draw2d.ContextmenuConnection.prototype = new draw2d.Connection();
draw2d.ContextmenuConnection.prototype.getContextMenu = function() {
	var menu = new draw2d.Menu();
	var oThis = this;
	menu.appendMenuItem(new draw2d.MenuItem("NULL Router", null, function() {
		oThis.setRouter(null);
	}));
	menu.appendMenuItem(new draw2d.MenuItem("Manhatten Router", null,
			function() {
				oThis.setRouter(new draw2d.ManhattanConnectionRouter());
			}));
	menu.appendMenuItem(new draw2d.MenuItem("Bezier Router", null, function() {
		oThis.setRouter(new draw2d.BezierConnectionRouter());
	}));
	menu.appendMenuItem(new draw2d.MenuItem("Fan Router", null, function() {
		oThis.setRouter(new draw2d.FanConnectionRouter());
	}));
	return menu;
};

draw2d.LabelConnection = function(text) {
	draw2d.Connection.call(this);
	var label = new draw2d.Html(text);
	label.setBackgroundColor(new draw2d.Color(255, 255, 153));
	label.setBorder(new draw2d.LineBorder(1));
	label.setWordwrap(false);
	this.addFigure(label, new draw2d.ManhattanMidpointLocator(this));
	this.setRouter(new draw2d.ManhattanConnectionRouter());
};
draw2d.LabelConnection.prototype = new draw2d.Connection();
draw2d.LabelConnection.prototype.type = "draw2d.LabelConnection";

draw2d.MyInputPort = function(_5770) {
	draw2d.InputPort.call(this, _5770);
};
draw2d.MyInputPort.prototype = new draw2d.InputPort();
draw2d.MyInputPort.prototype.type = "MyInputPort";
draw2d.MyInputPort.prototype.onDrop = function(port) {
	if (port.getMaxFanOut && port.getMaxFanOut() <= port.getFanOut()) {
		return;
	}
	if (this.parentNode.id == port.parentNode.id) {
	} else {
		var _5772 = new draw2d.CommandConnect(this.parentNode.workflow, port,
				this);
		_5772.setConnection(new draw2d.ContextmenuConnection());
		this.parentNode.workflow.getCommandStack().execute(_5772);
	}
};

draw2d.MyOutputPort = function(_5932) {
	draw2d.OutputPort.call(this, _5932);
};
draw2d.MyOutputPort.prototype = new draw2d.OutputPort();
draw2d.MyOutputPort.prototype.type = "MyOutputPort";
draw2d.MyOutputPort.prototype.onDrop = function(port) {
	if (this.getMaxFanOut() <= this.getFanOut()) {
		return;
	}
	if (this.parentNode.id == port.parentNode.id) {
	} else {
		var _5934 = new draw2d.CommandConnect(this.parentNode.workflow, this,
				port);
		_5934.setConnection(new draw2d.ContextmenuConnection());
		this.parentNode.workflow.getCommandStack().execute(_5934);
	}
};

draw2d.Statement = function() {
	draw2d.CompartmentFigure.call(this);

	this.setResizeable(false);

	this.inputPort = null;
	this.outputPort = null;
	this.setBackgroundColor(new draw2d.Color(255, 255, 204));
	this.label = new draw2d.Html("");
	this.label.setPosition(5, 5);
	this.label.setCanDrag(false);
	this.label.setResizeable(false);
	this.addChild(this.label);

	workflow.addFigure(this.label);
};
draw2d.Statement.prototype = new draw2d.CompartmentFigure();
draw2d.Statement.prototype.type = "Statement";
draw2d.Statement.prototype.label = null;
draw2d.Statement.prototype.setText = function(text) {
	this.setHtmlContent(text);
}
draw2d.Statement.prototype.setHtmlContent = function(htmlContent) {
	this.label.setHtmlContent(htmlContent);
	this.setDimension(this.label.getWidth() + 10, this.label.getHeight() + 10);
}
draw2d.Statement.prototype.setWorkflow = function(_52e0) {
	draw2d.CompartmentFigure.prototype.setWorkflow.call(this, _52e0);

	if (_52e0 !== null && this.outputPort === null) {

		this.outputPort = new draw2d.MyOutputPort();
		this.outputPort.setWorkflow(_52e0);
		this.outputPort.setMaxFanOut(4);
		this.outputPort.setBackgroundColor(new draw2d.Color(255, 204, 102));
		this.addPort(this.outputPort, this.width / 2, this.height);
	}

	if (_52e0 !== null && this.inputPort === null) {

		this.inputPort = new draw2d.MyInputPort();
		this.inputPort.setWorkflow(_52e0);
		this.inputPort.setBackgroundColor(new draw2d.Color(255, 102, 51));
		this.inputPort.setColor(null);
		this.addPort(this.inputPort, this.width / 2, 0);
	}
};

draw2d.Start = function() {
	draw2d.Circle.call(this, 30);

	this.setResizeable(false);

	this.outputPort = null;
	this.setBackgroundColor(new draw2d.Color(153, 255, 153));
};
draw2d.Start.prototype = new draw2d.Circle();
draw2d.Start.prototype.type = "Start";
draw2d.Start.prototype.setWorkflow = function(_52e0) {
	draw2d.CompartmentFigure.prototype.setWorkflow.call(this, _52e0);

	if (_52e0 !== null && this.outputPort === null) {

		this.outputPort = new draw2d.MyOutputPort();
		this.outputPort.setWorkflow(_52e0);
		this.outputPort.setMaxFanOut(4);
		this.outputPort.setBackgroundColor(new draw2d.Color(255, 204, 102));
		this.addPort(this.outputPort, this.width / 2, this.height);
	}
};

draw2d.Return = function() {
	draw2d.CompartmentFigure.call(this);

	this.html.style.border = "0px";
	this.setResizeable(false);

	this.inputPort = null;

	this.circle = new draw2d.Circle();
	this.circle.setBackgroundColor(new draw2d.Color(102, 204, 102));
	this.circle.setPosition(5, 5);
	this.circle.setCanDrag(false);
	this.circle.setResizeable(false);
	this.addChild(this.circle);
	workflow.addFigure(this.circle);

	this.label = new draw2d.Html("");
	this.label.setPosition(7, 7);
	this.label.setCanDrag(false);
	this.label.setResizeable(false);
	this.addChild(this.label);
	workflow.addFigure(this.label);
};
draw2d.Return.prototype = new draw2d.CompartmentFigure();
draw2d.Return.prototype.type = "Return";
draw2d.Return.prototype.label = null;
draw2d.Return.prototype.circle = null;
draw2d.Return.prototype.setText = function(text) {
	this.setHtmlContent("<b>" + text + "</b>");
}
draw2d.Return.prototype.setHtmlContent = function(htmlContent) {
	this.label.setHtmlContent(htmlContent);
	this.circle.setDimension(this.label.getWidth() + 8, this.label.getHeight() + 8); this.setDimension(this.circle.getWidth() + 10, this.circle.getHeight() + 10);
	this.label.setPosition(7, (this.getHeight() / 2) - (this.label.getHeight() / 2));
}
draw2d.Return.prototype.setWorkflow = function(_52e0) {
	draw2d.CompartmentFigure.prototype.setWorkflow.call(this, _52e0);

	if (_52e0 !== null && this.inputPort === null) {

		this.inputPort = new draw2d.MyInputPort();
		this.inputPort.setWorkflow(_52e0);
		this.inputPort.setBackgroundColor(new draw2d.Color(255, 102, 51));
		this.inputPort.setColor(null);
		this.addPort(this.inputPort, this.width / 2, 0);
	}
};

draw2d.Throw = function() {
	draw2d.CompartmentFigure.call(this);

	this.html.style.border = "0px";
	this.setResizeable(false);

	this.inputPort = null;

	this.circle = new draw2d.Circle();
	this.circle.setBackgroundColor(new draw2d.Color(255, 102, 51));
	this.circle.setPosition(5, 5);
	this.circle.setCanDrag(false);
	this.circle.setResizeable(false);
	this.addChild(this.circle);
	workflow.addFigure(this.circle);

	this.label = new draw2d.Html("");
	this.label.setPosition(7, 7);
	this.label.setCanDrag(false);
	this.label.setResizeable(false);
	this.addChild(this.label);
	workflow.addFigure(this.label);
};
draw2d.Throw.prototype = new draw2d.CompartmentFigure();
draw2d.Throw.prototype.type = "Throw";
draw2d.Throw.prototype.label = null;
draw2d.Throw.prototype.circle = null;
draw2d.Throw.prototype.setText = function(text) {
	this.setHtmlContent("<b>" + text + "</b>");
}
draw2d.Throw.prototype.setHtmlContent = function(htmlContent) {
	this.label.setHtmlContent(htmlContent);
	this.circle.setDimension(this.label.getWidth() + 8,
			this.label.getHeight() + 8);
	this
			.setDimension(this.circle.getWidth() + 10,
					this.circle.getHeight() + 10);
	this.label.setPosition(7, (this.getHeight() / 2)
			- (this.label.getHeight() / 2));
}
draw2d.Throw.prototype.setWorkflow = function(_52e0) {
	draw2d.CompartmentFigure.prototype.setWorkflow.call(this, _52e0);

	if (_52e0 !== null && this.inputPort === null) {

		this.inputPort = new draw2d.MyInputPort();
		this.inputPort.setWorkflow(_52e0);
		this.inputPort.setBackgroundColor(new draw2d.Color(255, 102, 51));
		this.inputPort.setColor(null);
		this.addPort(this.inputPort, this.width / 2, 0);
	}
};

draw2d.Loop = function() {
	draw2d.CompartmentFigure.call(this);

	this.setResizeable(false);

	this.inputPort = null;
	this.endInputPort = null;
	this.outputPort = null;
	this.falseOutputPort = null;
	this.setBackgroundColor(new draw2d.Color(204, 204, 153));
	this.label = new draw2d.Html("");
	this.label.setPosition(5, 5);
	this.label.setCanDrag(false);
	this.label.setResizeable(false);
	this.addChild(this.label);

	workflow.addFigure(this.label);
};
draw2d.Loop.prototype = new draw2d.CompartmentFigure();
draw2d.Loop.prototype.type = "Loop";
draw2d.Loop.prototype.label = null;
draw2d.Loop.prototype.setText = function(text) {
	this.setHtmlContent(text);
}
draw2d.Loop.prototype.setHtmlContent = function(htmlContent) {
	this.label.setHtmlContent(htmlContent);
	this.setDimension(this.label.getWidth() + 10, this.label.getHeight() + 10);
}
draw2d.Loop.prototype.setWorkflow = function(_52e0) {
	draw2d.CompartmentFigure.prototype.setWorkflow.call(this, _52e0);

	if (_52e0 !== null && this.outputPort === null) {

		this.outputPort = new draw2d.MyOutputPort();
		this.outputPort.setWorkflow(_52e0);
		this.outputPort.setMaxFanOut(4);
		this.outputPort.setBackgroundColor(new draw2d.Color(255, 204, 102));
		this.addPort(this.outputPort, this.width, this.height / 2);

		this.falseOutputPort = new draw2d.MyOutputPort();
		this.falseOutputPort.setWorkflow(_52e0);
		this.falseOutputPort.setMaxFanOut(4);
		this.falseOutputPort.setBackgroundColor(new draw2d.Color(255, 204, 102));
		this.addPort(this.falseOutputPort, this.width / 2, this.height);
	}

	if (_52e0 !== null && this.inputPort === null) {

		this.inputPort = new draw2d.MyInputPort();
		this.inputPort.setWorkflow(_52e0);
		this.inputPort.setBackgroundColor(new draw2d.Color(255, 102, 51));
		this.inputPort.setColor(null);
		this.addPort(this.inputPort, this.width / 2, 0);

		this.endInputPort = new draw2d.MyInputPort();
		this.endInputPort.setWorkflow(_52e0);
		this.endInputPort.setBackgroundColor(new draw2d.Color(255, 102, 51));
		this.endInputPort.setColor(null);
		this.addPort(this.endInputPort, 0, this.height / 2);
	}
};

draw2d.IfElse = function() {
	draw2d.CompartmentFigure.call(this);

	this.html.style.border = "0px";
	this.setResizeable(false);

	this.inputPort = null;
	this.truePort = null;

	this.diamond = new draw2d.Diamond(5, 5);
	this.diamond.setBackgroundColor(new draw2d.Color(255, 204, 153));
	this.diamond.setPosition(0, 0);
	this.diamond.setCanDrag(false);
	this.diamond.setResizeable(false);
	this.addChild(this.diamond);
	workflow.addFigure(this.diamond);
	
	this.label = new draw2d.Html("");
	this.label.setPosition(5, 5);
	this.label.setCanDrag(false);
	this.label.setResizeable(false);
	this.addChild(this.label);

	workflow.addFigure(this.label);
};
draw2d.IfElse.prototype = new draw2d.CompartmentFigure();
draw2d.IfElse.prototype.type = "IfElse";
draw2d.IfElse.prototype.label = null;
draw2d.IfElse.prototype.diamond = null;
draw2d.IfElse.prototype.setText = function(text) {
	this.setHtmlContent(text);
}
draw2d.IfElse.prototype.setHtmlContent = function(htmlContent) {
	this.label.setHtmlContent(htmlContent);
	this.setDimension(this.label.getWidth() + 50, this.label.getHeight() + 50);
	this.diamond.setDimension(this.getWidth(), this.getHeight());
	this.label.setPosition(25, (this.getHeight() / 2) - (this.label.getHeight() / 2));
}
draw2d.IfElse.prototype.setWorkflow = function(_52e0) {
	draw2d.CompartmentFigure.prototype.setWorkflow.call(this, _52e0);

	if (_52e0 !== null && this.truePort === null) {

		this.truePort = new draw2d.MyOutputPort();
		this.truePort.setWorkflow(_52e0);
		this.truePort.setMaxFanOut(4);
		this.truePort.setBackgroundColor(new draw2d.Color(255, 204, 102));
		this.addPort(this.truePort, 0, this.height / 2);

		this.falsePort = new draw2d.MyOutputPort();
		this.falsePort.setWorkflow(_52e0);
		this.falsePort.setMaxFanOut(4);
		this.falsePort.setBackgroundColor(new draw2d.Color(255, 204, 102));
		this.addPort(this.falsePort, this.width, this.height / 2);
	}

	if (_52e0 !== null && this.inputPort === null) {

		this.inputPort = new draw2d.MyInputPort();
		this.inputPort.setWorkflow(_52e0);
		this.inputPort.setBackgroundColor(new draw2d.Color(255, 102, 51));
		this.inputPort.setColor(null);
		this.addPort(this.inputPort, this.width / 2, 0);
	}
};

draw2d.TryCatch = function() {
	draw2d.CompartmentFigure.call(this);

	this.setResizeable(false);

	this.inputPort = null;
	this.truePort = null;
	this.setBackgroundColor(new draw2d.Color(255, 255, 102));
	this.label = new draw2d.Html("");
	this.label.setPosition(5, 5);
	this.label.setCanDrag(false);
	this.label.setResizeable(false);
	this.addChild(this.label);

	workflow.addFigure(this.label);
};
draw2d.TryCatch.prototype = new draw2d.CompartmentFigure();
draw2d.TryCatch.prototype.type = "TryCatch";
draw2d.TryCatch.prototype.label = null;
draw2d.TryCatch.prototype.setText = function(text) {
	this.setHtmlContent(text);
}
draw2d.TryCatch.prototype.setHtmlContent = function(htmlContent) {
	this.label.setHtmlContent(htmlContent);
	this.setDimension(this.label.getWidth() + 10, this.label.getHeight() + 10);
}
draw2d.TryCatch.prototype.setWorkflow = function(_52e0) {
	draw2d.CompartmentFigure.prototype.setWorkflow.call(this, _52e0);

	if (_52e0 !== null && this.truePort === null) {

		this.truePort = new draw2d.MyOutputPort();
		this.truePort.setWorkflow(_52e0);
		this.truePort.setMaxFanOut(4);
		this.truePort.setBackgroundColor(new draw2d.Color(255, 204, 102));
		this.addPort(this.truePort, 0, this.height / 2);

		this.falsePort = new draw2d.MyOutputPort();
		this.falsePort.setWorkflow(_52e0);
		this.falsePort.setMaxFanOut(4);
		this.falsePort.setBackgroundColor(new draw2d.Color(255, 204, 102));
		this.addPort(this.falsePort, this.width, this.height / 2);
	}

	if (_52e0 !== null && this.inputPort === null) {

		this.inputPort = new draw2d.MyInputPort();
		this.inputPort.setWorkflow(_52e0);
		this.inputPort.setBackgroundColor(new draw2d.Color(255, 102, 51));
		this.inputPort.setColor(null);
		this.addPort(this.inputPort, this.width / 2, 0);
	}
};

