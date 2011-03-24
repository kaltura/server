draw2d.shape={};

draw2d.shape.Job=function(_4ef2){
	this.childJobs=[];
	this.nodeLevel=0;
	this.parentJobNode=null;
	this.jobX=0;
	this.jobY=0;
	this.portTop=null;
	this.portRight=null;
	this.portBottom=null;
	this.portLeft=null;
	draw2d.Node.call(this);
	this.setDimension(250, 100);
	this.setResizeable(false);
	this.setClassName(_4ef2);
};
draw2d.shape.Job.prototype=new draw2d.Node;
draw2d.shape.Job.prototype.type="shape.job";

draw2d.shape.Job.prototype.setWorkflow=function(_4ef3){
	draw2d.Node.prototype.setWorkflow.call(this,_4ef3);
	if(_4ef3!==null&&this.portTop===null){
		this.recalculateSize();
	}
};
draw2d.shape.Job.prototype.setClassName=function(name){
	this.headerLabel.innerHTML=name;
	this.recalculateSize();
};
draw2d.shape.Job.prototype.addAttribute=function(name,type,_4ef7){
	var row=document.createElement("tr");
	row.style.backgroundColor="transparent";
	this.table.appendChild(row);
	var td=document.createElement("td");
	td.style.whiteSpace="nowrap";
	row.appendChild(td);
	this.disableTextSelection(td);
	if(_4ef7){
		td.innerHTML=name+" : "+type+" = "+_4ef7;
	}else{
		td.innerHTML=name+" : "+type;
	}
	this.recalculateSize();
};
draw2d.shape.Job.prototype.setDimension=function(w,h){
	this.table.style.minHeight="0px";
	this.table.style.minWidth="0px";
	draw2d.Node.prototype.setDimension.call(this,w,h);
	if(this.portTop!==null){
		this.portTop.setPosition(this.width/2,0);
		this.portRight.setPosition(this.width,this.height/2);
		this.portBottom.setPosition(this.width/2,this.height);
		this.portLeft.setPosition(0,this.height/2);
	}
};
draw2d.shape.Job.prototype.createHTMLElement=function(){
	var item=document.createElement("div");
	item.id=this.id;
	item.style.position="absolute";
	item.style.left=this.x+"px";
	item.style.top=this.y+"px";
	item.style.height=this.width+"px";
	item.style.width=this.height+"px";
	item.style.margin="0px";
	item.style.padding="0px";
	item.style.outline="none";
	item.style.border="1px solid black";
	item.style.zIndex=""+draw2d.Figure.ZOrderBaseIndex;
	item.style.backgroundColor="rgb(255,255,206)";
	this.disableTextSelection(item);
	this.table=document.createElement("table");
	this.table.style.width="100%";
	this.table.style.height="100%";
	this.table.style.margin="0px";
	this.table.style.padding="0px";
	item.appendChild(this.table);
	var _4efd=document.createElement("tbody");
	this.table.appendChild(_4efd);
	var _4efe=document.createElement("tr");
	_4efe.style.backgroundColor="transparent";
	_4efd.appendChild(_4efe);
	this.headerLabel=document.createElement("td");
	this.headerLabel.style.align="left";
	this.headerLabel.style.verticalAlign="top";
	this.headerLabel.style.borderBottom="1px solid black";
	this.headerLabel.style.fontWeight="bold";
	this.headerLabel.style.textAlign="center";
	_4efe.appendChild(this.headerLabel);
	this.headerLabel.innerHTML="";
	return item;
};
draw2d.shape.Job.prototype.recalculateSize=function(name){
	this.setDimension(this.getWidth(),this.getHeight());
};
draw2d.shape.Job.prototype.getWidth=function(){
	if(this.workflow===null){
			return 10;
	}
	if(this.table.xgetBoundingClientRect){
		return this.table.getBoundingClientRect().right-this.table.getBoundingClientRect().left;
	}else{
		if(document.getBoxObjectFor){
			return document.getBoxObjectFor(this.table).width;
		}else{
			return this.table.offsetWidth;
		}
	}
};
draw2d.shape.Job.prototype.getHeight=function(){
	if(this.workflow===null){
		return 10;
	}
	if(this.table.xgetBoundingClientRect){
		return this.table.getBoundingClientRect().bottom-this.table.getBoundingClientRect().top;
	}else{
		if(document.getBoxObjectFor){
			return document.getBoxObjectFor(this.table).height;
		}else{
			return this.table.offsetHeight;
		}
	}
};





draw2d.shape.Job.prototype.getNodeLevel=function(_4ef3){
	return this.nodeLevel;
};
draw2d.shape.Job.prototype.setNodeLevel=function(level){
	this.nodeLevel=level;
	for(var i = 0; i < this.childJobs.length; i++)
		this.childJobs[i].setNodeLevel(level + 1);
}
draw2d.shape.Job.prototype.setParentJobNode=function(parentJobNode){
	this.parentJobNode=parentJobNode;
}
draw2d.shape.Job.prototype.addChildJob=function(childJob){
	this.childJobs.push(childJob);
	childJob.setParentJobNode(this);
	childJob.setNodeLevel(this.nodeLevel + 1);
}
draw2d.shape.Job.prototype.getChildrenWidth=function(_4ef3){
	if(!this.childJobs.length)
		return 255;
	
	var childrenWidth = 0;
	for(var i = 0; i < this.childJobs.length; i++)
		childrenWidth += this.childJobs[i].getChildrenWidth();
	return childrenWidth;
}
draw2d.shape.Job.prototype.getJobX=function(_4ef3){
	return this.jobX;
}
draw2d.shape.Job.prototype.setJobX=function(theX){
	this.jobX = theX;
}
draw2d.shape.Job.prototype.setJobY=function(theY){
	this.jobY = theY;
}
draw2d.shape.Job.prototype.updatePosition=function(baseY){
	this.setPosition(this.x, baseY + this.jobY);
};
draw2d.shape.Job.prototype.getJobParentX=function(_4ef3){
	return this.parentJobNode.getJobX();
}
draw2d.shape.Job.prototype.setJobParentX=function(theX){
	this.parentJobNode.setJobX(theX);
}
draw2d.shape.Job.prototype.addConnections=function(workflow){
	if(!this.childJobs.length)
		return;
	
	this.portBottom=new draw2d.Port();
	this.portBottom.setWorkflow(workflow);
	this.addPort(this.portBottom,0,0);
	
	for(var i = 0; i < this.childJobs.length; i++){
		var childJob = this.childJobs[i];
		
		childJob.portTop=new draw2d.Port();
		childJob.portTop.setWorkflow(workflow);
		childJob.addPort(childJob.portTop,0,0);
		
		var con = new draw2d.Connection();
		con.setSourceAnchor(new draw2d.ChopboxConnectionAnchor());
		con.setTargetAnchor(new draw2d.ChopboxConnectionAnchor());
		con.setRouter(new draw2d.NullConnectionRouter());
		con.setSource(this.portBottom);
		con.setTarget(childJob.portTop);
		workflow.addFigure(con);
		
		childJob.addConnections(workflow);
	}
}
