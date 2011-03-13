
/**This notice must be untouched at all times.
This is the COMPRESSED version of the Draw2D Library
WebSite: http://www.draw2d.org
Copyright: 2006 Andreas Herz. All rights reserved.
Created: 5.11.2006 by Andreas Herz (Web: http://www.freegroup.de )
LICENSE: LGPL
**/
var draw2d=new Object();
var _errorStack_=[];
function pushErrorStack(e,_56b5){
_errorStack_.push(_56b5+"\n");
throw e;
}
draw2d.AbstractEvent=function(){
this.type=null;
this.target=null;
this.relatedTarget=null;
this.cancelable=false;
this.timeStamp=null;
this.returnValue=true;
};
draw2d.AbstractEvent.prototype.initEvent=function(sType,_56b7){
this.type=sType;
this.cancelable=_56b7;
this.timeStamp=(new Date()).getTime();
};
draw2d.AbstractEvent.prototype.preventDefault=function(){
if(this.cancelable){
this.returnValue=false;
}
};
draw2d.AbstractEvent.fireDOMEvent=function(_56b8,_56b9){
if(document.createEvent){
var evt=document.createEvent("Events");
evt.initEvent(_56b8,true,true);
_56b9.dispatchEvent(evt);
}else{
if(document.createEventObject){
var evt=document.createEventObject();
_56b9.fireEvent("on"+_56b8,evt);
}
}
};
draw2d.EventTarget=function(){
this.eventhandlers={};
};
draw2d.EventTarget.prototype.addEventListener=function(sType,_56bc){
if(typeof this.eventhandlers[sType]=="undefined"){
this.eventhandlers[sType]=[];
}
this.eventhandlers[sType][this.eventhandlers[sType].length]=_56bc;
};
draw2d.EventTarget.prototype.dispatchEvent=function(_56bd){
_56bd.target=this;
if(typeof this.eventhandlers[_56bd.type]!="undefined"){
for(var i=0;i<this.eventhandlers[_56bd.type].length;i++){
this.eventhandlers[_56bd.type][i](_56bd);
}
}
return _56bd.returnValue;
};
draw2d.EventTarget.prototype.removeEventListener=function(sType,_56c0){
if(typeof this.eventhandlers[sType]!="undefined"){
var _56c1=[];
for(var i=0;i<this.eventhandlers[sType].length;i++){
if(this.eventhandlers[sType][i]!=_56c0){
_56c1[_56c1.length]=this.eventhandlers[sType][i];
}
}
this.eventhandlers[sType]=_56c1;
}
};
String.prototype.trim=function(){
return (this.replace(new RegExp("^([\\s]+)|([\\s]+)$","gm"),""));
};
String.prototype.lefttrim=function(){
return (this.replace(new RegExp("^[\\s]+","gm"),""));
};
String.prototype.righttrim=function(){
return (this.replace(new RegExp("[\\s]+$","gm"),""));
};
String.prototype.between=function(left,right,_558d){
if(!_558d){
_558d=0;
}
var li=this.indexOf(left,_558d);
if(li==-1){
return null;
}
var ri=this.indexOf(right,li);
if(ri==-1){
return null;
}
return this.substring(li+left.length,ri);
};
draw2d.UUID=function(){
};
draw2d.UUID.prototype.type="draw2d.UUID";
draw2d.UUID.create=function(){
var _4a16=function(){
return (((1+Math.random())*65536)|0).toString(16).substring(1);
};
return (_4a16()+_4a16()+"-"+_4a16()+"-"+_4a16()+"-"+_4a16()+"-"+_4a16()+_4a16()+_4a16());
};
draw2d.ArrayList=function(){
this.increment=10;
this.size=0;
this.data=new Array(this.increment);
};
draw2d.ArrayList.EMPTY_LIST=new draw2d.ArrayList();
draw2d.ArrayList.prototype.type="draw2d.ArrayList";
draw2d.ArrayList.prototype.reverse=function(){
var _4b21=new Array(this.size);
for(var i=0;i<this.size;i++){
_4b21[i]=this.data[this.size-i-1];
}
this.data=_4b21;
};
draw2d.ArrayList.prototype.getCapacity=function(){
return this.data.length;
};
draw2d.ArrayList.prototype.getSize=function(){
return this.size;
};
draw2d.ArrayList.prototype.isEmpty=function(){
return this.getSize()===0;
};
draw2d.ArrayList.prototype.getLastElement=function(){
if(this.data[this.getSize()-1]!==null){
return this.data[this.getSize()-1];
}
};
draw2d.ArrayList.prototype.getFirstElement=function(){
if(this.data[0]!==null&&this.data[0]!==undefined){
return this.data[0];
}
return null;
};
draw2d.ArrayList.prototype.get=function(i){
return this.data[i];
};
draw2d.ArrayList.prototype.add=function(obj){
if(this.getSize()==this.data.length){
this.resize();
}
this.data[this.size++]=obj;
};
draw2d.ArrayList.prototype.addAll=function(obj){
for(var i=0;i<obj.getSize();i++){
this.add(obj.get(i));
}
};
draw2d.ArrayList.prototype.remove=function(obj){
var index=this.indexOf(obj);
if(index>=0){
return this.removeElementAt(index);
}
return null;
};
draw2d.ArrayList.prototype.insertElementAt=function(obj,index){
if(this.size==this.capacity){
this.resize();
}
for(var i=this.getSize();i>index;i--){
this.data[i]=this.data[i-1];
}
this.data[index]=obj;
this.size++;
};
draw2d.ArrayList.prototype.removeElementAt=function(index){
var _4b2d=this.data[index];
for(var i=index;i<(this.getSize()-1);i++){
this.data[i]=this.data[i+1];
}
this.data[this.getSize()-1]=null;
this.size--;
return _4b2d;
};
draw2d.ArrayList.prototype.removeAllElements=function(){
this.size=0;
for(var i=0;i<this.data.length;i++){
this.data[i]=null;
}
};
draw2d.ArrayList.prototype.indexOf=function(obj){
for(var i=0;i<this.getSize();i++){
if(this.data[i]==obj){
return i;
}
}
return -1;
};
draw2d.ArrayList.prototype.contains=function(obj){
for(var i=0;i<this.getSize();i++){
if(this.data[i]==obj){
return true;
}
}
return false;
};
draw2d.ArrayList.prototype.resize=function(){
newData=new Array(this.data.length+this.increment);
for(var i=0;i<this.data.length;i++){
newData[i]=this.data[i];
}
this.data=newData;
};
draw2d.ArrayList.prototype.trimToSize=function(){
if(this.data.length==this.size){
return;
}
var temp=new Array(this.getSize());
for(var i=0;i<this.getSize();i++){
temp[i]=this.data[i];
}
this.size=temp.length;
this.data=temp;
};
draw2d.ArrayList.prototype.sort=function(f){
var i,j;
var _4b39;
var _4b3a;
var _4b3b;
var _4b3c;
for(i=1;i<this.getSize();i++){
_4b3a=this.data[i];
_4b39=_4b3a[f];
j=i-1;
_4b3b=this.data[j];
_4b3c=_4b3b[f];
while(j>=0&&_4b3c>_4b39){
this.data[j+1]=this.data[j];
j--;
if(j>=0){
_4b3b=this.data[j];
_4b3c=_4b3b[f];
}
}
this.data[j+1]=_4b3a;
}
};
draw2d.ArrayList.prototype.clone=function(){
var _4b3d=new draw2d.ArrayList(this.size);
for(var i=0;i<this.size;i++){
_4b3d.add(this.data[i]);
}
return _4b3d;
};
draw2d.ArrayList.prototype.overwriteElementAt=function(obj,index){
this.data[index]=obj;
};
draw2d.ArrayList.prototype.getPersistentAttributes=function(){
return {data:this.data,increment:this.increment,size:this.getSize()};
};
function trace(_5cb9){
var _5cba=openwindow("about:blank",700,400);
_5cba.document.writeln("<pre>"+_5cb9+"</pre>");
}
function openwindow(url,width,_5cbd){
var left=(screen.width-width)/2;
var top=(screen.height-_5cbd)/2;
property="left="+left+", top="+top+", toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,alwaysRaised,width="+width+",height="+_5cbd;
return window.open(url,"_blank",property);
}
function dumpObject(obj){
trace("----------------------------------------------------------------------------");
trace("- Object dump");
trace("----------------------------------------------------------------------------");
for(var i in obj){
try{
if(typeof obj[i]!="function"){
trace(i+" --&gt; "+obj[i]);
}
}
catch(e){
}
}
for(var i in obj){
try{
if(typeof obj[i]=="function"){
trace(i+" --&gt; "+obj[i]);
}
}
catch(e){
}
}
trace("----------------------------------------------------------------------------");
}
draw2d.Drag=function(){
};
draw2d.Drag.current=null;
draw2d.Drag.currentTarget=null;
draw2d.Drag.currentHover=null;
draw2d.Drag.currentCompartment=null;
draw2d.Drag.dragging=false;
draw2d.Drag.isDragging=function(){
return this.dragging;
};
draw2d.Drag.setCurrent=function(_6052){
this.current=_6052;
this.dragging=true;
};
draw2d.Drag.getCurrent=function(){
return this.current;
};
draw2d.Drag.clearCurrent=function(){
this.current=null;
this.dragging=false;
this.currentTarget=null;
};
draw2d.Draggable=function(_6053,_6054){
this.id=draw2d.UUID.create();
this.node=null;
draw2d.EventTarget.call(this);
this.construct(_6053,_6054);
this.diffX=0;
this.diffY=0;
this.targets=new draw2d.ArrayList();
};
draw2d.Draggable.prototype=new draw2d.EventTarget();
draw2d.Draggable.prototype.construct=function(_6055){
if(_6055===null||_6055===undefined){
return;
}
this.element=_6055;
var oThis=this;
var _6057=function(){
var _6058=new draw2d.DragDropEvent();
_6058.initDragDropEvent("dblclick",true);
oThis.dispatchEvent(_6058);
var _6059=arguments[0]||window.event;
_6059.cancelBubble=true;
_6059.returnValue=false;
};
var _605a=function(){
var _605b=arguments[0]||window.event;
var _605c=new draw2d.DragDropEvent();
if(oThis.node!==null){
var _605d=oThis.node.getWorkflow().getAbsoluteX();
var _605e=oThis.node.getWorkflow().getAbsoluteY();
var _605f=oThis.node.getWorkflow().getScrollLeft();
var _6060=oThis.node.getWorkflow().getScrollTop();
_605c.x=_605b.clientX-oThis.element.offsetLeft+_605f-_605d;
_605c.y=_605b.clientY-oThis.element.offsetTop+_6060-_605e;
}
if(_605b.button===2){
_605c.initDragDropEvent("contextmenu",true);
oThis.dispatchEvent(_605c);
}else{
_605c.initDragDropEvent("dragstart",true);
if(oThis.dispatchEvent(_605c)){
oThis.diffX=_605b.clientX-oThis.element.offsetLeft;
oThis.diffY=_605b.clientY-oThis.element.offsetTop;
draw2d.Drag.setCurrent(oThis);
if(oThis.isAttached==true){
oThis.detachEventHandlers();
}
oThis.attachEventHandlers();
}
}
_605b.cancelBubble=true;
_605b.returnValue=false;
};
var _6061=function(){
if(draw2d.Drag.getCurrent()===null){
var _6062=arguments[0]||window.event;
if(draw2d.Drag.currentHover!==null&&oThis!==draw2d.Drag.currentHover){
var _6063=new draw2d.DragDropEvent();
_6063.initDragDropEvent("mouseleave",false,oThis);
draw2d.Drag.currentHover.dispatchEvent(_6063);
}
if(oThis!==null&&oThis!==draw2d.Drag.currentHover){
var _6063=new draw2d.DragDropEvent();
_6063.initDragDropEvent("mouseenter",false,oThis);
oThis.dispatchEvent(_6063);
}
draw2d.Drag.currentHover=oThis;
}else{
}
};
if(this.element.addEventListener){
this.element.addEventListener("mousemove",_6061,false);
this.element.addEventListener("mousedown",_605a,false);
this.element.addEventListener("dblclick",_6057,false);
}else{
if(this.element.attachEvent){
this.element.attachEvent("onmousemove",_6061);
this.element.attachEvent("onmousedown",_605a);
this.element.attachEvent("ondblclick",_6057);
}else{
throw "Drag not supported in this browser.";
}
}
};
draw2d.Draggable.prototype.onDrop=function(_6064,_6065){
};
draw2d.Draggable.prototype.attachEventHandlers=function(){
var oThis=this;
oThis.isAttached=true;
this.tempMouseMove=function(){
var _6067=arguments[0]||window.event;
var _6068=new draw2d.Point(_6067.clientX-oThis.diffX,_6067.clientY-oThis.diffY);
if(oThis.node!==null&&oThis.node.getCanSnapToHelper()){
_6068=oThis.node.getWorkflow().snapToHelper(oThis.node,_6068);
}
oThis.element.style.left=_6068.x+"px";
oThis.element.style.top=_6068.y+"px";
if(oThis.node!==null){
var _6069=oThis.node.getWorkflow().getScrollLeft();
var _606a=oThis.node.getWorkflow().getScrollTop();
var _606b=oThis.node.getWorkflow().getAbsoluteX();
var _606c=oThis.node.getWorkflow().getAbsoluteY();
var _606d=oThis.getDropTarget(_6067.clientX+_6069-_606b,_6067.clientY+_606a-_606c);
var _606e=oThis.getCompartment(_6067.clientX+_6069-_606b,_6067.clientY+_606a-_606c);
if(draw2d.Drag.currentTarget!==null&&_606d!=draw2d.Drag.currentTarget){
var _606f=new draw2d.DragDropEvent();
_606f.initDragDropEvent("dragleave",false,oThis);
draw2d.Drag.currentTarget.dispatchEvent(_606f);
}
if(_606d!==null&&_606d!==draw2d.Drag.currentTarget){
var _606f=new draw2d.DragDropEvent();
_606f.initDragDropEvent("dragenter",false,oThis);
_606d.dispatchEvent(_606f);
}
draw2d.Drag.currentTarget=_606d;
if(draw2d.Drag.currentCompartment!==null&&_606e!==draw2d.Drag.currentCompartment){
var _606f=new draw2d.DragDropEvent();
_606f.initDragDropEvent("figureleave",false,oThis);
draw2d.Drag.currentCompartment.dispatchEvent(_606f);
}
if(_606e!==null&&_606e.node!=oThis.node&&_606e!==draw2d.Drag.currentCompartment){
var _606f=new draw2d.DragDropEvent();
_606f.initDragDropEvent("figureenter",false,oThis);
_606e.dispatchEvent(_606f);
}
draw2d.Drag.currentCompartment=_606e;
}
var _6070=new draw2d.DragDropEvent();
_6070.initDragDropEvent("drag",false);
oThis.dispatchEvent(_6070);
};
oThis.tempMouseUp=function(){
oThis.detachEventHandlers();
var _6071=arguments[0]||window.event;
if(oThis.node!==null){
var _6072=oThis.node.getWorkflow().getScrollLeft();
var _6073=oThis.node.getWorkflow().getScrollTop();
var _6074=oThis.node.getWorkflow().getAbsoluteX();
var _6075=oThis.node.getWorkflow().getAbsoluteY();
var _6076=oThis.getDropTarget(_6071.clientX+_6072-_6074,_6071.clientY+_6073-_6075);
var _6077=oThis.getCompartment(_6071.clientX+_6072-_6074,_6071.clientY+_6073-_6075);
if(_6076!==null){
var _6078=new draw2d.DragDropEvent();
_6078.initDragDropEvent("drop",false,oThis);
_6076.dispatchEvent(_6078);
}
if(_6077!==null&&_6077.node!==oThis.node){
var _6078=new draw2d.DragDropEvent();
_6078.initDragDropEvent("figuredrop",false,oThis);
_6077.dispatchEvent(_6078);
}
if(draw2d.Drag.currentTarget!==null){
var _6078=new draw2d.DragDropEvent();
_6078.initDragDropEvent("dragleave",false,oThis);
draw2d.Drag.currentTarget.dispatchEvent(_6078);
draw2d.Drag.currentTarget=null;
}
}
var _6079=new draw2d.DragDropEvent();
_6079.initDragDropEvent("dragend",false);
oThis.dispatchEvent(_6079);
oThis.onDrop(_6071.clientX,_6071.clientY);
draw2d.Drag.currentCompartment=null;
draw2d.Drag.clearCurrent();
};
if(document.body.addEventListener){
document.body.addEventListener("mousemove",this.tempMouseMove,false);
document.body.addEventListener("mouseup",this.tempMouseUp,false);
}else{
if(document.body.attachEvent){
document.body.attachEvent("onmousemove",this.tempMouseMove);
document.body.attachEvent("onmouseup",this.tempMouseUp);
}else{
throw new Error("Drag doesn't support this browser.");
}
}
};
draw2d.Draggable.prototype.detachEventHandlers=function(){
this.isAttached=false;
if(document.body.removeEventListener){
document.body.removeEventListener("mousemove",this.tempMouseMove,false);
document.body.removeEventListener("mouseup",this.tempMouseUp,false);
}else{
if(document.body.detachEvent){
document.body.detachEvent("onmousemove",this.tempMouseMove);
document.body.detachEvent("onmouseup",this.tempMouseUp);
}else{
throw new Error("Drag doesn't support this browser.");
}
}
};
draw2d.Draggable.prototype.getDropTarget=function(x,y){
for(var i=0;i<this.targets.getSize();i++){
var _607d=this.targets.get(i);
if(_607d.node.isOver(x,y)&&_607d.node!==this.node){
return _607d;
}
}
return null;
};
draw2d.Draggable.prototype.getCompartment=function(x,y){
var _6080=null;
for(var i=0;i<this.node.getWorkflow().compartments.getSize();i++){
var _6082=this.node.getWorkflow().compartments.get(i);
if(_6082.isOver(x,y)&&_6082!==this.node){
if(_6080===null){
_6080=_6082;
}else{
if(_6080.getZOrder()<_6082.getZOrder()){
_6080=_6082;
}
}
}
}
return _6080===null?null:_6080.dropable;
};
draw2d.Draggable.prototype.getLeft=function(){
return this.element.offsetLeft;
};
draw2d.Draggable.prototype.getTop=function(){
return this.element.offsetTop;
};
draw2d.DragDropEvent=function(){
draw2d.AbstractEvent.call(this);
};
draw2d.DragDropEvent.prototype=new draw2d.AbstractEvent();
draw2d.DragDropEvent.prototype.initDragDropEvent=function(sType,_6084,_6085){
this.initEvent(sType,_6084);
this.relatedTarget=_6085;
};
draw2d.DropTarget=function(_6086){
draw2d.EventTarget.call(this);
this.construct(_6086);
};
draw2d.DropTarget.prototype=new draw2d.EventTarget();
draw2d.DropTarget.prototype.construct=function(_6087){
this.element=_6087;
};
draw2d.DropTarget.prototype.getLeft=function(){
var el=this.element;
var ol=el.offsetLeft;
while((el=el.offsetParent)!==null){
ol+=el.offsetLeft;
}
return ol;
};
draw2d.DropTarget.prototype.getTop=function(){
var el=this.element;
var ot=el.offsetTop;
while((el=el.offsetParent)!==null){
ot+=el.offsetTop;
}
return ot;
};
draw2d.DropTarget.prototype.getHeight=function(){
return this.element.offsetHeight;
};
draw2d.DropTarget.prototype.getWidth=function(){
return this.element.offsetWidth;
};
draw2d.PositionConstants=function(){
};
draw2d.PositionConstants.NORTH=1;
draw2d.PositionConstants.SOUTH=4;
draw2d.PositionConstants.WEST=8;
draw2d.PositionConstants.EAST=16;
draw2d.Color=function(red,green,blue){
if(typeof green=="undefined"){
var rgb=this.hex2rgb(red);
this.red=rgb[0];
this.green=rgb[1];
this.blue=rgb[2];
}else{
this.red=red;
this.green=green;
this.blue=blue;
}
};
draw2d.Color.prototype.type="draw2d.Color";
draw2d.Color.prototype.getHTMLStyle=function(){
return "rgb("+this.red+","+this.green+","+this.blue+")";
};
draw2d.Color.prototype.getRed=function(){
return this.red;
};
draw2d.Color.prototype.getGreen=function(){
return this.green;
};
draw2d.Color.prototype.getBlue=function(){
return this.blue;
};
draw2d.Color.prototype.getIdealTextColor=function(){
var _5065=105;
var _5066=(this.red*0.299)+(this.green*0.587)+(this.blue*0.114);
return (255-_5066<_5065)?new draw2d.Color(0,0,0):new draw2d.Color(255,255,255);
};
draw2d.Color.prototype.hex2rgb=function(_5067){
_5067=_5067.replace("#","");
return ({0:parseInt(_5067.substr(0,2),16),1:parseInt(_5067.substr(2,2),16),2:parseInt(_5067.substr(4,2),16)});
};
draw2d.Color.prototype.hex=function(){
return (this.int2hex(this.red)+this.int2hex(this.green)+this.int2hex(this.blue));
};
draw2d.Color.prototype.int2hex=function(v){
v=Math.round(Math.min(Math.max(0,v),255));
return ("0123456789ABCDEF".charAt((v-v%16)/16)+"0123456789ABCDEF".charAt(v%16));
};
draw2d.Color.prototype.darker=function(_5069){
var red=parseInt(Math.round(this.getRed()*(1-_5069)));
var green=parseInt(Math.round(this.getGreen()*(1-_5069)));
var blue=parseInt(Math.round(this.getBlue()*(1-_5069)));
if(red<0){
red=0;
}else{
if(red>255){
red=255;
}
}
if(green<0){
green=0;
}else{
if(green>255){
green=255;
}
}
if(blue<0){
blue=0;
}else{
if(blue>255){
blue=255;
}
}
return new draw2d.Color(red,green,blue);
};
draw2d.Color.prototype.lighter=function(_506d){
var red=parseInt(Math.round(this.getRed()*(1+_506d)));
var green=parseInt(Math.round(this.getGreen()*(1+_506d)));
var blue=parseInt(Math.round(this.getBlue()*(1+_506d)));
if(red<0){
red=0;
}else{
if(red>255){
red=255;
}
}
if(green<0){
green=0;
}else{
if(green>255){
green=255;
}
}
if(blue<0){
blue=0;
}else{
if(blue>255){
blue=255;
}
}
return new draw2d.Color(red,green,blue);
};
draw2d.Point=function(x,y){
this.x=x;
this.y=y;
};
draw2d.Point.prototype.type="draw2d.Point";
draw2d.Point.prototype.getX=function(){
return this.x;
};
draw2d.Point.prototype.getY=function(){
return this.y;
};
draw2d.Point.prototype.getPosition=function(p){
var dx=p.x-this.x;
var dy=p.y-this.y;
if(Math.abs(dx)>Math.abs(dy)){
if(dx<0){
return draw2d.PositionConstants.WEST;
}
return draw2d.PositionConstants.EAST;
}
if(dy<0){
return draw2d.PositionConstants.NORTH;
}
return draw2d.PositionConstants.SOUTH;
};
draw2d.Point.prototype.equals=function(o){
return this.x==o.x&&this.y==o.y;
};
draw2d.Point.prototype.getDistance=function(other){
return Math.sqrt((this.x-other.x)*(this.x-other.x)+(this.y-other.y)*(this.y-other.y));
};
draw2d.Point.prototype.getTranslated=function(other){
return new draw2d.Point(this.x+other.x,this.y+other.y);
};
draw2d.Point.prototype.getPersistentAttributes=function(){
return {x:this.x,y:this.y};
};
draw2d.Dimension=function(x,y,w,h){
draw2d.Point.call(this,x,y);
this.w=w;
this.h=h;
};
draw2d.Dimension.prototype=new draw2d.Point();
draw2d.Dimension.prototype.type="draw2d.Dimension";
draw2d.Dimension.prototype.translate=function(dx,dy){
this.x+=dx;
this.y+=dy;
return this;
};
draw2d.Dimension.prototype.resize=function(dw,dh){
this.w+=dw;
this.h+=dh;
return this;
};
draw2d.Dimension.prototype.setBounds=function(rect){
this.x=rect.x;
this.y=rect.y;
this.w=rect.w;
this.h=rect.h;
return this;
};
draw2d.Dimension.prototype.isEmpty=function(){
return this.w<=0||this.h<=0;
};
draw2d.Dimension.prototype.getWidth=function(){
return this.w;
};
draw2d.Dimension.prototype.getHeight=function(){
return this.h;
};
draw2d.Dimension.prototype.getRight=function(){
return this.x+this.w;
};
draw2d.Dimension.prototype.getBottom=function(){
return this.y+this.h;
};
draw2d.Dimension.prototype.getTopLeft=function(){
return new draw2d.Point(this.x,this.y);
};
draw2d.Dimension.prototype.getCenter=function(){
return new draw2d.Point(this.x+this.w/2,this.y+this.h/2);
};
draw2d.Dimension.prototype.getBottomRight=function(){
return new draw2d.Point(this.x+this.w,this.y+this.h);
};
draw2d.Dimension.prototype.equals=function(o){
return this.x==o.x&&this.y==o.y&&this.w==o.w&&this.h==o.h;
};
draw2d.SnapToHelper=function(_5935){
this.workflow=_5935;
};
draw2d.SnapToHelper.NORTH=1;
draw2d.SnapToHelper.SOUTH=4;
draw2d.SnapToHelper.WEST=8;
draw2d.SnapToHelper.EAST=16;
draw2d.SnapToHelper.CENTER=32;
draw2d.SnapToHelper.NORTH_EAST=draw2d.SnapToHelper.NORTH|draw2d.SnapToHelper.EAST;
draw2d.SnapToHelper.NORTH_WEST=draw2d.SnapToHelper.NORTH|draw2d.SnapToHelper.WEST;
draw2d.SnapToHelper.SOUTH_EAST=draw2d.SnapToHelper.SOUTH|draw2d.SnapToHelper.EAST;
draw2d.SnapToHelper.SOUTH_WEST=draw2d.SnapToHelper.SOUTH|draw2d.SnapToHelper.WEST;
draw2d.SnapToHelper.NORTH_SOUTH=draw2d.SnapToHelper.NORTH|draw2d.SnapToHelper.SOUTH;
draw2d.SnapToHelper.EAST_WEST=draw2d.SnapToHelper.EAST|draw2d.SnapToHelper.WEST;
draw2d.SnapToHelper.NSEW=draw2d.SnapToHelper.NORTH_SOUTH|draw2d.SnapToHelper.EAST_WEST;
draw2d.SnapToHelper.prototype.snapPoint=function(_5936,_5937,_5938){
return _5937;
};
draw2d.SnapToHelper.prototype.snapRectangle=function(_5939,_593a){
return _5939;
};
draw2d.SnapToHelper.prototype.onSetDocumentDirty=function(){
};
draw2d.SnapToGrid=function(_4a17){
draw2d.SnapToHelper.call(this,_4a17);
};
draw2d.SnapToGrid.prototype=new draw2d.SnapToHelper();
draw2d.SnapToGrid.prototype.type="draw2d.SnapToGrid";
draw2d.SnapToGrid.prototype.snapPoint=function(_4a18,_4a19,_4a1a){
_4a1a.x=this.workflow.gridWidthX*Math.floor(((_4a19.x+this.workflow.gridWidthX/2)/this.workflow.gridWidthX));
_4a1a.y=this.workflow.gridWidthY*Math.floor(((_4a19.y+this.workflow.gridWidthY/2)/this.workflow.gridWidthY));
return 0;
};
draw2d.SnapToGrid.prototype.snapRectangle=function(_4a1b,_4a1c){
_4a1c.x=_4a1b.x;
_4a1c.y=_4a1b.y;
_4a1c.w=_4a1b.w;
_4a1c.h=_4a1b.h;
return 0;
};
draw2d.SnapToGeometryEntry=function(type,_5947){
this.type=type;
this.location=_5947;
};
draw2d.SnapToGeometryEntry.prototype.getLocation=function(){
return this.location;
};
draw2d.SnapToGeometryEntry.prototype.getType=function(){
return this.type;
};
draw2d.SnapToGeometry=function(_5b02){
draw2d.SnapToHelper.call(this,_5b02);
this.rows=null;
this.cols=null;
};
draw2d.SnapToGeometry.prototype=new draw2d.SnapToHelper();
draw2d.SnapToGeometry.THRESHOLD=5;
draw2d.SnapToGeometry.prototype.snapPoint=function(_5b03,_5b04,_5b05){
if(this.rows===null||this.cols===null){
this.populateRowsAndCols();
}
if((_5b03&draw2d.SnapToHelper.EAST)!==0){
var _5b06=this.getCorrectionFor(this.cols,_5b04.getX()-1,1);
if(_5b06!==draw2d.SnapToGeometry.THRESHOLD){
_5b03&=~draw2d.SnapToHelper.EAST;
_5b05.x+=_5b06;
}
}
if((_5b03&draw2d.SnapToHelper.WEST)!==0){
var _5b07=this.getCorrectionFor(this.cols,_5b04.getX(),-1);
if(_5b07!==draw2d.SnapToGeometry.THRESHOLD){
_5b03&=~draw2d.SnapToHelper.WEST;
_5b05.x+=_5b07;
}
}
if((_5b03&draw2d.SnapToHelper.SOUTH)!==0){
var _5b08=this.getCorrectionFor(this.rows,_5b04.getY()-1,1);
if(_5b08!==draw2d.SnapToGeometry.THRESHOLD){
_5b03&=~draw2d.SnapToHelper.SOUTH;
_5b05.y+=_5b08;
}
}
if((_5b03&draw2d.SnapToHelper.NORTH)!==0){
var _5b09=this.getCorrectionFor(this.rows,_5b04.getY(),-1);
if(_5b09!==draw2d.SnapToGeometry.THRESHOLD){
_5b03&=~draw2d.SnapToHelper.NORTH;
_5b05.y+=_5b09;
}
}
return _5b03;
};
draw2d.SnapToGeometry.prototype.snapRectangle=function(_5b0a,_5b0b){
var _5b0c=_5b0a.getTopLeft();
var _5b0d=_5b0a.getBottomRight();
var _5b0e=this.snapPoint(draw2d.SnapToHelper.NORTH_WEST,_5b0a.getTopLeft(),_5b0c);
_5b0b.x=_5b0c.x;
_5b0b.y=_5b0c.y;
var _5b0f=this.snapPoint(draw2d.SnapToHelper.SOUTH_EAST,_5b0a.getBottomRight(),_5b0d);
if(_5b0e&draw2d.SnapToHelper.WEST){
_5b0b.x=_5b0d.x-_5b0a.getWidth();
}
if(_5b0e&draw2d.SnapToHelper.NORTH){
_5b0b.y=_5b0d.y-_5b0a.getHeight();
}
return _5b0e|_5b0f;
};
draw2d.SnapToGeometry.prototype.populateRowsAndCols=function(){
this.rows=[];
this.cols=[];
var _5b10=this.workflow.getDocument().getFigures();
var index=0;
for(var i=0;i<_5b10.getSize();i++){
var _5b13=_5b10.get(i);
if(_5b13!=this.workflow.getCurrentSelection()){
var _5b14=_5b13.getBounds();
this.cols[index*3]=new draw2d.SnapToGeometryEntry(-1,_5b14.getX());
this.rows[index*3]=new draw2d.SnapToGeometryEntry(-1,_5b14.getY());
this.cols[index*3+1]=new draw2d.SnapToGeometryEntry(0,_5b14.x+(_5b14.getWidth()-1)/2);
this.rows[index*3+1]=new draw2d.SnapToGeometryEntry(0,_5b14.y+(_5b14.getHeight()-1)/2);
this.cols[index*3+2]=new draw2d.SnapToGeometryEntry(1,_5b14.getRight()-1);
this.rows[index*3+2]=new draw2d.SnapToGeometryEntry(1,_5b14.getBottom()-1);
index++;
}
}
};
draw2d.SnapToGeometry.prototype.getCorrectionFor=function(_5b15,value,side){
var _5b18=draw2d.SnapToGeometry.THRESHOLD;
var _5b19=draw2d.SnapToGeometry.THRESHOLD;
for(var i=0;i<_5b15.length;i++){
var entry=_5b15[i];
var _5b1c;
if(entry.type===-1&&side!==0){
_5b1c=Math.abs(value-entry.location);
if(_5b1c<_5b18){
_5b18=_5b1c;
_5b19=entry.location-value;
}
}else{
if(entry.type===0&&side===0){
_5b1c=Math.abs(value-entry.location);
if(_5b1c<_5b18){
_5b18=_5b1c;
_5b19=entry.location-value;
}
}else{
if(entry.type===1&&side!==0){
_5b1c=Math.abs(value-entry.location);
if(_5b1c<_5b18){
_5b18=_5b1c;
_5b19=entry.location-value;
}
}
}
}
}
return _5b19;
};
draw2d.SnapToGeometry.prototype.onSetDocumentDirty=function(){
this.rows=null;
this.cols=null;
};
draw2d.Border=function(){
this.color=null;
};
draw2d.Border.prototype.type="draw2d.Border";
draw2d.Border.prototype.dispose=function(){
this.color=null;
};
draw2d.Border.prototype.getHTMLStyle=function(){
return "";
};
draw2d.Border.prototype.setColor=function(c){
this.color=c;
};
draw2d.Border.prototype.getColor=function(){
return this.color;
};
draw2d.Border.prototype.refresh=function(){
};
draw2d.LineBorder=function(width){
draw2d.Border.call(this);
this.width=1;
if(width){
this.width=width;
}
this.figure=null;
};
draw2d.LineBorder.prototype=new draw2d.Border();
draw2d.LineBorder.prototype.type="draw2d.LineBorder";
draw2d.LineBorder.prototype.dispose=function(){
draw2d.Border.prototype.dispose.call(this);
this.figure=null;
};
draw2d.LineBorder.prototype.setLineWidth=function(w){
this.width=w;
if(this.figure!==null){
this.figure.html.style.border=this.getHTMLStyle();
}
};
draw2d.LineBorder.prototype.getHTMLStyle=function(){
if(this.getColor()!==null){
return this.width+"px solid "+this.getColor().getHTMLStyle();
}
return this.width+"px solid black";
};
draw2d.LineBorder.prototype.refresh=function(){
this.setLineWidth(this.width);
};
draw2d.Figure=function(){
this.construct();
};
draw2d.Figure.prototype.type="draw2d.Figure";
draw2d.Figure.ZOrderBaseIndex=100;
draw2d.Figure.setZOrderBaseIndex=function(index){
draw2d.Figure.ZOrderBaseIndex=index;
};
draw2d.Figure.prototype.construct=function(){
this.lastDragStartTime=0;
this.x=0;
this.y=0;
this.width=10;
this.height=10;
this.border=null;
this.id=draw2d.UUID.create();
this.html=this.createHTMLElement();
this.canvas=null;
this.workflow=null;
this.draggable=null;
this.parent=null;
this.isMoving=false;
this.canSnapToHelper=true;
this.snapToGridAnchor=new draw2d.Point(0,0);
this.timer=-1;
this.model=null;
this.properties={};
this.moveListener=new draw2d.ArrayList();
this.setDimension(this.width,this.height);
this.setDeleteable(true);
this.setCanDrag(true);
this.setResizeable(true);
this.setSelectable(true);
};
draw2d.Figure.prototype.dispose=function(){
this.canvas=null;
this.workflow=null;
this.moveListener=null;
if(this.draggable!==null){
this.draggable.removeEventListener("mouseenter",this.tmpMouseEnter);
this.draggable.removeEventListener("mouseleave",this.tmpMouseLeave);
this.draggable.removeEventListener("dragend",this.tmpDragend);
this.draggable.removeEventListener("dragstart",this.tmpDragstart);
this.draggable.removeEventListener("drag",this.tmpDrag);
this.draggable.removeEventListener("dblclick",this.tmpDoubleClick);
this.draggable.node=null;
this.draggable.target.removeAllElements();
}
this.draggable=null;
if(this.border!==null){
this.border.dispose();
}
this.border=null;
if(this.parent!==null){
this.parent.removeChild(this);
}
};
draw2d.Figure.prototype.getProperties=function(){
return this.properties;
};
draw2d.Figure.prototype.getProperty=function(key){
return this.properties[key];
};
draw2d.Figure.prototype.setProperty=function(key,value){
this.properties[key]=value;
this.setDocumentDirty();
};
draw2d.Figure.prototype.getId=function(){
return this.id;
};
draw2d.Figure.prototype.setId=function(id){
this.id=id;
if(this.html!==null){
this.html.id=id;
}
};
draw2d.Figure.prototype.setCanvas=function(_59c9){
this.canvas=_59c9;
};
draw2d.Figure.prototype.getWorkflow=function(){
return this.workflow;
};
draw2d.Figure.prototype.setWorkflow=function(_59ca){
if(this.draggable===null){
this.html.tabIndex="0";
var oThis=this;
this.keyDown=function(event){
event.cancelBubble=true;
event.returnValue=true;
oThis.onKeyDown(event.keyCode,event.ctrlKey);
};
if(this.html.addEventListener){
this.html.addEventListener("keydown",this.keyDown,false);
}else{
if(this.html.attachEvent){
this.html.attachEvent("onkeydown",this.keyDown);
}
}
this.draggable=new draw2d.Draggable(this.html,draw2d.Draggable.DRAG_X|draw2d.Draggable.DRAG_Y);
this.draggable.node=this;
this.tmpContextMenu=function(_59cd){
oThis.onContextMenu(oThis.x+_59cd.x,_59cd.y+oThis.y);
};
this.tmpMouseEnter=function(_59ce){
oThis.onMouseEnter();
};
this.tmpMouseLeave=function(_59cf){
oThis.onMouseLeave();
};
this.tmpDragend=function(_59d0){
oThis.onDragend();
};
this.tmpDragstart=function(_59d1){
var w=oThis.workflow;
w.showMenu(null);
if(w.toolPalette&&w.toolPalette.activeTool){
_59d1.returnValue=false;
w.onMouseDown(oThis.x+_59d1.x,_59d1.y+oThis.y);
w.onMouseUp(oThis.x+_59d1.x,_59d1.y+oThis.y);
return;
}
if(!(oThis instanceof draw2d.ResizeHandle)&&!(oThis instanceof draw2d.Port)){
var line=w.getBestLine(oThis.x+_59d1.x,_59d1.y+oThis.y);
if(line!==null){
_59d1.returnValue=false;
w.setCurrentSelection(line);
w.showLineResizeHandles(line);
w.onMouseDown(oThis.x+_59d1.x,_59d1.y+oThis.y);
return;
}else{
if(oThis.isSelectable()){
w.showResizeHandles(oThis);
w.setCurrentSelection(oThis);
}
}
}
_59d1.returnValue=oThis.onDragstart(_59d1.x,_59d1.y);
};
this.tmpDrag=function(_59d4){
oThis.onDrag();
};
this.tmpDoubleClick=function(_59d5){
oThis.onDoubleClick();
};
this.draggable.addEventListener("contextmenu",this.tmpContextMenu);
this.draggable.addEventListener("mouseenter",this.tmpMouseEnter);
this.draggable.addEventListener("mouseleave",this.tmpMouseLeave);
this.draggable.addEventListener("dragend",this.tmpDragend);
this.draggable.addEventListener("dragstart",this.tmpDragstart);
this.draggable.addEventListener("drag",this.tmpDrag);
this.draggable.addEventListener("dblclick",this.tmpDoubleClick);
}
this.workflow=_59ca;
};
draw2d.Figure.prototype.createHTMLElement=function(){
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
item.style.zIndex=""+draw2d.Figure.ZOrderBaseIndex;
return item;
};
draw2d.Figure.prototype.setParent=function(_59d7){
this.parent=_59d7;
};
draw2d.Figure.prototype.getParent=function(){
return this.parent;
};
draw2d.Figure.prototype.getZOrder=function(){
return this.html.style.zIndex;
};
draw2d.Figure.prototype.setZOrder=function(index){
this.html.style.zIndex=index;
};
draw2d.Figure.prototype.hasFixedPosition=function(){
return false;
};
draw2d.Figure.prototype.getMinWidth=function(){
return 5;
};
draw2d.Figure.prototype.getMinHeight=function(){
return 5;
};
draw2d.Figure.prototype.getHTMLElement=function(){
if(this.html===null){
this.html=this.createHTMLElement();
}
return this.html;
};
draw2d.Figure.prototype.paint=function(){
};
draw2d.Figure.prototype.setBorder=function(_59d9){
if(this.border!==null){
this.border.figure=null;
}
this.border=_59d9;
this.border.figure=this;
this.border.refresh();
this.setDocumentDirty();
};
draw2d.Figure.prototype.onRemove=function(_59da){
};
draw2d.Figure.prototype.onContextMenu=function(x,y){
var menu=this.getContextMenu();
if(menu!==null){
this.workflow.showMenu(menu,x,y);
}
};
draw2d.Figure.prototype.getContextMenu=function(){
return null;
};
draw2d.Figure.prototype.onDoubleClick=function(){
};
draw2d.Figure.prototype.onMouseEnter=function(){
};
draw2d.Figure.prototype.onMouseLeave=function(){
};
draw2d.Figure.prototype.onDrag=function(){
this.x=this.draggable.getLeft();
this.y=this.draggable.getTop();
if(this.isMoving==false){
this.isMoving=true;
this.setAlpha(0.5);
}
this.fireMoveEvent();
};
draw2d.Figure.prototype.onDragend=function(){
if(this.getWorkflow().getEnableSmoothFigureHandling()==true){
var _59de=this;
var _59df=function(){
if(_59de.alpha<1){
_59de.setAlpha(Math.min(1,_59de.alpha+0.05));
}else{
window.clearInterval(_59de.timer);
_59de.timer=-1;
}
};
if(_59de.timer>0){
window.clearInterval(_59de.timer);
}
_59de.timer=window.setInterval(_59df,20);
}else{
this.setAlpha(1);
}
this.command.setPosition(this.x,this.y);
this.workflow.commandStack.execute(this.command);
this.command=null;
this.isMoving=false;
this.workflow.hideSnapToHelperLines();
this.fireMoveEvent();
};
draw2d.Figure.prototype.onDragstart=function(x,y){
this.command=this.createCommand(new draw2d.EditPolicy(draw2d.EditPolicy.MOVE));
return this.command!==null;
};
draw2d.Figure.prototype.setCanDrag=function(flag){
this.canDrag=flag;
if(flag){
this.html.style.cursor="move";
}else{
this.html.style.cursor="";
}
};
draw2d.Figure.prototype.getCanDrag=function(){
return this.canDrag;
};
draw2d.Figure.prototype.setAlpha=function(_59e3){
if(this.alpha==_59e3){
return;
}
try{
this.html.style.MozOpacity=_59e3;
}
catch(exc){
}
try{
this.html.style.opacity=_59e3;
}
catch(exc){
}
try{
var _59e4=Math.round(_59e3*100);
if(_59e4>=99){
this.html.style.filter="";
}else{
this.html.style.filter="alpha(opacity="+_59e4+")";
}
}
catch(exc){
}
this.alpha=_59e3;
};
draw2d.Figure.prototype.setDimension=function(w,h){
this.width=Math.max(this.getMinWidth(),w);
this.height=Math.max(this.getMinHeight(),h);
if(this.html===null){
return;
}
this.html.style.width=this.width+"px";
this.html.style.height=this.height+"px";
this.fireMoveEvent();
if(this.workflow!==null&&this.workflow.getCurrentSelection()==this){
this.workflow.showResizeHandles(this);
}
};
draw2d.Figure.prototype.setPosition=function(xPos,yPos){
this.x=xPos;
this.y=yPos;
if(this.html===null){
return;
}
this.html.style.left=this.x+"px";
this.html.style.top=this.y+"px";
this.fireMoveEvent();
if(this.workflow!==null&&this.workflow.getCurrentSelection()==this){
this.workflow.showResizeHandles(this);
}
};
draw2d.Figure.prototype.isResizeable=function(){
return this.resizeable;
};
draw2d.Figure.prototype.setResizeable=function(flag){
this.resizeable=flag;
};
draw2d.Figure.prototype.isSelectable=function(){
return this.selectable;
};
draw2d.Figure.prototype.setSelectable=function(flag){
this.selectable=flag;
};
draw2d.Figure.prototype.isStrechable=function(){
return true;
};
draw2d.Figure.prototype.isDeleteable=function(){
return this.deleteable;
};
draw2d.Figure.prototype.setDeleteable=function(flag){
this.deleteable=flag;
};
draw2d.Figure.prototype.setCanSnapToHelper=function(flag){
this.canSnapToHelper=flag;
};
draw2d.Figure.prototype.getCanSnapToHelper=function(){
return this.canSnapToHelper;
};
draw2d.Figure.prototype.getSnapToGridAnchor=function(){
return this.snapToGridAnchor;
};
draw2d.Figure.prototype.setSnapToGridAnchor=function(point){
this.snapToGridAnchor=point;
};
draw2d.Figure.prototype.getBounds=function(){
return new draw2d.Dimension(this.getX(),this.getY(),this.getWidth(),this.getHeight());
};
draw2d.Figure.prototype.getWidth=function(){
return this.width;
};
draw2d.Figure.prototype.getHeight=function(){
return this.height;
};
draw2d.Figure.prototype.getY=function(){
return this.y;
};
draw2d.Figure.prototype.getX=function(){
return this.x;
};
draw2d.Figure.prototype.getAbsoluteY=function(){
return this.y;
};
draw2d.Figure.prototype.getAbsoluteX=function(){
return this.x;
};
draw2d.Figure.prototype.onKeyDown=function(_59ee,ctrl){
if(_59ee==46){
this.workflow.getCommandStack().execute(this.createCommand(new draw2d.EditPolicy(draw2d.EditPolicy.DELETE)));
}
if(ctrl){
this.workflow.onKeyDown(_59ee,ctrl);
}
};
draw2d.Figure.prototype.getPosition=function(){
return new draw2d.Point(this.x,this.y);
};
draw2d.Figure.prototype.isOver=function(iX,iY){
var x=this.getAbsoluteX();
var y=this.getAbsoluteY();
var iX2=x+this.width;
var iY2=y+this.height;
return (iX>=x&&iX<=iX2&&iY>=y&&iY<=iY2);
};
draw2d.Figure.prototype.attachMoveListener=function(_59f6){
if(_59f6===null||this.moveListener===null){
return;
}
this.moveListener.add(_59f6);
};
draw2d.Figure.prototype.detachMoveListener=function(_59f7){
if(_59f7===null||this.moveListener===null){
return;
}
this.moveListener.remove(_59f7);
};
draw2d.Figure.prototype.fireMoveEvent=function(){
this.setDocumentDirty();
var size=this.moveListener.getSize();
for(var i=0;i<size;i++){
this.moveListener.get(i).onOtherFigureMoved(this);
}
};
draw2d.Figure.prototype.setModel=function(model){
if(this.model!==null){
this.model.removePropertyChangeListener(this);
}
this.model=model;
if(this.model!==null){
this.model.addPropertyChangeListener(this);
}
};
draw2d.Figure.prototype.getModel=function(){
return this.model;
};
draw2d.Figure.prototype.onOtherFigureMoved=function(_59fb){
};
draw2d.Figure.prototype.setDocumentDirty=function(){
if(this.workflow!==null){
this.workflow.setDocumentDirty();
}
};
draw2d.Figure.prototype.disableTextSelection=function(_59fc){
_59fc.onselectstart=function(){
return false;
};
_59fc.unselectable="on";
_59fc.style.MozUserSelect="none";
_59fc.onmousedown=function(){
return false;
};
};
draw2d.Figure.prototype.createCommand=function(_59fd){
if(_59fd.getPolicy()==draw2d.EditPolicy.MOVE){
if(!this.canDrag){
return null;
}
return new draw2d.CommandMove(this);
}
if(_59fd.getPolicy()==draw2d.EditPolicy.DELETE){
if(!this.isDeleteable()){
return null;
}
return new draw2d.CommandDelete(this);
}
if(_59fd.getPolicy()==draw2d.EditPolicy.RESIZE){
if(!this.isResizeable()){
return null;
}
return new draw2d.CommandResize(this);
}
return null;
};
draw2d.Node=function(){
this.bgColor=null;
this.lineColor=new draw2d.Color(128,128,255);
this.lineStroke=1;
this.ports=new draw2d.ArrayList();
draw2d.Figure.call(this);
};
draw2d.Node.prototype=new draw2d.Figure();
draw2d.Node.prototype.type="draw2d.Node";
draw2d.Node.prototype.dispose=function(){
for(var i=0;i<this.ports.getSize();i++){
this.ports.get(i).dispose();
}
this.ports=null;
draw2d.Figure.prototype.dispose.call(this);
};
draw2d.Node.prototype.createHTMLElement=function(){
var item=draw2d.Figure.prototype.createHTMLElement.call(this);
item.style.width="auto";
item.style.height="auto";
item.style.margin="0px";
item.style.padding="0px";
if(this.lineColor!==null){
item.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
}
item.style.fontSize="1px";
if(this.bgColor!==null){
item.style.backgroundColor=this.bgColor.getHTMLStyle();
}
return item;
};
draw2d.Node.prototype.paint=function(){
draw2d.Figure.prototype.paint.call(this);
for(var i=0;i<this.ports.getSize();i++){
this.ports.get(i).paint();
}
};
draw2d.Node.prototype.getPorts=function(){
return this.ports;
};
draw2d.Node.prototype.getInputPorts=function(){
var _4adb=new draw2d.ArrayList();
for(var i=0;i<this.ports.getSize();i++){
var port=this.ports.get(i);
if(port instanceof draw2d.InputPort){
_4adb.add(port);
}
}
return _4adb;
};
draw2d.Node.prototype.getOutputPorts=function(){
var _4ade=new draw2d.ArrayList();
for(var i=0;i<this.ports.getSize();i++){
var port=this.ports.get(i);
if(port instanceof draw2d.OutputPort){
_4ade.add(port);
}
}
return _4ade;
};
draw2d.Node.prototype.getPort=function(_4ae1){
if(this.ports===null){
return null;
}
for(var i=0;i<this.ports.getSize();i++){
var port=this.ports.get(i);
if(port.getName()==_4ae1){
return port;
}
}
};
draw2d.Node.prototype.getInputPort=function(_4ae4){
if(this.ports===null){
return null;
}
for(var i=0;i<this.ports.getSize();i++){
var port=this.ports.get(i);
if(port.getName()==_4ae4&&port instanceof draw2d.InputPort){
return port;
}
}
};
draw2d.Node.prototype.getOutputPort=function(_4ae7){
if(this.ports===null){
return null;
}
for(var i=0;i<this.ports.getSize();i++){
var port=this.ports.get(i);
if(port.getName()==_4ae7&&port instanceof draw2d.OutputPort){
return port;
}
}
};
draw2d.Node.prototype.addPort=function(port,x,y){
this.ports.add(port);
port.setOrigin(x,y);
port.setPosition(x,y);
port.setParent(this);
port.setDeleteable(false);
this.html.appendChild(port.getHTMLElement());
if(this.workflow!==null){
this.workflow.registerPort(port);
}
};
draw2d.Node.prototype.removePort=function(port){
if(this.ports!==null){
this.ports.remove(port);
}
try{
this.html.removeChild(port.getHTMLElement());
}
catch(exc){
}
if(this.workflow!==null){
this.workflow.unregisterPort(port);
}
var _4aee=port.getConnections();
for(var i=0;i<_4aee.getSize();++i){
this.workflow.removeFigure(_4aee.get(i));
}
};
draw2d.Node.prototype.setWorkflow=function(_4af0){
var _4af1=this.workflow;
draw2d.Figure.prototype.setWorkflow.call(this,_4af0);
if(_4af1!==null){
for(var i=0;i<this.ports.getSize();i++){
_4af1.unregisterPort(this.ports.get(i));
}
}
if(this.workflow!==null){
for(var i=0;i<this.ports.getSize();i++){
this.workflow.registerPort(this.ports.get(i));
}
}
};
draw2d.Node.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.bgColor!==null){
this.html.style.backgroundColor=this.bgColor.getHTMLStyle();
}else{
this.html.style.backgroundColor="transparent";
}
};
draw2d.Node.prototype.getBackgroundColor=function(){
return this.bgColor;
};
draw2d.Node.prototype.setColor=function(color){
this.lineColor=color;
if(this.lineColor!==null){
this.html.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
}else{
this.html.style.border="0px";
}
};
draw2d.Node.prototype.setLineWidth=function(w){
this.lineStroke=w;
if(this.lineColor!==null){
this.html.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
}else{
this.html.style.border="0px";
}
};
draw2d.Node.prototype.getModelSourceConnections=function(){
throw "You must override the method [Node.prototype.getModelSourceConnections]";
};
draw2d.Node.prototype.refreshConnections=function(){
if(this.workflow!==null){
this.workflow.refreshConnections(this);
}
};
draw2d.VectorFigure=function(){
this.bgColor=null;
this.lineColor=new draw2d.Color(0,0,0);
this.stroke=1;
this.graphics=null;
draw2d.Node.call(this);
};
draw2d.VectorFigure.prototype=new draw2d.Node;
draw2d.VectorFigure.prototype.type="draw2d.VectorFigure";
draw2d.VectorFigure.prototype.dispose=function(){
draw2d.Node.prototype.dispose.call(this);
this.bgColor=null;
this.lineColor=null;
if(this.graphics!==null){
this.graphics.clear();
}
this.graphics=null;
};
draw2d.VectorFigure.prototype.createHTMLElement=function(){
var item=draw2d.Node.prototype.createHTMLElement.call(this);
item.style.border="0px";
item.style.backgroundColor="transparent";
return item;
};
draw2d.VectorFigure.prototype.setWorkflow=function(_4b47){
draw2d.Node.prototype.setWorkflow.call(this,_4b47);
if(this.workflow===null){
this.graphics.clear();
this.graphics=null;
}
};
draw2d.VectorFigure.prototype.paint=function(){
if(this.html===null){
return;
}
try{
if(this.graphics===null){
this.graphics=new jsGraphics(this.html);
}else{
this.graphics.clear();
}
draw2d.Node.prototype.paint.call(this);
for(var i=0;i<this.ports.getSize();i++){
this.getHTMLElement().appendChild(this.ports.get(i).getHTMLElement());
}
}
catch(e){
pushErrorStack(e,"draw2d.VectorFigure.prototype.paint=function()["+area+"]");
}
};
draw2d.VectorFigure.prototype.setDimension=function(w,h){
draw2d.Node.prototype.setDimension.call(this,w,h);
if(this.graphics!==null){
this.paint();
}
};
draw2d.VectorFigure.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.graphics!==null){
this.paint();
}
};
draw2d.VectorFigure.prototype.getBackgroundColor=function(){
return this.bgColor;
};
draw2d.VectorFigure.prototype.setLineWidth=function(w){
this.stroke=w;
if(this.graphics!==null){
this.paint();
}
};
draw2d.VectorFigure.prototype.setColor=function(color){
this.lineColor=color;
if(this.graphics!==null){
this.paint();
}
};
draw2d.VectorFigure.prototype.getColor=function(){
return this.lineColor;
};
draw2d.SVGFigure=function(width,_5073){
this.bgColor=null;
this.lineColor=new draw2d.Color(0,0,0);
this.stroke=1;
this.context=null;
draw2d.Node.call(this);
if(width&&_5073){
this.setDimension(width,_5073);
}
};
draw2d.SVGFigure.prototype=new draw2d.Node();
draw2d.SVGFigure.prototype.type="draw2d.SVGFigure";
draw2d.SVGFigure.prototype.createHTMLElement=function(){
var item=new MooCanvas(this.id,{width:100,height:100});
item.style.position="absolute";
item.style.left=this.x+"px";
item.style.top=this.y+"px";
item.style.zIndex=""+draw2d.Figure.ZOrderBaseIndex;
this.context=item.getContext("2d");
return item;
};
draw2d.SVGFigure.prototype.paint=function(){
this.context.clearRect(0,0,this.getWidth(),this.getHeight());
this.context.fillStyle="rgba(200,0,0,0.3)";
this.context.fillRect(0,0,this.getWidth(),this.getHeight());
};
draw2d.SVGFigure.prototype.setDimension=function(w,h){
draw2d.Node.prototype.setDimension.call(this,w,h);
this.html.width=w;
this.html.height=h;
this.html.style.width=w+"px";
this.html.style.height=h+"px";
if(this.context!==null){
if(this.context.element){
this.context.element.style.width=w+"px";
this.context.element.style.height=h+"px";
}
this.paint();
}
};
draw2d.SVGFigure.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.graphics!==null){
this.paint();
}
};
draw2d.SVGFigure.prototype.getBackgroundColor=function(){
return this.bgColor;
};
draw2d.SVGFigure.prototype.setLineWidth=function(w){
this.stroke=w;
if(this.context!==null){
this.paint();
}
};
draw2d.SVGFigure.prototype.setColor=function(color){
this.lineColor=color;
if(this.context!==null){
this.paint();
}
};
draw2d.SVGFigure.prototype.getColor=function(){
return this.lineColor;
};
draw2d.Label=function(msg){
this.msg=msg;
this.bgColor=null;
this.color=new draw2d.Color(0,0,0);
this.fontSize=10;
this.textNode=null;
this.align="center";
draw2d.Figure.call(this);
};
draw2d.Label.prototype=new draw2d.Figure();
draw2d.Label.prototype.type="draw2d.Label";
draw2d.Label.prototype.createHTMLElement=function(){
var item=draw2d.Figure.prototype.createHTMLElement.call(this);
this.textNode=document.createTextNode(this.msg);
item.appendChild(this.textNode);
item.style.color=this.color.getHTMLStyle();
item.style.fontSize=this.fontSize+"pt";
item.style.width="auto";
item.style.height="auto";
item.style.paddingLeft="3px";
item.style.paddingRight="3px";
item.style.textAlign=this.align;
item.style.MozUserSelect="none";
this.disableTextSelection(item);
if(this.bgColor!==null){
item.style.backgroundColor=this.bgColor.getHTMLStyle();
}
return item;
};
draw2d.Label.prototype.isResizeable=function(){
return false;
};
draw2d.Label.prototype.setWordwrap=function(flag){
this.html.style.whiteSpace=flag?"wrap":"nowrap";
};
draw2d.Label.prototype.setAlign=function(align){
this.align=align;
this.html.style.textAlign=align;
};
draw2d.Label.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.bgColor!==null){
this.html.style.backgroundColor=this.bgColor.getHTMLStyle();
}else{
this.html.style.backgroundColor="transparent";
}
};
draw2d.Label.prototype.setColor=function(color){
this.color=color;
this.html.style.color=this.color.getHTMLStyle();
};
draw2d.Label.prototype.setFontSize=function(size){
this.fontSize=size;
this.html.style.fontSize=this.fontSize+"pt";
};
draw2d.Label.prototype.setDimension=function(w,h){
};
draw2d.Label.prototype.getWidth=function(){
if(window.getComputedStyle){
return parseInt(getComputedStyle(this.html,"").getPropertyValue("width"));
}
return parseInt(this.html.clientWidth);
};
draw2d.Label.prototype.getHeight=function(){
if(window.getComputedStyle){
return parseInt(getComputedStyle(this.html,"").getPropertyValue("height"));
}
return parseInt(this.html.clientHeight);
};
draw2d.Label.prototype.getText=function(){
return this.msg;
};
draw2d.Label.prototype.setText=function(text){
this.msg=text;
this.html.removeChild(this.textNode);
this.textNode=document.createTextNode(this.msg);
this.html.appendChild(this.textNode);
};
draw2d.Label.prototype.setStyledText=function(text){
this.msg=text;
this.html.removeChild(this.textNode);
this.textNode=document.createElement("div");
this.textNode.style.whiteSpace="nowrap";
this.textNode.innerHTML=text;
this.html.appendChild(this.textNode);
};
draw2d.Oval=function(){
draw2d.VectorFigure.call(this);
};
draw2d.Oval.prototype=new draw2d.VectorFigure();
draw2d.Oval.prototype.type="draw2d.Oval";
draw2d.Oval.prototype.paint=function(){
if(this.html===null){
return;
}
try{
draw2d.VectorFigure.prototype.paint.call(this);
this.graphics.setStroke(this.stroke);
if(this.bgColor!==null){
this.graphics.setColor(this.bgColor.getHTMLStyle());
this.graphics.fillOval(0,0,this.getWidth()-1,this.getHeight()-1);
}
if(this.lineColor!==null){
this.graphics.setColor(this.lineColor.getHTMLStyle());
this.graphics.drawOval(0,0,this.getWidth()-1,this.getHeight()-1);
}
this.graphics.paint();
}
catch(e){
pushErrorStack(e,"draw2d.Oval.prototype.paint=function()");
}
};
draw2d.Circle=function(_5047){
draw2d.Oval.call(this);
if(_5047){
this.setDimension(_5047,_5047);
}
};
draw2d.Circle.prototype=new draw2d.Oval();
draw2d.Circle.prototype.type="draw2d.Circle";
draw2d.Circle.prototype.setDimension=function(w,h){
if(w>h){
draw2d.Oval.prototype.setDimension.call(this,w,w);
}else{
draw2d.Oval.prototype.setDimension.call(this,h,h);
}
};
draw2d.Circle.prototype.isStrechable=function(){
return false;
};
draw2d.Rectangle=function(width,_569b){
this.bgColor=null;
this.lineColor=new draw2d.Color(0,0,0);
this.lineStroke=1;
draw2d.Figure.call(this);
if(width&&_569b){
this.setDimension(width,_569b);
}
};
draw2d.Rectangle.prototype=new draw2d.Figure();
draw2d.Rectangle.prototype.type="draw2d.Rectangle";
draw2d.Rectangle.prototype.dispose=function(){
draw2d.Figure.prototype.dispose.call(this);
this.bgColor=null;
this.lineColor=null;
};
draw2d.Rectangle.prototype.createHTMLElement=function(){
var item=draw2d.Figure.prototype.createHTMLElement.call(this);
item.style.width="auto";
item.style.height="auto";
item.style.margin="0px";
item.style.padding="0px";
item.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
item.style.fontSize="1px";
item.style.lineHeight="1px";
item.innerHTML="&nbsp";
if(this.bgColor!==null){
item.style.backgroundColor=this.bgColor.getHTMLStyle();
}
return item;
};
draw2d.Rectangle.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.bgColor!==null){
this.html.style.backgroundColor=this.bgColor.getHTMLStyle();
}else{
this.html.style.backgroundColor="transparent";
}
};
draw2d.Rectangle.prototype.getBackgroundColor=function(){
return this.bgColor;
};
draw2d.Rectangle.prototype.setColor=function(color){
this.lineColor=color;
if(this.lineColor!==null){
this.html.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
}else{
this.html.style.border=this.lineStroke+"0px";
}
};
draw2d.Rectangle.prototype.getColor=function(){
return this.lineColor;
};
draw2d.Rectangle.prototype.getWidth=function(){
return draw2d.Figure.prototype.getWidth.call(this)+2*this.lineStroke;
};
draw2d.Rectangle.prototype.getHeight=function(){
return draw2d.Figure.prototype.getHeight.call(this)+2*this.lineStroke;
};
draw2d.Rectangle.prototype.setDimension=function(w,h){
draw2d.Figure.prototype.setDimension.call(this,w-2*this.lineStroke,h-2*this.lineStroke);
};
draw2d.Rectangle.prototype.setLineWidth=function(w){
var diff=w-this.lineStroke;
this.setDimension(this.getWidth()-2*diff,this.getHeight()-2*diff);
this.lineStroke=w;
var c="transparent";
if(this.lineColor!==null){
c=this.lineColor.getHTMLStyle();
}
this.html.style.border=this.lineStroke+"px solid "+c;
};
draw2d.Rectangle.prototype.getLineWidth=function(){
return this.lineStroke;
};
draw2d.ImageFigure=function(url){
if(url===undefined){
url=null;
}
this.url=url;
draw2d.Node.call(this);
this.setDimension(40,40);
};
draw2d.ImageFigure.prototype=new draw2d.Node;
draw2d.ImageFigure.prototype.type="draw2d.Image";
draw2d.ImageFigure.prototype.createHTMLElement=function(){
var item=draw2d.Node.prototype.createHTMLElement.call(this);
item.style.width=this.width+"px";
item.style.height=this.height+"px";
item.style.margin="0px";
item.style.padding="0px";
item.style.border="0px";
if(this.url!==null){
item.style.backgroundImage="url("+this.url+")";
}else{
item.style.backgroundImage="";
}
return item;
};
draw2d.ImageFigure.prototype.setColor=function(color){
};
draw2d.ImageFigure.prototype.isResizeable=function(){
return false;
};
draw2d.ImageFigure.prototype.setImage=function(url){
if(url===undefined){
url=null;
}
this.url=url;
if(this.url!==null){
this.html.style.backgroundImage="url("+this.url+")";
}else{
this.html.style.backgroundImage="";
}
};
draw2d.Port=function(_5733,_5734){
Corona=function(){
};
Corona.prototype=new draw2d.Circle();
Corona.prototype.setAlpha=function(_5735){
draw2d.Circle.prototype.setAlpha.call(this,Math.min(0.3,_5735));
this.setDeleteable(false);
this.setCanDrag(false);
this.setResizeable(false);
this.setSelectable(false);
};
if(_5733===null||_5733===undefined){
this.currentUIRepresentation=new draw2d.Circle();
}else{
this.currentUIRepresentation=_5733;
}
if(_5734===null||_5734===undefined){
this.connectedUIRepresentation=new draw2d.Circle();
this.connectedUIRepresentation.setColor(null);
}else{
this.connectedUIRepresentation=_5734;
}
this.disconnectedUIRepresentation=this.currentUIRepresentation;
this.hideIfConnected=false;
this.uiRepresentationAdded=true;
this.parentNode=null;
this.originX=0;
this.originY=0;
this.coronaWidth=10;
this.corona=null;
draw2d.Rectangle.call(this);
this.setDimension(8,8);
this.setBackgroundColor(new draw2d.Color(100,180,100));
this.setColor(new draw2d.Color(90,150,90));
draw2d.Rectangle.prototype.setColor.call(this,null);
this.dropable=new draw2d.DropTarget(this.html);
this.dropable.node=this;
this.dropable.addEventListener("dragenter",function(_5736){
_5736.target.node.onDragEnter(_5736.relatedTarget.node);
});
this.dropable.addEventListener("dragleave",function(_5737){
_5737.target.node.onDragLeave(_5737.relatedTarget.node);
});
this.dropable.addEventListener("drop",function(_5738){
_5738.relatedTarget.node.onDrop(_5738.target.node);
});
};
draw2d.Port.prototype=new draw2d.Rectangle();
draw2d.Port.prototype.type="draw2d.Port";
draw2d.Port.ZOrderBaseIndex=5000;
draw2d.Port.setZOrderBaseIndex=function(index){
draw2d.Port.ZOrderBaseIndex=index;
};
draw2d.Port.prototype.setHideIfConnected=function(flag){
this.hideIfConnected=flag;
};
draw2d.Port.prototype.dispose=function(){
var size=this.moveListener.getSize();
for(var i=0;i<size;i++){
var _573d=this.moveListener.get(i);
this.parentNode.workflow.removeFigure(_573d);
_573d.dispose();
}
draw2d.Rectangle.prototype.dispose.call(this);
this.parentNode=null;
this.dropable.node=null;
this.dropable=null;
this.disconnectedUIRepresentation.dispose();
this.connectedUIRepresentation.dispose();
};
draw2d.Port.prototype.createHTMLElement=function(){
var item=draw2d.Rectangle.prototype.createHTMLElement.call(this);
item.style.zIndex=draw2d.Port.ZOrderBaseIndex;
this.currentUIRepresentation.html.zIndex=draw2d.Port.ZOrderBaseIndex;
item.appendChild(this.currentUIRepresentation.html);
this.uiRepresentationAdded=true;
return item;
};
draw2d.Port.prototype.setUiRepresentation=function(_573f){
if(_573f===null){
_573f=new draw2d.Figure();
}
if(this.uiRepresentationAdded){
this.html.removeChild(this.currentUIRepresentation.getHTMLElement());
}
this.html.appendChild(_573f.getHTMLElement());
_573f.paint();
this.currentUIRepresentation=_573f;
};
draw2d.Port.prototype.onMouseEnter=function(){
this.setLineWidth(2);
};
draw2d.Port.prototype.onMouseLeave=function(){
this.setLineWidth(0);
};
draw2d.Port.prototype.setDimension=function(width,_5741){
draw2d.Rectangle.prototype.setDimension.call(this,width,_5741);
this.connectedUIRepresentation.setDimension(width,_5741);
this.disconnectedUIRepresentation.setDimension(width,_5741);
this.setPosition(this.x,this.y);
};
draw2d.Port.prototype.setBackgroundColor=function(color){
this.currentUIRepresentation.setBackgroundColor(color);
};
draw2d.Port.prototype.getBackgroundColor=function(){
return this.currentUIRepresentation.getBackgroundColor();
};
draw2d.Port.prototype.getConnections=function(){
var _5743=new draw2d.ArrayList();
var size=this.moveListener.getSize();
for(var i=0;i<size;i++){
var _5746=this.moveListener.get(i);
if(_5746 instanceof draw2d.Connection){
_5743.add(_5746);
}
}
return _5743;
};
draw2d.Port.prototype.setColor=function(color){
this.currentUIRepresentation.setColor(color);
};
draw2d.Port.prototype.getColor=function(){
return this.currentUIRepresentation.getColor();
};
draw2d.Port.prototype.setLineWidth=function(width){
this.currentUIRepresentation.setLineWidth(width);
};
draw2d.Port.prototype.getLineWidth=function(){
return this.currentUIRepresentation.getLineWidth();
};
draw2d.Port.prototype.paint=function(){
try{
this.currentUIRepresentation.paint();
}
catch(e){
pushErrorStack(e,"draw2d.Port.prototype.paint=function()");
}
};
draw2d.Port.prototype.setPosition=function(xPos,yPos){
this.originX=xPos;
this.originY=yPos;
draw2d.Rectangle.prototype.setPosition.call(this,xPos,yPos);
if(this.html===null){
return;
}
this.html.style.left=(this.x-this.getWidth()/2)+"px";
this.html.style.top=(this.y-this.getHeight()/2)+"px";
};
draw2d.Port.prototype.setParent=function(_574b){
if(this.parentNode!==null){
this.parentNode.detachMoveListener(this);
}
this.parentNode=_574b;
if(this.parentNode!==null){
this.parentNode.attachMoveListener(this);
}
};
draw2d.Port.prototype.attachMoveListener=function(_574c){
draw2d.Rectangle.prototype.attachMoveListener.call(this,_574c);
if(this.hideIfConnected==true){
this.setUiRepresentation(this.connectedUIRepresentation);
}
};
draw2d.Port.prototype.detachMoveListener=function(_574d){
draw2d.Rectangle.prototype.detachMoveListener.call(this,_574d);
if(this.getConnections().getSize()==0){
this.setUiRepresentation(this.disconnectedUIRepresentation);
}
};
draw2d.Port.prototype.getParent=function(){
return this.parentNode;
};
draw2d.Port.prototype.onDrag=function(){
draw2d.Rectangle.prototype.onDrag.call(this);
this.parentNode.workflow.showConnectionLine(this.parentNode.x+this.x,this.parentNode.y+this.y,this.parentNode.x+this.originX,this.parentNode.y+this.originY);
};
draw2d.Port.prototype.getCoronaWidth=function(){
return this.coronaWidth;
};
draw2d.Port.prototype.setCoronaWidth=function(width){
this.coronaWidth=width;
};
draw2d.Port.prototype.setOrigin=function(x,y){
this.originX=x;
this.originY=y;
};
draw2d.Port.prototype.onDragend=function(){
this.setAlpha(1);
this.setPosition(this.originX,this.originY);
this.parentNode.workflow.hideConnectionLine();
document.body.focus();
};
draw2d.Port.prototype.onDragEnter=function(port){
var _5752=new draw2d.EditPolicy(draw2d.EditPolicy.CONNECT);
_5752.canvas=this.parentNode.workflow;
_5752.source=port;
_5752.target=this;
var _5753=this.createCommand(_5752);
if(_5753===null){
return;
}
this.parentNode.workflow.connectionLine.setColor(new draw2d.Color(0,150,0));
this.parentNode.workflow.connectionLine.setLineWidth(3);
this.showCorona(true);
};
draw2d.Port.prototype.onDragLeave=function(port){
this.parentNode.workflow.connectionLine.setColor(new draw2d.Color(0,0,0));
this.parentNode.workflow.connectionLine.setLineWidth(1);
this.showCorona(false);
};
draw2d.Port.prototype.onDrop=function(port){
var _5756=new draw2d.EditPolicy(draw2d.EditPolicy.CONNECT);
_5756.canvas=this.parentNode.workflow;
_5756.source=port;
_5756.target=this;
var _5757=this.createCommand(_5756);
if(_5757!==null){
this.parentNode.workflow.getCommandStack().execute(_5757);
}
};
draw2d.Port.prototype.getAbsolutePosition=function(){
return new draw2d.Point(this.getAbsoluteX(),this.getAbsoluteY());
};
draw2d.Port.prototype.getAbsoluteBounds=function(){
return new draw2d.Dimension(this.getAbsoluteX(),this.getAbsoluteY(),this.getWidth(),this.getHeight());
};
draw2d.Port.prototype.getAbsoluteY=function(){
return this.originY+this.parentNode.getY();
};
draw2d.Port.prototype.getAbsoluteX=function(){
return this.originX+this.parentNode.getX();
};
draw2d.Port.prototype.onOtherFigureMoved=function(_5758){
this.fireMoveEvent();
};
draw2d.Port.prototype.getName=function(){
return this.name;
};
draw2d.Port.prototype.setName=function(name){
this.name=name;
};
draw2d.Port.prototype.isOver=function(iX,iY){
var x=this.getAbsoluteX()-this.coronaWidth-this.getWidth()/2;
var y=this.getAbsoluteY()-this.coronaWidth-this.getHeight()/2;
var iX2=x+this.width+(this.coronaWidth*2)+this.getWidth()/2;
var iY2=y+this.height+(this.coronaWidth*2)+this.getHeight()/2;
return (iX>=x&&iX<=iX2&&iY>=y&&iY<=iY2);
};
draw2d.Port.prototype.showCorona=function(flag,_5761){
if(flag===true){
this.corona=new Corona();
this.corona.setAlpha(0.3);
this.corona.setBackgroundColor(new draw2d.Color(0,125,125));
this.corona.setColor(null);
this.corona.setDimension(this.getWidth()+(this.getCoronaWidth()*2),this.getWidth()+(this.getCoronaWidth()*2));
this.parentNode.getWorkflow().addFigure(this.corona,this.getAbsoluteX()-this.getCoronaWidth()-this.getWidth()/2,this.getAbsoluteY()-this.getCoronaWidth()-this.getHeight()/2);
}else{
if(flag===false&&this.corona!==null){
this.parentNode.getWorkflow().removeFigure(this.corona);
this.corona=null;
}
}
};
draw2d.Port.prototype.createCommand=function(_5762){
if(_5762.getPolicy()===draw2d.EditPolicy.MOVE){
if(!this.canDrag){
return null;
}
return new draw2d.CommandMovePort(this);
}
if(_5762.getPolicy()===draw2d.EditPolicy.CONNECT){
if(_5762.source.parentNode.id===_5762.target.parentNode.id){
return null;
}else{
return new draw2d.CommandConnect(_5762.canvas,_5762.source,_5762.target);
}
}
return null;
};
draw2d.InputPort=function(_590e){
draw2d.Port.call(this,_590e);
};
draw2d.InputPort.prototype=new draw2d.Port();
draw2d.InputPort.prototype.type="draw2d.InputPort";
draw2d.InputPort.prototype.onDragstart=function(x,y){
if(!this.canDrag){
return false;
}
return true;
};
draw2d.InputPort.prototype.onDragEnter=function(port){
if(port instanceof draw2d.OutputPort){
draw2d.Port.prototype.onDragEnter.call(this,port);
}else{
if(port instanceof draw2d.LineStartResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof draw2d.Connection&&line.getSource() instanceof draw2d.InputPort){
draw2d.Port.prototype.onDragEnter.call(this,line.getTarget());
}
}else{
if(port instanceof draw2d.LineEndResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof draw2d.Connection&&line.getTarget() instanceof draw2d.InputPort){
draw2d.Port.prototype.onDragEnter.call(this,line.getSource());
}
}
}
}
};
draw2d.InputPort.prototype.onDragLeave=function(port){
if(port instanceof draw2d.OutputPort){
draw2d.Port.prototype.onDragLeave.call(this,port);
}else{
if(port instanceof draw2d.LineStartResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof draw2d.Connection&&line.getSource() instanceof draw2d.InputPort){
draw2d.Port.prototype.onDragLeave.call(this,line.getTarget());
}
}else{
if(port instanceof draw2d.LineEndResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof draw2d.Connection&&line.getTarget() instanceof draw2d.InputPort){
draw2d.Port.prototype.onDragLeave.call(this,line.getSource());
}
}
}
}
};
draw2d.InputPort.prototype.createCommand=function(_5915){
if(_5915.getPolicy()==draw2d.EditPolicy.CONNECT){
if(_5915.source.parentNode.id==_5915.target.parentNode.id){
return null;
}
if(_5915.source instanceof draw2d.OutputPort){
return new draw2d.CommandConnect(_5915.canvas,_5915.source,_5915.target);
}
return null;
}
return draw2d.Port.prototype.createCommand.call(this,_5915);
};
draw2d.OutputPort=function(_5ca6){
draw2d.Port.call(this,_5ca6);
this.maxFanOut=100;
};
draw2d.OutputPort.prototype=new draw2d.Port();
draw2d.OutputPort.prototype.type="draw2d.OutputPort";
draw2d.OutputPort.prototype.onDragEnter=function(port){
if(this.getMaxFanOut()<=this.getFanOut()){
return;
}
if(port instanceof draw2d.InputPort){
draw2d.Port.prototype.onDragEnter.call(this,port);
}else{
if(port instanceof draw2d.LineStartResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof draw2d.Connection&&line.getSource() instanceof draw2d.OutputPort){
draw2d.Port.prototype.onDragEnter.call(this,line.getTarget());
}
}else{
if(port instanceof draw2d.LineEndResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof draw2d.Connection&&line.getTarget() instanceof draw2d.OutputPort){
draw2d.Port.prototype.onDragEnter.call(this,line.getSource());
}
}
}
}
};
draw2d.OutputPort.prototype.onDragLeave=function(port){
if(port instanceof draw2d.InputPort){
draw2d.Port.prototype.onDragLeave.call(this,port);
}else{
if(port instanceof draw2d.LineStartResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof draw2d.Connection&&line.getSource() instanceof draw2d.OutputPort){
draw2d.Port.prototype.onDragLeave.call(this,line.getTarget());
}
}else{
if(port instanceof draw2d.LineEndResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof draw2d.Connection&&line.getTarget() instanceof draw2d.OutputPort){
draw2d.Port.prototype.onDragLeave.call(this,line.getSource());
}
}
}
}
};
draw2d.OutputPort.prototype.onDragstart=function(x,y){
if(!this.canDrag){
return false;
}
if(this.maxFanOut===-1){
return true;
}
if(this.getMaxFanOut()<=this.getFanOut()){
return false;
}
return true;
};
draw2d.OutputPort.prototype.setMaxFanOut=function(count){
this.maxFanOut=count;
};
draw2d.OutputPort.prototype.getMaxFanOut=function(){
return this.maxFanOut;
};
draw2d.OutputPort.prototype.getFanOut=function(){
if(this.getParent().workflow===null){
return 0;
}
var count=0;
var lines=this.getParent().workflow.getLines();
var size=lines.getSize();
for(var i=0;i<size;i++){
var line=lines.get(i);
if(line instanceof draw2d.Connection){
if(line.getSource()==this){
count++;
}else{
if(line.getTarget()==this){
count++;
}
}
}
}
return count;
};
draw2d.OutputPort.prototype.createCommand=function(_5cb3){
if(_5cb3.getPolicy()===draw2d.EditPolicy.CONNECT){
if(_5cb3.source.parentNode.id===_5cb3.target.parentNode.id){
return null;
}
if(_5cb3.source instanceof draw2d.InputPort){
return new draw2d.CommandConnect(_5cb3.canvas,_5cb3.target,_5cb3.source);
}
return null;
}
return draw2d.Port.prototype.createCommand.call(this,_5cb3);
};
draw2d.Line=function(){
this.lineColor=new draw2d.Color(0,0,0);
this.stroke=1;
this.canvas=null;
this.workflow=null;
this.html=null;
this.graphics=null;
this.id=draw2d.UUID.create();
this.startX=30;
this.startY=30;
this.endX=100;
this.endY=100;
this.alpha=1;
this.isMoving=false;
this.model=null;
this.zOrder=draw2d.Line.ZOrderBaseIndex;
this.corona=draw2d.Line.CoronaWidth;
this.properties={};
this.moveListener=new draw2d.ArrayList();
this.setSelectable(true);
this.setDeleteable(true);
};
draw2d.Line.prototype.type="draw2d.Line";
draw2d.Line.ZOrderBaseIndex=200;
draw2d.Line.ZOrderBaseIndex=200;
draw2d.Line.CoronaWidth=5;
draw2d.Line.setZOrderBaseIndex=function(index){
draw2d.Line.ZOrderBaseIndex=index;
};
draw2d.Line.setDefaultCoronaWidth=function(width){
draw2d.Line.CoronaWidth=width;
};
draw2d.Line.prototype.dispose=function(){
this.canvas=null;
this.workflow=null;
if(this.graphics!==null){
this.graphics.clear();
}
this.graphics=null;
};
draw2d.Line.prototype.getZOrder=function(){
return this.zOrder;
};
draw2d.Line.prototype.setZOrder=function(index){
if(this.html!==null){
this.html.style.zIndex=index;
}
this.zOrder=index;
};
draw2d.Line.prototype.setCoronaWidth=function(width){
this.corona=width;
};
draw2d.Line.prototype.createHTMLElement=function(){
var item=document.createElement("div");
item.id=this.id;
item.style.position="absolute";
item.style.left="0px";
item.style.top="0px";
item.style.height="0px";
item.style.width="0px";
item.style.zIndex=this.zOrder;
return item;
};
draw2d.Line.prototype.setId=function(id){
this.id=id;
if(this.html!==null){
this.html.id=id;
}
};
draw2d.Line.prototype.getId=function(){
return this.id;
};
draw2d.Line.prototype.getProperties=function(){
return this.properties;
};
draw2d.Line.prototype.getProperty=function(key){
return this.properties[key];
};
draw2d.Line.prototype.setProperty=function(key,value){
this.properties[key]=value;
this.setDocumentDirty();
};
draw2d.Line.prototype.getHTMLElement=function(){
if(this.html===null){
this.html=this.createHTMLElement();
}
return this.html;
};
draw2d.Line.prototype.getWorkflow=function(){
return this.workflow;
};
draw2d.Line.prototype.isResizeable=function(){
return true;
};
draw2d.Line.prototype.setCanvas=function(_56f0){
this.canvas=_56f0;
if(this.graphics!==null){
this.graphics.clear();
}
this.graphics=null;
};
draw2d.Line.prototype.setWorkflow=function(_56f1){
this.workflow=_56f1;
if(this.graphics!==null){
this.graphics.clear();
}
this.graphics=null;
};
draw2d.Line.prototype.paint=function(){
if(this.html===null){
return;
}
try{
if(this.graphics===null){
this.graphics=new jsGraphics(this.html);
}else{
this.graphics.clear();
}
this.graphics.setStroke(this.stroke);
this.graphics.setColor(this.lineColor.getHTMLStyle());
this.graphics.drawLine(this.startX,this.startY,this.endX,this.endY);
this.graphics.paint();
}
catch(e){
pushErrorStack(e,"draw2d.Line.prototype.paint=function()");
}
};
draw2d.Line.prototype.attachMoveListener=function(_56f2){
this.moveListener.add(_56f2);
};
draw2d.Line.prototype.detachMoveListener=function(_56f3){
this.moveListener.remove(_56f3);
};
draw2d.Line.prototype.fireMoveEvent=function(){
var size=this.moveListener.getSize();
for(var i=0;i<size;i++){
this.moveListener.get(i).onOtherFigureMoved(this);
}
};
draw2d.Line.prototype.onOtherFigureMoved=function(_56f6){
};
draw2d.Line.prototype.setLineWidth=function(w){
this.stroke=w;
if(this.graphics!==null){
this.paint();
}
this.setDocumentDirty();
};
draw2d.Line.prototype.setColor=function(color){
this.lineColor=color;
if(this.graphics!==null){
this.paint();
}
this.setDocumentDirty();
};
draw2d.Line.prototype.getColor=function(){
return this.lineColor;
};
draw2d.Line.prototype.setAlpha=function(_56f9){
if(_56f9==this.alpha){
return;
}
try{
this.html.style.MozOpacity=_56f9;
}
catch(exc1){
}
try{
this.html.style.opacity=_56f9;
}
catch(exc2){
}
try{
var _56fa=Math.round(_56f9*100);
if(_56fa>=99){
this.html.style.filter="";
}else{
this.html.style.filter="alpha(opacity="+_56fa+")";
}
}
catch(exc3){
}
this.alpha=_56f9;
};
draw2d.Line.prototype.setStartPoint=function(x,y){
this.startX=x;
this.startY=y;
if(this.graphics!==null){
this.paint();
}
this.setDocumentDirty();
};
draw2d.Line.prototype.setEndPoint=function(x,y){
this.endX=x;
this.endY=y;
if(this.graphics!==null){
this.paint();
}
this.setDocumentDirty();
};
draw2d.Line.prototype.getStartX=function(){
return this.startX;
};
draw2d.Line.prototype.getStartY=function(){
return this.startY;
};
draw2d.Line.prototype.getStartPoint=function(){
return new draw2d.Point(this.startX,this.startY);
};
draw2d.Line.prototype.getEndX=function(){
return this.endX;
};
draw2d.Line.prototype.getEndY=function(){
return this.endY;
};
draw2d.Line.prototype.getEndPoint=function(){
return new draw2d.Point(this.endX,this.endY);
};
draw2d.Line.prototype.isSelectable=function(){
return this.selectable;
};
draw2d.Line.prototype.setSelectable=function(flag){
this.selectable=flag;
};
draw2d.Line.prototype.isDeleteable=function(){
return this.deleteable;
};
draw2d.Line.prototype.setDeleteable=function(flag){
this.deleteable=flag;
};
draw2d.Line.prototype.getLength=function(){
return Math.sqrt((this.startX-this.endX)*(this.startX-this.endX)+(this.startY-this.endY)*(this.startY-this.endY));
};
draw2d.Line.prototype.getAngle=function(){
var _5701=this.getLength();
var angle=-(180/Math.PI)*Math.asin((this.startY-this.endY)/_5701);
if(angle<0){
if(this.endX<this.startX){
angle=Math.abs(angle)+180;
}else{
angle=360-Math.abs(angle);
}
}else{
if(this.endX<this.startX){
angle=180-angle;
}
}
return angle;
};
draw2d.Line.prototype.createCommand=function(_5703){
if(_5703.getPolicy()==draw2d.EditPolicy.MOVE){
var x1=this.getStartX();
var y1=this.getStartY();
var x2=this.getEndX();
var y2=this.getEndY();
return new draw2d.CommandMoveLine(this,x1,y1,x2,y2);
}
if(_5703.getPolicy()==draw2d.EditPolicy.DELETE){
if(this.isDeleteable()==false){
return null;
}
return new draw2d.CommandDelete(this);
}
return null;
};
draw2d.Line.prototype.setModel=function(model){
if(this.model!==null){
this.model.removePropertyChangeListener(this);
}
this.model=model;
if(this.model!==null){
this.model.addPropertyChangeListener(this);
}
};
draw2d.Line.prototype.getModel=function(){
return this.model;
};
draw2d.Line.prototype.onRemove=function(_5709){
};
draw2d.Line.prototype.onContextMenu=function(x,y){
var menu=this.getContextMenu();
if(menu!==null){
this.workflow.showMenu(menu,x,y);
}
};
draw2d.Line.prototype.getContextMenu=function(){
return null;
};
draw2d.Line.prototype.onDoubleClick=function(){
};
draw2d.Line.prototype.setDocumentDirty=function(){
if(this.workflow!==null){
this.workflow.setDocumentDirty();
}
};
draw2d.Line.prototype.containsPoint=function(px,py){
return draw2d.Line.hit(this.corona,this.startX,this.startY,this.endX,this.endY,px,py);
};
draw2d.Line.hit=function(_570f,X1,Y1,X2,Y2,px,py){
X2-=X1;
Y2-=Y1;
px-=X1;
py-=Y1;
var _5716=px*X2+py*Y2;
var _5717;
if(_5716<=0){
_5717=0;
}else{
px=X2-px;
py=Y2-py;
_5716=px*X2+py*Y2;
if(_5716<=0){
_5717=0;
}else{
_5717=_5716*_5716/(X2*X2+Y2*Y2);
}
}
var lenSq=px*px+py*py-_5717;
if(lenSq<0){
lenSq=0;
}
return Math.sqrt(lenSq)<_570f;
};
draw2d.ConnectionRouter=function(){
};
draw2d.ConnectionRouter.prototype.type="draw2d.ConnectionRouter";
draw2d.ConnectionRouter.prototype.getDirection=function(r,p){
var _5b32=Math.abs(r.x-p.x);
var _5b33=3;
var i=Math.abs(r.y-p.y);
if(i<=_5b32){
_5b32=i;
_5b33=0;
}
i=Math.abs(r.getBottom()-p.y);
if(i<=_5b32){
_5b32=i;
_5b33=2;
}
i=Math.abs(r.getRight()-p.x);
if(i<_5b32){
_5b32=i;
_5b33=1;
}
return _5b33;
};
draw2d.ConnectionRouter.prototype.getEndDirection=function(conn){
var p=conn.getEndPoint();
var rect=conn.getTarget().getParent().getBounds();
return this.getDirection(rect,p);
};
draw2d.ConnectionRouter.prototype.getStartDirection=function(conn){
var p=conn.getStartPoint();
var rect=conn.getSource().getParent().getBounds();
return this.getDirection(rect,p);
};
draw2d.ConnectionRouter.prototype.route=function(_5b3b){
};
draw2d.NullConnectionRouter=function(){
};
draw2d.NullConnectionRouter.prototype=new draw2d.ConnectionRouter();
draw2d.NullConnectionRouter.prototype.type="draw2d.NullConnectionRouter";
draw2d.NullConnectionRouter.prototype.invalidate=function(){
};
draw2d.NullConnectionRouter.prototype.route=function(_4a14){
_4a14.addPoint(_4a14.getStartPoint());
_4a14.addPoint(_4a14.getEndPoint());
};
draw2d.ManhattanConnectionRouter=function(){
this.MINDIST=20;
};
draw2d.ManhattanConnectionRouter.prototype=new draw2d.ConnectionRouter();
draw2d.ManhattanConnectionRouter.prototype.type="draw2d.ManhattanConnectionRouter";
draw2d.ManhattanConnectionRouter.prototype.route=function(conn){
var _5528=conn.getStartPoint();
var _5529=this.getStartDirection(conn);
var toPt=conn.getEndPoint();
var toDir=this.getEndDirection(conn);
this._route(conn,toPt,toDir,_5528,_5529);
};
draw2d.ManhattanConnectionRouter.prototype._route=function(conn,_552d,_552e,toPt,toDir){
var TOL=0.1;
var _5532=0.01;
var UP=0;
var RIGHT=1;
var DOWN=2;
var LEFT=3;
var xDiff=_552d.x-toPt.x;
var yDiff=_552d.y-toPt.y;
var point;
var dir;
if(((xDiff*xDiff)<(_5532))&&((yDiff*yDiff)<(_5532))){
conn.addPoint(new draw2d.Point(toPt.x,toPt.y));
return;
}
if(_552e==LEFT){
if((xDiff>0)&&((yDiff*yDiff)<TOL)&&(toDir===RIGHT)){
point=toPt;
dir=toDir;
}else{
if(xDiff<0){
point=new draw2d.Point(_552d.x-this.MINDIST,_552d.y);
}else{
if(((yDiff>0)&&(toDir===DOWN))||((yDiff<0)&&(toDir==UP))){
point=new draw2d.Point(toPt.x,_552d.y);
}else{
if(_552e==toDir){
var pos=Math.min(_552d.x,toPt.x)-this.MINDIST;
point=new draw2d.Point(pos,_552d.y);
}else{
point=new draw2d.Point(_552d.x-(xDiff/2),_552d.y);
}
}
}
if(yDiff>0){
dir=UP;
}else{
dir=DOWN;
}
}
}else{
if(_552e==RIGHT){
if((xDiff<0)&&((yDiff*yDiff)<TOL)&&(toDir===LEFT)){
point=toPt;
dir=toDir;
}else{
if(xDiff>0){
point=new draw2d.Point(_552d.x+this.MINDIST,_552d.y);
}else{
if(((yDiff>0)&&(toDir===DOWN))||((yDiff<0)&&(toDir===UP))){
point=new draw2d.Point(toPt.x,_552d.y);
}else{
if(_552e==toDir){
var pos=Math.max(_552d.x,toPt.x)+this.MINDIST;
point=new draw2d.Point(pos,_552d.y);
}else{
point=new draw2d.Point(_552d.x-(xDiff/2),_552d.y);
}
}
}
if(yDiff>0){
dir=UP;
}else{
dir=DOWN;
}
}
}else{
if(_552e==DOWN){
if(((xDiff*xDiff)<TOL)&&(yDiff<0)&&(toDir==UP)){
point=toPt;
dir=toDir;
}else{
if(yDiff>0){
point=new draw2d.Point(_552d.x,_552d.y+this.MINDIST);
}else{
if(((xDiff>0)&&(toDir===RIGHT))||((xDiff<0)&&(toDir===LEFT))){
point=new draw2d.Point(_552d.x,toPt.y);
}else{
if(_552e===toDir){
var pos=Math.max(_552d.y,toPt.y)+this.MINDIST;
point=new draw2d.Point(_552d.x,pos);
}else{
point=new draw2d.Point(_552d.x,_552d.y-(yDiff/2));
}
}
}
if(xDiff>0){
dir=LEFT;
}else{
dir=RIGHT;
}
}
}else{
if(_552e==UP){
if(((xDiff*xDiff)<TOL)&&(yDiff>0)&&(toDir===DOWN)){
point=toPt;
dir=toDir;
}else{
if(yDiff<0){
point=new draw2d.Point(_552d.x,_552d.y-this.MINDIST);
}else{
if(((xDiff>0)&&(toDir===RIGHT))||((xDiff<0)&&(toDir===LEFT))){
point=new draw2d.Point(_552d.x,toPt.y);
}else{
if(_552e===toDir){
var pos=Math.min(_552d.y,toPt.y)-this.MINDIST;
point=new draw2d.Point(_552d.x,pos);
}else{
point=new draw2d.Point(_552d.x,_552d.y-(yDiff/2));
}
}
}
if(xDiff>0){
dir=LEFT;
}else{
dir=RIGHT;
}
}
}
}
}
}
this._route(conn,point,dir,toPt,toDir);
conn.addPoint(_552d);
};
draw2d.BezierConnectionRouter=function(_5fa8){
if(!_5fa8){
this.cheapRouter=new draw2d.ManhattanConnectionRouter();
}else{
this.cheapRouter=null;
}
this.iteration=5;
};
draw2d.BezierConnectionRouter.prototype=new draw2d.ConnectionRouter();
draw2d.BezierConnectionRouter.prototype.type="draw2d.BezierConnectionRouter";
draw2d.BezierConnectionRouter.prototype.drawBezier=function(_5fa9,_5faa,t,iter){
var n=_5fa9.length-1;
var q=[];
var _5faf=n+1;
for(var i=0;i<_5faf;i++){
q[i]=[];
q[i][0]=_5fa9[i];
}
for(var j=1;j<=n;j++){
for(var i=0;i<=(n-j);i++){
q[i][j]=new draw2d.Point((1-t)*q[i][j-1].x+t*q[i+1][j-1].x,(1-t)*q[i][j-1].y+t*q[i+1][j-1].y);
}
}
var c1=[];
var c2=[];
for(var i=0;i<n+1;i++){
c1[i]=q[0][i];
c2[i]=q[i][n-i];
}
if(iter>=0){
this.drawBezier(c1,_5faa,t,--iter);
this.drawBezier(c2,_5faa,t,--iter);
}else{
for(var i=0;i<n;i++){
_5faa.push(q[i][n-i]);
}
}
};
draw2d.BezierConnectionRouter.prototype.route=function(conn){
if(this.cheapRouter!==null&&(conn.getSource().getParent().isMoving===true||conn.getTarget().getParent().isMoving===true)){
this.cheapRouter.route(conn);
return;
}
var _5fb5=[];
var _5fb6=conn.getStartPoint();
var toPt=conn.getEndPoint();
this._route(_5fb5,conn,toPt,this.getEndDirection(conn),_5fb6,this.getStartDirection(conn));
var _5fb8=[];
this.drawBezier(_5fb5,_5fb8,0.5,this.iteration);
for(var i=0;i<_5fb8.length;i++){
conn.addPoint(_5fb8[i]);
}
conn.addPoint(toPt);
};
draw2d.BezierConnectionRouter.prototype._route=function(_5fba,conn,_5fbc,_5fbd,toPt,toDir){
var TOL=0.1;
var _5fc1=0.01;
var _5fc2=90;
var UP=0;
var RIGHT=1;
var DOWN=2;
var LEFT=3;
var xDiff=_5fbc.x-toPt.x;
var yDiff=_5fbc.y-toPt.y;
var point;
var dir;
if(((xDiff*xDiff)<(_5fc1))&&((yDiff*yDiff)<(_5fc1))){
_5fba.push(new draw2d.Point(toPt.x,toPt.y));
return;
}
if(_5fbd===LEFT){
if((xDiff>0)&&((yDiff*yDiff)<TOL)&&(toDir===RIGHT)){
point=toPt;
dir=toDir;
}else{
if(xDiff<0){
point=new draw2d.Point(_5fbc.x-_5fc2,_5fbc.y);
}else{
if(((yDiff>0)&&(toDir===DOWN))||((yDiff<0)&&(toDir===UP))){
point=new draw2d.Point(toPt.x,_5fbc.y);
}else{
if(_5fbd===toDir){
var pos=Math.min(_5fbc.x,toPt.x)-_5fc2;
point=new draw2d.Point(pos,_5fbc.y);
}else{
point=new draw2d.Point(_5fbc.x-(xDiff/2),_5fbc.y);
}
}
}
if(yDiff>0){
dir=UP;
}else{
dir=DOWN;
}
}
}else{
if(_5fbd===RIGHT){
if((xDiff<0)&&((yDiff*yDiff)<TOL)&&(toDir==LEFT)){
point=toPt;
dir=toDir;
}else{
if(xDiff>0){
point=new draw2d.Point(_5fbc.x+_5fc2,_5fbc.y);
}else{
if(((yDiff>0)&&(toDir===DOWN))||((yDiff<0)&&(toDir===UP))){
point=new draw2d.Point(toPt.x,_5fbc.y);
}else{
if(_5fbd===toDir){
var pos=Math.max(_5fbc.x,toPt.x)+_5fc2;
point=new draw2d.Point(pos,_5fbc.y);
}else{
point=new draw2d.Point(_5fbc.x-(xDiff/2),_5fbc.y);
}
}
}
if(yDiff>0){
dir=UP;
}else{
dir=DOWN;
}
}
}else{
if(_5fbd===DOWN){
if(((xDiff*xDiff)<TOL)&&(yDiff<0)&&(toDir===UP)){
point=toPt;
dir=toDir;
}else{
if(yDiff>0){
point=new draw2d.Point(_5fbc.x,_5fbc.y+_5fc2);
}else{
if(((xDiff>0)&&(toDir===RIGHT))||((xDiff<0)&&(toDir===LEFT))){
point=new draw2d.Point(_5fbc.x,toPt.y);
}else{
if(_5fbd===toDir){
var pos=Math.max(_5fbc.y,toPt.y)+_5fc2;
point=new draw2d.Point(_5fbc.x,pos);
}else{
point=new draw2d.Point(_5fbc.x,_5fbc.y-(yDiff/2));
}
}
}
if(xDiff>0){
dir=LEFT;
}else{
dir=RIGHT;
}
}
}else{
if(_5fbd===UP){
if(((xDiff*xDiff)<TOL)&&(yDiff>0)&&(toDir===DOWN)){
point=toPt;
dir=toDir;
}else{
if(yDiff<0){
point=new draw2d.Point(_5fbc.x,_5fbc.y-_5fc2);
}else{
if(((xDiff>0)&&(toDir===RIGHT))||((xDiff<0)&&(toDir===LEFT))){
point=new draw2d.Point(_5fbc.x,toPt.y);
}else{
if(_5fbd===toDir){
var pos=Math.min(_5fbc.y,toPt.y)-_5fc2;
point=new draw2d.Point(_5fbc.x,pos);
}else{
point=new draw2d.Point(_5fbc.x,_5fbc.y-(yDiff/2));
}
}
}
if(xDiff>0){
dir=LEFT;
}else{
dir=RIGHT;
}
}
}
}
}
}
this._route(_5fba,conn,point,dir,toPt,toDir);
_5fba.push(_5fbc);
};
draw2d.FanConnectionRouter=function(){
};
draw2d.FanConnectionRouter.prototype=new draw2d.NullConnectionRouter();
draw2d.FanConnectionRouter.prototype.type="draw2d.FanConnectionRouter";
draw2d.FanConnectionRouter.prototype.route=function(conn){
var _5683=conn.getStartPoint();
var toPt=conn.getEndPoint();
var lines=conn.getSource().getConnections();
var _5686=new draw2d.ArrayList();
var index=0;
for(var i=0;i<lines.getSize();i++){
var _5689=lines.get(i);
if(_5689.getTarget()==conn.getTarget()||_5689.getSource()==conn.getTarget()){
_5686.add(_5689);
if(conn==_5689){
index=_5686.getSize();
}
}
}
if(_5686.getSize()>1){
this.routeCollision(conn,index);
}else{
draw2d.NullConnectionRouter.prototype.route.call(this,conn);
}
};
draw2d.FanConnectionRouter.prototype.routeNormal=function(conn){
conn.addPoint(conn.getStartPoint());
conn.addPoint(conn.getEndPoint());
};
draw2d.FanConnectionRouter.prototype.routeCollision=function(conn,index){
var start=conn.getStartPoint();
var end=conn.getEndPoint();
conn.addPoint(start);
var _568f=10;
var _5690=new draw2d.Point((end.x+start.x)/2,(end.y+start.y)/2);
var _5691=end.getPosition(start);
var ray;
if(_5691==draw2d.PositionConstants.SOUTH||_5691==draw2d.PositionConstants.EAST){
ray=new draw2d.Point(end.x-start.x,end.y-start.y);
}else{
ray=new draw2d.Point(start.x-end.x,start.y-end.y);
}
var _5693=Math.sqrt(ray.x*ray.x+ray.y*ray.y);
var _5694=_568f*ray.x/_5693;
var _5695=_568f*ray.y/_5693;
var _5696;
if(index%2===0){
_5696=new draw2d.Point(_5690.x+(index/2)*(-1*_5695),_5690.y+(index/2)*_5694);
}else{
_5696=new draw2d.Point(_5690.x+(index/2)*_5695,_5690.y+(index/2)*(-1*_5694));
}
conn.addPoint(_5696);
conn.addPoint(end);
};
draw2d.Graphics=function(_601d,_601e,_601f){
this.jsGraphics=_601d;
this.xt=_601f.x;
this.yt=_601f.y;
this.radian=_601e*Math.PI/180;
this.sinRadian=Math.sin(this.radian);
this.cosRadian=Math.cos(this.radian);
};
draw2d.Graphics.prototype.setStroke=function(x){
this.jsGraphics.setStroke(x);
};
draw2d.Graphics.prototype.drawLine=function(x1,y1,x2,y2){
var _x1=this.xt+x1*this.cosRadian-y1*this.sinRadian;
var _y1=this.yt+x1*this.sinRadian+y1*this.cosRadian;
var _x2=this.xt+x2*this.cosRadian-y2*this.sinRadian;
var _y2=this.yt+x2*this.sinRadian+y2*this.cosRadian;
this.jsGraphics.drawLine(_x1,_y1,_x2,_y2);
};
draw2d.Graphics.prototype.fillRect=function(x,y,w,h){
var x1=this.xt+x*this.cosRadian-y*this.sinRadian;
var y1=this.yt+x*this.sinRadian+y*this.cosRadian;
var x2=this.xt+(x+w)*this.cosRadian-y*this.sinRadian;
var y2=this.yt+(x+w)*this.sinRadian+y*this.cosRadian;
var x3=this.xt+(x+w)*this.cosRadian-(y+h)*this.sinRadian;
var y3=this.yt+(x+w)*this.sinRadian+(y+h)*this.cosRadian;
var x4=this.xt+x*this.cosRadian-(y+h)*this.sinRadian;
var y4=this.yt+x*this.sinRadian+(y+h)*this.cosRadian;
this.jsGraphics.fillPolygon([x1,x2,x3,x4],[y1,y2,y3,y4]);
};
draw2d.Graphics.prototype.fillPolygon=function(_6035,_6036){
var rotX=[];
var rotY=[];
for(var i=0;i<_6035.length;i++){
rotX[i]=this.xt+_6035[i]*this.cosRadian-_6036[i]*this.sinRadian;
rotY[i]=this.yt+_6035[i]*this.sinRadian+_6036[i]*this.cosRadian;
}
this.jsGraphics.fillPolygon(rotX,rotY);
};
draw2d.Graphics.prototype.setColor=function(color){
this.jsGraphics.setColor(color.getHTMLStyle());
};
draw2d.Graphics.prototype.drawPolygon=function(_603b,_603c){
var rotX=[];
var rotY=[];
for(var i=0;i<_603b.length;i++){
rotX[i]=this.xt+_603b[i]*this.cosRadian-_603c[i]*this.sinRadian;
rotY[i]=this.yt+_603b[i]*this.sinRadian+_603c[i]*this.cosRadian;
}
this.jsGraphics.drawPolygon(rotX,rotY);
};
draw2d.Connection=function(){
draw2d.Line.call(this);
this.sourcePort=null;
this.targetPort=null;
this.canDrag=true;
this.sourceDecorator=null;
this.targetDecorator=null;
this.sourceAnchor=new draw2d.ConnectionAnchor();
this.targetAnchor=new draw2d.ConnectionAnchor();
this.router=draw2d.Connection.defaultRouter;
this.lineSegments=new draw2d.ArrayList();
this.children=new draw2d.ArrayList();
this.setColor(new draw2d.Color(0,0,115));
this.setLineWidth(1);
};
draw2d.Connection.prototype=new draw2d.Line();
draw2d.Connection.prototype.type="draw2d.Connection";
draw2d.Connection.defaultRouter=new draw2d.ManhattanConnectionRouter();
draw2d.Connection.setDefaultRouter=function(_58c0){
draw2d.Connection.defaultRouter=_58c0;
};
draw2d.Connection.prototype.disconnect=function(){
if(this.sourcePort!==null){
this.sourcePort.detachMoveListener(this);
this.fireSourcePortRouteEvent();
}
if(this.targetPort!==null){
this.targetPort.detachMoveListener(this);
this.fireTargetPortRouteEvent();
}
};
draw2d.Connection.prototype.reconnect=function(){
if(this.sourcePort!==null){
this.sourcePort.attachMoveListener(this);
this.fireSourcePortRouteEvent();
}
if(this.targetPort!==null){
this.targetPort.attachMoveListener(this);
this.fireTargetPortRouteEvent();
}
};
draw2d.Connection.prototype.isResizeable=function(){
return this.getCanDrag();
};
draw2d.Connection.prototype.setCanDrag=function(flag){
this.canDrag=flag;
};
draw2d.Connection.prototype.getCanDrag=function(){
return this.canDrag;
};
draw2d.Connection.prototype.addFigure=function(_58c2,_58c3){
var entry={};
entry.figure=_58c2;
entry.locator=_58c3;
this.children.add(entry);
if(this.graphics!==null){
this.paint();
}
var oThis=this;
var _58c6=function(){
var _58c7=arguments[0]||window.event;
_58c7.returnValue=false;
oThis.getWorkflow().setCurrentSelection(oThis);
oThis.getWorkflow().showLineResizeHandles(oThis);
};
if(_58c2.getHTMLElement().addEventListener){
_58c2.getHTMLElement().addEventListener("mousedown",_58c6,false);
}else{
if(_58c2.getHTMLElement().attachEvent){
_58c2.getHTMLElement().attachEvent("onmousedown",_58c6);
}
}
};
draw2d.Connection.prototype.setSourceDecorator=function(_58c8){
this.sourceDecorator=_58c8;
if(this.graphics!==null){
this.paint();
}
};
draw2d.Connection.prototype.getSourceDecorator=function(){
return this.sourceDecorator;
};
draw2d.Connection.prototype.setTargetDecorator=function(_58c9){
this.targetDecorator=_58c9;
if(this.graphics!==null){
this.paint();
}
};
draw2d.Connection.prototype.getTargetDecorator=function(){
return this.targetDecorator;
};
draw2d.Connection.prototype.setSourceAnchor=function(_58ca){
this.sourceAnchor=_58ca;
this.sourceAnchor.setOwner(this.sourcePort);
if(this.graphics!==null){
this.paint();
}
};
draw2d.Connection.prototype.setTargetAnchor=function(_58cb){
this.targetAnchor=_58cb;
this.targetAnchor.setOwner(this.targetPort);
if(this.graphics!==null){
this.paint();
}
};
draw2d.Connection.prototype.setRouter=function(_58cc){
if(_58cc!==null){
this.router=_58cc;
}else{
this.router=new draw2d.NullConnectionRouter();
}
if(this.graphics!==null){
this.paint();
}
};
draw2d.Connection.prototype.getRouter=function(){
return this.router;
};
draw2d.Connection.prototype.setWorkflow=function(_58cd){
draw2d.Line.prototype.setWorkflow.call(this,_58cd);
for(var i=0;i<this.children.getSize();i++){
this.children.get(i).isAppended=false;
}
};
draw2d.Connection.prototype.paint=function(){
if(this.html===null){
return;
}
try{
for(var i=0;i<this.children.getSize();i++){
var entry=this.children.get(i);
if(entry.isAppended==true){
this.html.removeChild(entry.figure.getHTMLElement());
}
entry.isAppended=false;
}
if(this.graphics===null){
this.graphics=new jsGraphics(this.html);
}else{
this.graphics.clear();
}
this.graphics.setStroke(this.stroke);
this.graphics.setColor(this.lineColor.getHTMLStyle());
this.startStroke();
this.router.route(this);
if(this.getSource().getParent().isMoving==false&&this.getTarget().getParent().isMoving==false){
if(this.targetDecorator!==null){
this.targetDecorator.paint(new draw2d.Graphics(this.graphics,this.getEndAngle(),this.getEndPoint()));
}
if(this.sourceDecorator!==null){
this.sourceDecorator.paint(new draw2d.Graphics(this.graphics,this.getStartAngle(),this.getStartPoint()));
}
}
this.finishStroke();
for(var i=0;i<this.children.getSize();i++){
var entry=this.children.get(i);
this.html.appendChild(entry.figure.getHTMLElement());
entry.isAppended=true;
entry.locator.relocate(entry.figure);
}
}
catch(e){
pushErrorStack(e,"draw2d.Connection.prototype.paint=function()");
}
};
draw2d.Connection.prototype.getStartPoint=function(){
if(this.isMoving==false){
return this.sourceAnchor.getLocation(this.targetAnchor.getReferencePoint());
}else{
return draw2d.Line.prototype.getStartPoint.call(this);
}
};
draw2d.Connection.prototype.getEndPoint=function(){
if(this.isMoving==false){
return this.targetAnchor.getLocation(this.sourceAnchor.getReferencePoint());
}else{
return draw2d.Line.prototype.getEndPoint.call(this);
}
};
draw2d.Connection.prototype.startStroke=function(){
this.oldPoint=null;
this.lineSegments=new draw2d.ArrayList();
};
draw2d.Connection.prototype.finishStroke=function(){
this.graphics.paint();
this.oldPoint=null;
};
draw2d.Connection.prototype.getPoints=function(){
var _58d1=new draw2d.ArrayList();
var line=null;
for(var i=0;i<this.lineSegments.getSize();i++){
line=this.lineSegments.get(i);
_58d1.add(line.start);
}
if(line!==null){
_58d1.add(line.end);
}
return _58d1;
};
draw2d.Connection.prototype.addPoint=function(p){
p=new draw2d.Point(parseInt(p.x),parseInt(p.y));
if(this.oldPoint!==null){
this.graphics.drawLine(this.oldPoint.x,this.oldPoint.y,p.x,p.y);
var line={};
line.start=this.oldPoint;
line.end=p;
this.lineSegments.add(line);
}
this.oldPoint={};
this.oldPoint.x=p.x;
this.oldPoint.y=p.y;
};
draw2d.Connection.prototype.refreshSourcePort=function(){
var model=this.getModel().getSourceModel();
var _58d7=this.getModel().getSourcePortName();
var _58d8=this.getWorkflow().getDocument().getFigures();
var count=_58d8.getSize();
for(var i=0;i<count;i++){
var _58db=_58d8.get(i);
if(_58db.getModel()==model){
var port=_58db.getOutputPort(_58d7);
this.setSource(port);
}
}
this.setRouter(this.getRouter());
};
draw2d.Connection.prototype.refreshTargetPort=function(){
var model=this.getModel().getTargetModel();
var _58de=this.getModel().getTargetPortName();
var _58df=this.getWorkflow().getDocument().getFigures();
var count=_58df.getSize();
for(var i=0;i<count;i++){
var _58e2=_58df.get(i);
if(_58e2.getModel()==model){
var port=_58e2.getInputPort(_58de);
this.setTarget(port);
}
}
this.setRouter(this.getRouter());
};
draw2d.Connection.prototype.setSource=function(port){
if(this.sourcePort!==null){
this.sourcePort.detachMoveListener(this);
}
this.sourcePort=port;
if(this.sourcePort===null){
return;
}
this.sourceAnchor.setOwner(this.sourcePort);
this.fireSourcePortRouteEvent();
this.sourcePort.attachMoveListener(this);
this.setStartPoint(port.getAbsoluteX(),port.getAbsoluteY());
};
draw2d.Connection.prototype.getSource=function(){
return this.sourcePort;
};
draw2d.Connection.prototype.setTarget=function(port){
if(this.targetPort!==null){
this.targetPort.detachMoveListener(this);
}
this.targetPort=port;
if(this.targetPort===null){
return;
}
this.targetAnchor.setOwner(this.targetPort);
this.fireTargetPortRouteEvent();
this.targetPort.attachMoveListener(this);
this.setEndPoint(port.getAbsoluteX(),port.getAbsoluteY());
};
draw2d.Connection.prototype.getTarget=function(){
return this.targetPort;
};
draw2d.Connection.prototype.onOtherFigureMoved=function(_58e6){
if(_58e6==this.sourcePort){
this.setStartPoint(this.sourcePort.getAbsoluteX(),this.sourcePort.getAbsoluteY());
}else{
this.setEndPoint(this.targetPort.getAbsoluteX(),this.targetPort.getAbsoluteY());
}
};
draw2d.Connection.prototype.containsPoint=function(px,py){
for(var i=0;i<this.lineSegments.getSize();i++){
var line=this.lineSegments.get(i);
if(draw2d.Line.hit(this.corona,line.start.x,line.start.y,line.end.x,line.end.y,px,py)){
return true;
}
}
return false;
};
draw2d.Connection.prototype.getStartAngle=function(){
var p1=this.lineSegments.get(0).start;
var p2=this.lineSegments.get(0).end;
if(this.router instanceof draw2d.BezierConnectionRouter){
p2=this.lineSegments.get(5).end;
}
var _58ed=Math.sqrt((p1.x-p2.x)*(p1.x-p2.x)+(p1.y-p2.y)*(p1.y-p2.y));
var angle=-(180/Math.PI)*Math.asin((p1.y-p2.y)/_58ed);
if(angle<0){
if(p2.x<p1.x){
angle=Math.abs(angle)+180;
}else{
angle=360-Math.abs(angle);
}
}else{
if(p2.x<p1.x){
angle=180-angle;
}
}
return angle;
};
draw2d.Connection.prototype.getEndAngle=function(){
if(this.lineSegments.getSize()===0){
return 90;
}
var p1=this.lineSegments.get(this.lineSegments.getSize()-1).end;
var p2=this.lineSegments.get(this.lineSegments.getSize()-1).start;
if(this.router instanceof draw2d.BezierConnectionRouter){
p2=this.lineSegments.get(this.lineSegments.getSize()-5).end;
}
var _58f1=Math.sqrt((p1.x-p2.x)*(p1.x-p2.x)+(p1.y-p2.y)*(p1.y-p2.y));
var angle=-(180/Math.PI)*Math.asin((p1.y-p2.y)/_58f1);
if(angle<0){
if(p2.x<p1.x){
angle=Math.abs(angle)+180;
}else{
angle=360-Math.abs(angle);
}
}else{
if(p2.x<p1.x){
angle=180-angle;
}
}
return angle;
};
draw2d.Connection.prototype.fireSourcePortRouteEvent=function(){
var _58f3=this.sourcePort.getConnections();
for(var i=0;i<_58f3.getSize();i++){
_58f3.get(i).paint();
}
};
draw2d.Connection.prototype.fireTargetPortRouteEvent=function(){
var _58f5=this.targetPort.getConnections();
for(var i=0;i<_58f5.getSize();i++){
_58f5.get(i).paint();
}
};
draw2d.Connection.prototype.createCommand=function(_58f7){
if(_58f7.getPolicy()==draw2d.EditPolicy.MOVE){
return new draw2d.CommandReconnect(this);
}
if(_58f7.getPolicy()==draw2d.EditPolicy.DELETE){
if(this.isDeleteable()==true){
return new draw2d.CommandDelete(this);
}
return null;
}
return null;
};
draw2d.ConnectionAnchor=function(owner){
this.owner=owner;
};
draw2d.ConnectionAnchor.prototype.type="draw2d.ConnectionAnchor";
draw2d.ConnectionAnchor.prototype.getLocation=function(_5904){
return this.getReferencePoint();
};
draw2d.ConnectionAnchor.prototype.getOwner=function(){
return this.owner;
};
draw2d.ConnectionAnchor.prototype.setOwner=function(owner){
this.owner=owner;
};
draw2d.ConnectionAnchor.prototype.getBox=function(){
return this.getOwner().getAbsoluteBounds();
};
draw2d.ConnectionAnchor.prototype.getReferencePoint=function(){
if(this.getOwner()===null){
return null;
}else{
return this.getOwner().getAbsolutePosition();
}
};
draw2d.ChopboxConnectionAnchor=function(owner){
draw2d.ConnectionAnchor.call(this,owner);
};
draw2d.ChopboxConnectionAnchor.prototype=new draw2d.ConnectionAnchor();
draw2d.ChopboxConnectionAnchor.prototype.type="draw2d.ChopboxConnectionAnchor";
draw2d.ChopboxConnectionAnchor.prototype.getLocation=function(_500c){
var r=new draw2d.Dimension();
r.setBounds(this.getBox());
r.translate(-1,-1);
r.resize(1,1);
var _500e=r.x+r.w/2;
var _500f=r.y+r.h/2;
if(r.isEmpty()||(_500c.x==_500e&&_500c.y==_500f)){
return new Point(_500e,_500f);
}
var dx=_500c.x-_500e;
var dy=_500c.y-_500f;
var scale=0.5/Math.max(Math.abs(dx)/r.w,Math.abs(dy)/r.h);
dx*=scale;
dy*=scale;
_500e+=dx;
_500f+=dy;
return new draw2d.Point(Math.round(_500e),Math.round(_500f));
};
draw2d.ChopboxConnectionAnchor.prototype.getBox=function(){
return this.getOwner().getParent().getBounds();
};
draw2d.ChopboxConnectionAnchor.prototype.getReferencePoint=function(){
return this.getBox().getCenter();
};
draw2d.ConnectionDecorator=function(){
this.color=new draw2d.Color(0,0,0);
this.backgroundColor=new draw2d.Color(250,250,250);
};
draw2d.ConnectionDecorator.prototype.type="draw2d.ConnectionDecorator";
draw2d.ConnectionDecorator.prototype.paint=function(g){
};
draw2d.ConnectionDecorator.prototype.setColor=function(c){
this.color=c;
};
draw2d.ConnectionDecorator.prototype.setBackgroundColor=function(c){
this.backgroundColor=c;
};
draw2d.ArrowConnectionDecorator=function(_4b41,width){
draw2d.ConnectionDecorator.call(this);
if(_4b41===undefined||_4b41<1){
this.lenght=15;
}
if(width===undefined||width<1){
this.width=10;
}
};
draw2d.ArrowConnectionDecorator.prototype=new draw2d.ConnectionDecorator();
draw2d.ArrowConnectionDecorator.prototype.type="draw2d.ArrowConnectionDecorator";
draw2d.ArrowConnectionDecorator.prototype.paint=function(g){
if(this.backgroundColor!==null){
g.setColor(this.backgroundColor);
g.fillPolygon([3,this.lenght,this.lenght,3],[0,(this.width/2),-(this.width/2),0]);
}
g.setColor(this.color);
g.setStroke(1);
g.drawPolygon([3,this.lenght,this.lenght,3],[0,(this.width/2),-(this.width/2),0]);
};
draw2d.ArrowConnectionDecorator.prototype.setDimension=function(l,width){
this.width=w;
this.lenght=l;
};
draw2d.CompartmentFigure=function(){
draw2d.Node.call(this);
this.children=new draw2d.ArrayList();
this.setBorder(new draw2d.LineBorder(1));
this.dropable=new draw2d.DropTarget(this.html);
this.dropable.node=this;
this.dropable.addEventListener("figureenter",function(_4b0d){
_4b0d.target.node.onFigureEnter(_4b0d.relatedTarget.node);
});
this.dropable.addEventListener("figureleave",function(_4b0e){
_4b0e.target.node.onFigureLeave(_4b0e.relatedTarget.node);
});
this.dropable.addEventListener("figuredrop",function(_4b0f){
_4b0f.target.node.onFigureDrop(_4b0f.relatedTarget.node);
});
};
draw2d.CompartmentFigure.prototype=new draw2d.Node();
draw2d.CompartmentFigure.prototype.type="draw2d.CompartmentFigure";
draw2d.CompartmentFigure.prototype.onFigureEnter=function(_4b10){
};
draw2d.CompartmentFigure.prototype.onFigureLeave=function(_4b11){
};
draw2d.CompartmentFigure.prototype.onFigureDrop=function(_4b12){
};
draw2d.CompartmentFigure.prototype.getChildren=function(){
return this.children;
};
draw2d.CompartmentFigure.prototype.addChild=function(_4b13){
_4b13.setZOrder(this.getZOrder()+1);
_4b13.setParent(this);
this.children.add(_4b13);
};
draw2d.CompartmentFigure.prototype.removeChild=function(_4b14){
_4b14.setParent(null);
this.children.remove(_4b14);
};
draw2d.CompartmentFigure.prototype.setZOrder=function(index){
draw2d.Node.prototype.setZOrder.call(this,index);
for(var i=0;i<this.children.getSize();i++){
this.children.get(i).setZOrder(index+1);
}
};
draw2d.CompartmentFigure.prototype.setPosition=function(xPos,yPos){
var oldX=this.getX();
var oldY=this.getY();
draw2d.Node.prototype.setPosition.call(this,xPos,yPos);
for(var i=0;i<this.children.getSize();i++){
var child=this.children.get(i);
child.setPosition(child.getX()+this.getX()-oldX,child.getY()+this.getY()-oldY);
}
};
draw2d.CompartmentFigure.prototype.onDrag=function(){
var oldX=this.getX();
var oldY=this.getY();
draw2d.Node.prototype.onDrag.call(this);
for(var i=0;i<this.children.getSize();i++){
var child=this.children.get(i);
child.setPosition(child.getX()+this.getX()-oldX,child.getY()+this.getY()-oldY);
}
};
draw2d.CanvasDocument=function(_56ab){
this.canvas=_56ab;
};
draw2d.CanvasDocument.prototype.type="draw2d.CanvasDocument";
draw2d.CanvasDocument.prototype.getFigures=function(){
var _56ac=new draw2d.ArrayList();
var _56ad=this.canvas.figures;
var _56ae=this.canvas.dialogs;
for(var i=0;i<_56ad.getSize();i++){
var _56b0=_56ad.get(i);
if(_56ae.indexOf(_56b0)==-1&&_56b0.getParent()===null&&!(_56b0 instanceof draw2d.WindowFigure)){
_56ac.add(_56b0);
}
}
return _56ac;
};
draw2d.CanvasDocument.prototype.getFigure=function(id){
return this.canvas.getFigure(id);
};
draw2d.CanvasDocument.prototype.getLines=function(){
return this.canvas.getLines();
};
draw2d.CanvasDocument.prototype.getLine=function(id){
return this.canvas.getLine(id);
};
draw2d.Annotation=function(msg){
this.msg=msg;
this.color=new draw2d.Color(0,0,0);
this.bgColor=new draw2d.Color(241,241,121);
this.fontSize=10;
this.textNode=null;
draw2d.Figure.call(this);
};
draw2d.Annotation.prototype=new draw2d.Figure();
draw2d.Annotation.prototype.type="draw2d.Annotation";
draw2d.Annotation.prototype.createHTMLElement=function(){
var item=draw2d.Figure.prototype.createHTMLElement.call(this);
item.style.color=this.color.getHTMLStyle();
item.style.backgroundColor=this.bgColor.getHTMLStyle();
item.style.fontSize=this.fontSize+"pt";
item.style.width="auto";
item.style.height="auto";
item.style.margin="0px";
item.style.padding="0px";
item.onselectstart=function(){
return false;
};
item.unselectable="on";
item.style.cursor="default";
this.textNode=document.createTextNode(this.msg);
item.appendChild(this.textNode);
this.disableTextSelection(item);
return item;
};
draw2d.Annotation.prototype.onDoubleClick=function(){
var _57d3=new draw2d.AnnotationDialog(this);
this.workflow.showDialog(_57d3);
};
draw2d.Annotation.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.bgColor!==null){
this.html.style.backgroundColor=this.bgColor.getHTMLStyle();
}else{
this.html.style.backgroundColor="transparent";
}
};
draw2d.Annotation.prototype.getBackgroundColor=function(){
return this.bgColor;
};
draw2d.Annotation.prototype.setFontSize=function(size){
this.fontSize=size;
this.html.style.fontSize=this.fontSize+"pt";
};
draw2d.Annotation.prototype.getText=function(){
return this.msg;
};
draw2d.Annotation.prototype.setText=function(text){
this.msg=text;
this.html.removeChild(this.textNode);
this.textNode=document.createTextNode(this.msg);
this.html.appendChild(this.textNode);
};
draw2d.Annotation.prototype.setStyledText=function(text){
this.msg=text;
this.html.removeChild(this.textNode);
this.textNode=document.createElement("div");
this.textNode.innerHTML=text;
this.html.appendChild(this.textNode);
};
draw2d.ResizeHandle=function(_57e7,type){
draw2d.Rectangle.call(this,5,5);
this.type=type;
var _57e9=this.getWidth();
var _57ea=_57e9/2;
switch(this.type){
case 1:
this.setSnapToGridAnchor(new draw2d.Point(_57e9,_57e9));
break;
case 2:
this.setSnapToGridAnchor(new draw2d.Point(_57ea,_57e9));
break;
case 3:
this.setSnapToGridAnchor(new draw2d.Point(0,_57e9));
break;
case 4:
this.setSnapToGridAnchor(new draw2d.Point(0,_57ea));
break;
case 5:
this.setSnapToGridAnchor(new draw2d.Point(0,0));
break;
case 6:
this.setSnapToGridAnchor(new draw2d.Point(_57ea,0));
break;
case 7:
this.setSnapToGridAnchor(new draw2d.Point(_57e9,0));
break;
case 8:
this.setSnapToGridAnchor(new draw2d.Point(_57e9,_57ea));
case 9:
this.setSnapToGridAnchor(new draw2d.Point(_57ea,_57ea));
break;
}
this.setBackgroundColor(new draw2d.Color(0,255,0));
this.setWorkflow(_57e7);
this.setZOrder(10000);
};
draw2d.ResizeHandle.prototype=new draw2d.Rectangle();
draw2d.ResizeHandle.prototype.type="draw2d.ResizeHandle";
draw2d.ResizeHandle.prototype.getSnapToDirection=function(){
switch(this.type){
case 1:
return draw2d.SnapToHelper.NORTH_WEST;
case 2:
return draw2d.SnapToHelper.NORTH;
case 3:
return draw2d.SnapToHelper.NORTH_EAST;
case 4:
return draw2d.SnapToHelper.EAST;
case 5:
return draw2d.SnapToHelper.SOUTH_EAST;
case 6:
return draw2d.SnapToHelper.SOUTH;
case 7:
return draw2d.SnapToHelper.SOUTH_WEST;
case 8:
return draw2d.SnapToHelper.WEST;
case 9:
return draw2d.SnapToHelper.CENTER;
}
};
draw2d.ResizeHandle.prototype.onDragend=function(){
var _57eb=this.workflow.currentSelection;
if(this.commandMove!==null){
this.commandMove.setPosition(_57eb.getX(),_57eb.getY());
this.workflow.getCommandStack().execute(this.commandMove);
this.commandMove=null;
}
if(this.commandResize!==null){
this.commandResize.setDimension(_57eb.getWidth(),_57eb.getHeight());
this.workflow.getCommandStack().execute(this.commandResize);
this.commandResize=null;
}
this.workflow.hideSnapToHelperLines();
};
draw2d.ResizeHandle.prototype.setPosition=function(xPos,yPos){
this.x=xPos;
this.y=yPos;
if(this.html===null){
return;
}
this.html.style.left=this.x+"px";
this.html.style.top=this.y+"px";
};
draw2d.ResizeHandle.prototype.onDragstart=function(x,y){
if(!this.canDrag){
return false;
}
var _57f0=this.workflow.currentSelection;
this.commandMove=_57f0.createCommand(new draw2d.EditPolicy(draw2d.EditPolicy.MOVE));
this.commandResize=_57f0.createCommand(new draw2d.EditPolicy(draw2d.EditPolicy.RESIZE));
return true;
};
draw2d.ResizeHandle.prototype.onDrag=function(){
var oldX=this.getX();
var oldY=this.getY();
draw2d.Rectangle.prototype.onDrag.call(this);
var diffX=oldX-this.getX();
var diffY=oldY-this.getY();
var _57f5=this.workflow.currentSelection.getX();
var _57f6=this.workflow.currentSelection.getY();
var _57f7=this.workflow.currentSelection.getWidth();
var _57f8=this.workflow.currentSelection.getHeight();
switch(this.type){
case 1:
this.workflow.currentSelection.setPosition(_57f5-diffX,_57f6-diffY);
this.workflow.currentSelection.setDimension(_57f7+diffX,_57f8+diffY);
break;
case 2:
this.workflow.currentSelection.setPosition(_57f5,_57f6-diffY);
this.workflow.currentSelection.setDimension(_57f7,_57f8+diffY);
break;
case 3:
this.workflow.currentSelection.setPosition(_57f5,_57f6-diffY);
this.workflow.currentSelection.setDimension(_57f7-diffX,_57f8+diffY);
break;
case 4:
this.workflow.currentSelection.setPosition(_57f5,_57f6);
this.workflow.currentSelection.setDimension(_57f7-diffX,_57f8);
break;
case 5:
this.workflow.currentSelection.setPosition(_57f5,_57f6);
this.workflow.currentSelection.setDimension(_57f7-diffX,_57f8-diffY);
break;
case 6:
this.workflow.currentSelection.setPosition(_57f5,_57f6);
this.workflow.currentSelection.setDimension(_57f7,_57f8-diffY);
break;
case 7:
this.workflow.currentSelection.setPosition(_57f5-diffX,_57f6);
this.workflow.currentSelection.setDimension(_57f7+diffX,_57f8-diffY);
break;
case 8:
this.workflow.currentSelection.setPosition(_57f5-diffX,_57f6);
this.workflow.currentSelection.setDimension(_57f7+diffX,_57f8);
break;
}
this.workflow.moveResizeHandles(this.workflow.getCurrentSelection());
};
draw2d.ResizeHandle.prototype.setCanDrag=function(flag){
draw2d.Rectangle.prototype.setCanDrag.call(this,flag);
if(this.html===null){
return;
}
if(!flag){
this.html.style.cursor="";
return;
}
switch(this.type){
case 1:
this.html.style.cursor="nw-resize";
break;
case 2:
this.html.style.cursor="s-resize";
break;
case 3:
this.html.style.cursor="ne-resize";
break;
case 4:
this.html.style.cursor="w-resize";
break;
case 5:
this.html.style.cursor="se-resize";
break;
case 6:
this.html.style.cursor="n-resize";
break;
case 7:
this.html.style.cursor="sw-resize";
break;
case 8:
this.html.style.cursor="e-resize";
break;
case 9:
this.html.style.cursor="resize";
break;
}
};
draw2d.ResizeHandle.prototype.onKeyDown=function(_57fa,ctrl){
this.workflow.onKeyDown(_57fa,ctrl);
};
draw2d.ResizeHandle.prototype.fireMoveEvent=function(){
};
draw2d.LineStartResizeHandle=function(_5b22){
draw2d.ResizeHandle.call(this,_5b22,9);
this.setDimension(10,10);
this.setBackgroundColor(new draw2d.Color(100,255,0));
this.setZOrder(10000);
};
draw2d.LineStartResizeHandle.prototype=new draw2d.ResizeHandle();
draw2d.LineStartResizeHandle.prototype.type="draw2d.LineStartResizeHandle";
draw2d.LineStartResizeHandle.prototype.onDragend=function(){
if(this.workflow.currentSelection instanceof draw2d.Connection){
if(this.command!==null){
this.command.cancel();
}
}else{
if(this.command!==null){
this.getWorkflow().getCommandStack().execute(this.command);
}
}
this.command=null;
};
draw2d.LineStartResizeHandle.prototype.onDragstart=function(x,y){
if(!this.canDrag){
return false;
}
this.command=this.workflow.currentSelection.createCommand(new draw2d.EditPolicy(draw2d.EditPolicy.MOVE));
return this.command!==null;
};
draw2d.LineStartResizeHandle.prototype.onDrag=function(){
var oldX=this.getX();
var oldY=this.getY();
draw2d.Rectangle.prototype.onDrag.call(this);
var diffX=oldX-this.getX();
var diffY=oldY-this.getY();
var _5b29=this.workflow.currentSelection.getStartPoint();
var line=this.workflow.currentSelection;
line.setStartPoint(_5b29.x-diffX,_5b29.y-diffY);
line.isMoving=true;
};
draw2d.LineStartResizeHandle.prototype.onDrop=function(_5b2b){
var line=this.workflow.currentSelection;
line.isMoving=false;
if(line instanceof draw2d.Connection){
this.command.setNewPorts(_5b2b,line.getTarget());
this.getWorkflow().getCommandStack().execute(this.command);
}
this.command=null;
};
draw2d.LineEndResizeHandle=function(_58f8){
draw2d.ResizeHandle.call(this,_58f8,9);
this.setDimension(10,10);
this.setBackgroundColor(new draw2d.Color(0,255,0));
this.setZOrder(10000);
};
draw2d.LineEndResizeHandle.prototype=new draw2d.ResizeHandle();
draw2d.LineEndResizeHandle.prototype.type="draw2d.LineEndResizeHandle";
draw2d.LineEndResizeHandle.prototype.onDragend=function(){
if(this.workflow.currentSelection instanceof draw2d.Connection){
if(this.command!==null){
this.command.cancel();
}
}else{
if(this.command!==null){
this.workflow.getCommandStack().execute(this.command);
}
}
this.command=null;
};
draw2d.LineEndResizeHandle.prototype.onDragstart=function(x,y){
if(!this.canDrag){
return false;
}
this.command=this.workflow.currentSelection.createCommand(new draw2d.EditPolicy(draw2d.EditPolicy.MOVE));
return this.command!==null;
};
draw2d.LineEndResizeHandle.prototype.onDrag=function(){
var oldX=this.getX();
var oldY=this.getY();
draw2d.Rectangle.prototype.onDrag.call(this);
var diffX=oldX-this.getX();
var diffY=oldY-this.getY();
var _58ff=this.workflow.currentSelection.getEndPoint();
var line=this.workflow.currentSelection;
line.setEndPoint(_58ff.x-diffX,_58ff.y-diffY);
line.isMoving=true;
};
draw2d.LineEndResizeHandle.prototype.onDrop=function(_5901){
var line=this.workflow.currentSelection;
line.isMoving=false;
if(line instanceof draw2d.Connection){
this.command.setNewPorts(line.getSource(),_5901);
this.getWorkflow().getCommandStack().execute(this.command);
}
this.command=null;
};
draw2d.Canvas=function(_5023){
try{
if(_5023){
this.construct(_5023);
}
this.enableSmoothFigureHandling=false;
this.canvasLines=new draw2d.ArrayList();
}
catch(e){
pushErrorStack(e,"draw2d.Canvas=function(/*:String*/id)");
}
};
draw2d.Canvas.IMAGE_BASE_URL="";
draw2d.Canvas.prototype.type="draw2d.Canvas";
draw2d.Canvas.prototype.construct=function(_5024){
this.canvasId=_5024;
this.html=document.getElementById(this.canvasId);
this.scrollArea=document.body.parentNode;
};
draw2d.Canvas.prototype.setViewPort=function(divId){
this.scrollArea=document.getElementById(divId);
};
draw2d.Canvas.prototype.addFigure=function(_5026,xPos,yPos,_5029){
try{
if(this.enableSmoothFigureHandling===true){
if(_5026.timer<=0){
_5026.setAlpha(0.001);
}
var _502a=_5026;
var _502b=function(){
if(_502a.alpha<1){
_502a.setAlpha(Math.min(1,_502a.alpha+0.05));
}else{
window.clearInterval(_502a.timer);
_502a.timer=-1;
}
};
if(_502a.timer>0){
window.clearInterval(_502a.timer);
}
_502a.timer=window.setInterval(_502b,30);
}
_5026.setCanvas(this);
if(xPos&&yPos){
_5026.setPosition(xPos,yPos);
}
if(_5026 instanceof draw2d.Line){
this.canvasLines.add(_5026);
this.html.appendChild(_5026.getHTMLElement());
}else{
var obj=this.canvasLines.getFirstElement();
if(obj===null){
this.html.appendChild(_5026.getHTMLElement());
}else{
this.html.insertBefore(_5026.getHTMLElement(),obj.getHTMLElement());
}
}
if(!_5029){
_5026.paint();
}
}
catch(e){
pushErrorStack(e,"draw2d.Canvas.prototype.addFigure= function( /*:draw2d.Figure*/figure,/*:int*/ xPos,/*:int*/ yPos, /*:boolean*/ avoidPaint)");
}
};
draw2d.Canvas.prototype.removeFigure=function(_502d){
if(this.enableSmoothFigureHandling===true){
var oThis=this;
var _502f=_502d;
var _5030=function(){
if(_502f.alpha>0){
_502f.setAlpha(Math.max(0,_502f.alpha-0.05));
}else{
window.clearInterval(_502f.timer);
_502f.timer=-1;
oThis.html.removeChild(_502f.html);
_502f.setCanvas(null);
}
};
if(_502f.timer>0){
window.clearInterval(_502f.timer);
}
_502f.timer=window.setInterval(_5030,20);
}else{
this.html.removeChild(_502d.html);
_502d.setCanvas(null);
}
if(_502d instanceof draw2d.Line){
this.canvasLines.remove(_502d);
}
};
draw2d.Canvas.prototype.getEnableSmoothFigureHandling=function(){
return this.enableSmoothFigureHandling;
};
draw2d.Canvas.prototype.setEnableSmoothFigureHandling=function(flag){
this.enableSmoothFigureHandling=flag;
};
draw2d.Canvas.prototype.getWidth=function(){
return parseInt(this.html.style.width);
};
draw2d.Canvas.prototype.setWidth=function(width){
if(this.scrollArea!==null){
this.scrollArea.style.width=width+"px";
}else{
this.html.style.width=width+"px";
}
};
draw2d.Canvas.prototype.getHeight=function(){
return parseInt(this.html.style.height);
};
draw2d.Canvas.prototype.setHeight=function(_5033){
if(this.scrollArea!==null){
this.scrollArea.style.height=_5033+"px";
}else{
this.html.style.height=_5033+"px";
}
};
draw2d.Canvas.prototype.setBackgroundImage=function(_5034,_5035){
if(_5034!==null){
if(_5035){
this.html.style.background="transparent url("+_5034+") ";
}else{
this.html.style.background="transparent url("+_5034+") no-repeat";
}
}else{
this.html.style.background="transparent";
}
};
draw2d.Canvas.prototype.getY=function(){
return this.y;
};
draw2d.Canvas.prototype.getX=function(){
return this.x;
};
draw2d.Canvas.prototype.getAbsoluteY=function(){
var el=this.html;
var ot=el.offsetTop;
while((el=el.offsetParent)!==null){
ot+=el.offsetTop;
}
return ot;
};
draw2d.Canvas.prototype.getAbsoluteX=function(){
var el=this.html;
var ol=el.offsetLeft;
while((el=el.offsetParent)!==null){
ol+=el.offsetLeft;
}
return ol;
};
draw2d.Canvas.prototype.getScrollLeft=function(){
return this.scrollArea.scrollLeft;
};
draw2d.Canvas.prototype.getScrollTop=function(){
return this.scrollArea.scrollTop;
};
draw2d.Workflow=function(id){
try{
if(!id){
return;
}
this.menu=null;
this.gridWidthX=10;
this.gridWidthY=10;
this.snapToGridHelper=null;
this.verticalSnapToHelperLine=null;
this.horizontalSnapToHelperLine=null;
this.snapToGeometryHelper=null;
this.figures=new draw2d.ArrayList();
this.lines=new draw2d.ArrayList();
this.commonPorts=new draw2d.ArrayList();
this.dropTargets=new draw2d.ArrayList();
this.compartments=new draw2d.ArrayList();
this.selectionListeners=new draw2d.ArrayList();
this.dialogs=new draw2d.ArrayList();
this.toolPalette=null;
this.dragging=false;
this.tooltip=null;
this.draggingLine=null;
this.draggingLineCommand=null;
this.commandStack=new draw2d.CommandStack();
this.oldScrollPosLeft=0;
this.oldScrollPosTop=0;
this.currentSelection=null;
this.currentMenu=null;
this.connectionLine=new draw2d.Line();
this.resizeHandleStart=new draw2d.LineStartResizeHandle(this);
this.resizeHandleEnd=new draw2d.LineEndResizeHandle(this);
this.resizeHandle1=new draw2d.ResizeHandle(this,1);
this.resizeHandle2=new draw2d.ResizeHandle(this,2);
this.resizeHandle3=new draw2d.ResizeHandle(this,3);
this.resizeHandle4=new draw2d.ResizeHandle(this,4);
this.resizeHandle5=new draw2d.ResizeHandle(this,5);
this.resizeHandle6=new draw2d.ResizeHandle(this,6);
this.resizeHandle7=new draw2d.ResizeHandle(this,7);
this.resizeHandle8=new draw2d.ResizeHandle(this,8);
this.resizeHandleHalfWidth=parseInt(this.resizeHandle2.getWidth()/2);
draw2d.Canvas.call(this,id);
this.setPanning(false);
if(this.html!==null){
this.html.style.backgroundImage="url(grid_10.png)";
this.html.className="Workflow";
oThis=this;
this.html.tabIndex="0";
var _4a27=function(){
var _4a28=arguments[0]||window.event;
_4a28.cancelBubble=true;
_4a28.returnValue=false;
_4a28.stopped=true;
var diffX=_4a28.clientX;
var diffY=_4a28.clientY;
var _4a2b=oThis.getScrollLeft();
var _4a2c=oThis.getScrollTop();
var _4a2d=oThis.getAbsoluteX();
var _4a2e=oThis.getAbsoluteY();
if(oThis.getBestFigure(diffX+_4a2b-_4a2d,diffY+_4a2c-_4a2e)!==null){
return;
}
var line=oThis.getBestLine(diffX+_4a2b-_4a2d,diffY+_4a2c-_4a2e,null);
if(line!==null){
line.onContextMenu(diffX+_4a2b-_4a2d,diffY+_4a2c-_4a2e);
}else{
oThis.onContextMenu(diffX+_4a2b-_4a2d,diffY+_4a2c-_4a2e);
}
};
this.html.oncontextmenu=function(){
return false;
};
var oThis=this;
var _4a31=function(event){
var ctrl=event.ctrlKey;
oThis.onKeyDown(event.keyCode,ctrl);
};
var _4a34=function(){
var _4a35=arguments[0]||window.event;
if(_4a35.returnValue==false){
return;
}
var diffX=_4a35.clientX;
var diffY=_4a35.clientY;
var _4a38=oThis.getScrollLeft();
var _4a39=oThis.getScrollTop();
var _4a3a=oThis.getAbsoluteX();
var _4a3b=oThis.getAbsoluteY();
oThis.onMouseDown(diffX+_4a38-_4a3a,diffY+_4a39-_4a3b);
};
var _4a3c=function(){
var _4a3d=arguments[0]||window.event;
if(oThis.currentMenu!==null){
oThis.removeFigure(oThis.currentMenu);
oThis.currentMenu=null;
}
if(_4a3d.button==2){
return;
}
var diffX=_4a3d.clientX;
var diffY=_4a3d.clientY;
var _4a40=oThis.getScrollLeft();
var _4a41=oThis.getScrollTop();
var _4a42=oThis.getAbsoluteX();
var _4a43=oThis.getAbsoluteY();
oThis.onMouseUp(diffX+_4a40-_4a42,diffY+_4a41-_4a43);
};
var _4a44=function(){
var _4a45=arguments[0]||window.event;
var diffX=_4a45.clientX;
var diffY=_4a45.clientY;
var _4a48=oThis.getScrollLeft();
var _4a49=oThis.getScrollTop();
var _4a4a=oThis.getAbsoluteX();
var _4a4b=oThis.getAbsoluteY();
oThis.currentMouseX=diffX+_4a48-_4a4a;
oThis.currentMouseY=diffY+_4a49-_4a4b;
var obj=oThis.getBestFigure(oThis.currentMouseX,oThis.currentMouseY);
if(draw2d.Drag.currentHover!==null&&obj===null){
var _4a4d=new draw2d.DragDropEvent();
_4a4d.initDragDropEvent("mouseleave",false,oThis);
draw2d.Drag.currentHover.dispatchEvent(_4a4d);
}else{
var diffX=_4a45.clientX;
var diffY=_4a45.clientY;
var _4a48=oThis.getScrollLeft();
var _4a49=oThis.getScrollTop();
var _4a4a=oThis.getAbsoluteX();
var _4a4b=oThis.getAbsoluteY();
oThis.onMouseMove(diffX+_4a48-_4a4a,diffY+_4a49-_4a4b);
}
if(obj===null){
draw2d.Drag.currentHover=null;
}
if(oThis.tooltip!==null){
if(Math.abs(oThis.currentTooltipX-oThis.currentMouseX)>10||Math.abs(oThis.currentTooltipY-oThis.currentMouseY)>10){
oThis.showTooltip(null);
}
}
};
var _4a4e=function(_4a4f){
var _4a4f=arguments[0]||window.event;
var diffX=_4a4f.clientX;
var diffY=_4a4f.clientY;
var _4a52=oThis.getScrollLeft();
var _4a53=oThis.getScrollTop();
var _4a54=oThis.getAbsoluteX();
var _4a55=oThis.getAbsoluteY();
var line=oThis.getBestLine(diffX+_4a52-_4a54,diffY+_4a53-_4a55,null);
if(line!==null){
line.onDoubleClick();
}
};
if(this.html.addEventListener){
this.html.addEventListener("contextmenu",_4a27,false);
this.html.addEventListener("mousemove",_4a44,false);
this.html.addEventListener("mouseup",_4a3c,false);
this.html.addEventListener("mousedown",_4a34,false);
this.html.addEventListener("keydown",_4a31,false);
this.html.addEventListener("dblclick",_4a4e,false);
}else{
if(this.html.attachEvent){
this.html.attachEvent("oncontextmenu",_4a27);
this.html.attachEvent("onmousemove",_4a44);
this.html.attachEvent("onmousedown",_4a34);
this.html.attachEvent("onmouseup",_4a3c);
this.html.attachEvent("onkeydown",_4a31);
this.html.attachEvent("ondblclick",_4a4e);
}else{
throw "Open-jACOB Draw2D not supported in this browser.";
}
}
}
}
catch(e){
pushErrorStack(e,"draw2d.Workflow=function(/*:String*/id)");
}
};
draw2d.Workflow.prototype=new draw2d.Canvas();
draw2d.Workflow.prototype.type="draw2d.Workflow";
draw2d.Workflow.COLOR_GREEN=new draw2d.Color(0,255,0);
draw2d.Workflow.prototype.clear=function(){
this.scrollTo(0,0,true);
this.gridWidthX=10;
this.gridWidthY=10;
this.snapToGridHelper=null;
this.verticalSnapToHelperLine=null;
this.horizontalSnapToHelperLine=null;
var _4a57=this.getDocument();
var _4a58=_4a57.getLines().clone();
for(var i=0;i<_4a58.getSize();i++){
(new draw2d.CommandDelete(_4a58.get(i))).execute();
}
var _4a5a=_4a57.getFigures().clone();
for(var i=0;i<_4a5a.getSize();i++){
(new draw2d.CommandDelete(_4a5a.get(i))).execute();
}
this.commonPorts.removeAllElements();
this.dropTargets.removeAllElements();
this.compartments.removeAllElements();
this.selectionListeners.removeAllElements();
this.dialogs.removeAllElements();
this.commandStack=new draw2d.CommandStack();
this.currentSelection=null;
this.currentMenu=null;
draw2d.Drag.clearCurrent();
};
draw2d.Workflow.prototype.onScroll=function(){
var _4a5b=this.getScrollLeft();
var _4a5c=this.getScrollTop();
var _4a5d=_4a5b-this.oldScrollPosLeft;
var _4a5e=_4a5c-this.oldScrollPosTop;
for(var i=0;i<this.figures.getSize();i++){
var _4a60=this.figures.get(i);
if(_4a60.hasFixedPosition&&_4a60.hasFixedPosition()==true){
_4a60.setPosition(_4a60.getX()+_4a5d,_4a60.getY()+_4a5e);
}
}
this.oldScrollPosLeft=_4a5b;
this.oldScrollPosTop=_4a5c;
};
draw2d.Workflow.prototype.setPanning=function(flag){
this.panning=flag;
if(flag){
this.html.style.cursor="move";
}else{
this.html.style.cursor="default";
}
};
draw2d.Workflow.prototype.scrollTo=function(x,y,fast){
if(fast){
this.scrollArea.scrollLeft=x;
this.scrollArea.scrollTop=y;
}else{
var steps=40;
var xStep=(x-this.getScrollLeft())/steps;
var yStep=(y-this.getScrollTop())/steps;
var oldX=this.getScrollLeft();
var oldY=this.getScrollTop();
for(var i=0;i<steps;i++){
this.scrollArea.scrollLeft=oldX+(xStep*i);
this.scrollArea.scrollTop=oldY+(yStep*i);
}
}
};
draw2d.Workflow.prototype.showTooltip=function(_4a6b,_4a6c){
if(this.tooltip!==null){
this.removeFigure(this.tooltip);
this.tooltip=null;
if(this.tooltipTimer>=0){
window.clearTimeout(this.tooltipTimer);
this.tooltipTimer=-1;
}
}
this.tooltip=_4a6b;
if(this.tooltip!==null){
this.currentTooltipX=this.currentMouseX;
this.currentTooltipY=this.currentMouseY;
this.addFigure(this.tooltip,this.currentTooltipX+10,this.currentTooltipY+10);
var oThis=this;
var _4a6e=function(){
oThis.tooltipTimer=-1;
oThis.showTooltip(null);
};
if(_4a6c==true){
this.tooltipTimer=window.setTimeout(_4a6e,5000);
}
}
};
draw2d.Workflow.prototype.showDialog=function(_4a6f,xPos,yPos){
if(xPos){
this.addFigure(_4a6f,xPos,yPos);
}else{
this.addFigure(_4a6f,200,100);
}
this.dialogs.add(_4a6f);
};
draw2d.Workflow.prototype.showMenu=function(menu,xPos,yPos){
if(this.menu!==null){
this.html.removeChild(this.menu.getHTMLElement());
this.menu.setWorkflow();
}
this.menu=menu;
if(this.menu!==null){
this.menu.setWorkflow(this);
this.menu.setPosition(xPos,yPos);
this.html.appendChild(this.menu.getHTMLElement());
this.menu.paint();
}
};
draw2d.Workflow.prototype.onContextMenu=function(x,y){
var menu=this.getContextMenu();
if(menu!==null){
this.showMenu(menu,x,y);
}
};
draw2d.Workflow.prototype.getContextMenu=function(){
return null;
};
draw2d.Workflow.prototype.setToolWindow=function(_4a78,x,y){
this.toolPalette=_4a78;
if(y){
this.addFigure(_4a78,x,y);
}else{
this.addFigure(_4a78,20,20);
}
this.dialogs.add(_4a78);
};
draw2d.Workflow.prototype.setSnapToGrid=function(flag){
if(flag){
this.snapToGridHelper=new draw2d.SnapToGrid(this);
}else{
this.snapToGridHelper=null;
}
};
draw2d.Workflow.prototype.setSnapToGeometry=function(flag){
if(flag){
this.snapToGeometryHelper=new draw2d.SnapToGeometry(this);
}else{
this.snapToGeometryHelper=null;
}
};
draw2d.Workflow.prototype.setGridWidth=function(dx,dy){
this.gridWidthX=dx;
this.gridWidthY=dy;
};
draw2d.Workflow.prototype.addFigure=function(_4a7f,xPos,yPos){
try{
draw2d.Canvas.prototype.addFigure.call(this,_4a7f,xPos,yPos,true);
_4a7f.setWorkflow(this);
var _4a82=this;
if(_4a7f instanceof draw2d.CompartmentFigure){
this.compartments.add(_4a7f);
}
if(_4a7f instanceof draw2d.Line){
this.lines.add(_4a7f);
}else{
this.figures.add(_4a7f);
_4a7f.draggable.addEventListener("drag",function(_4a83){
var _4a84=_4a82.getFigure(_4a83.target.element.id);
if(_4a84===null){
return;
}
if(_4a84.isSelectable()==false){
return;
}
_4a82.moveResizeHandles(_4a84);
});
}
_4a7f.paint();
this.setDocumentDirty();
}
catch(e){
pushErrorStack(e,"draw2d.Workflow.prototype.addFigure=function(/*:draw2d.Figure*/ figure ,/*:int*/ xPos, /*:int*/ yPos)");
}
};
draw2d.Workflow.prototype.removeFigure=function(_4a85){
draw2d.Canvas.prototype.removeFigure.call(this,_4a85);
this.figures.remove(_4a85);
this.lines.remove(_4a85);
this.dialogs.remove(_4a85);
_4a85.setWorkflow(null);
if(_4a85 instanceof draw2d.CompartmentFigure){
this.compartments.remove(_4a85);
}
if(_4a85 instanceof draw2d.Connection){
_4a85.disconnect();
}
if(this.currentSelection==_4a85){
this.setCurrentSelection(null);
}
this.setDocumentDirty();
_4a85.onRemove(this);
};
draw2d.Workflow.prototype.moveFront=function(_4a86){
this.html.removeChild(_4a86.getHTMLElement());
this.html.appendChild(_4a86.getHTMLElement());
};
draw2d.Workflow.prototype.moveBack=function(_4a87){
this.html.removeChild(_4a87.getHTMLElement());
this.html.insertBefore(_4a87.getHTMLElement(),this.html.firstChild);
};
draw2d.Workflow.prototype.getBestCompartmentFigure=function(x,y,_4a8a){
var _4a8b=null;
for(var i=0;i<this.figures.getSize();i++){
var _4a8d=this.figures.get(i);
if((_4a8d instanceof draw2d.CompartmentFigure)&&_4a8d.isOver(x,y)==true&&_4a8d!=_4a8a){
if(_4a8b===null){
_4a8b=_4a8d;
}else{
if(_4a8b.getZOrder()<_4a8d.getZOrder()){
_4a8b=_4a8d;
}
}
}
}
return _4a8b;
};
draw2d.Workflow.prototype.getBestFigure=function(x,y,_4a90){
var _4a91=null;
for(var i=0;i<this.figures.getSize();i++){
var _4a93=this.figures.get(i);
if(_4a93.isOver(x,y)==true&&_4a93!=_4a90){
if(_4a91===null){
_4a91=_4a93;
}else{
if(_4a91.getZOrder()<_4a93.getZOrder()){
_4a91=_4a93;
}
}
}
}
return _4a91;
};
draw2d.Workflow.prototype.getBestLine=function(x,y,_4a96){
var _4a97=null;
var count=this.lines.getSize();
for(var i=0;i<count;i++){
var line=this.lines.get(i);
if(line.containsPoint(x,y)==true&&line!=_4a96){
if(_4a97===null){
_4a97=line;
}else{
if(_4a97.getZOrder()<line.getZOrder()){
_4a97=line;
}
}
}
}
return _4a97;
};
draw2d.Workflow.prototype.getFigure=function(id){
for(var i=0;i<this.figures.getSize();i++){
var _4a9d=this.figures.get(i);
if(_4a9d.id==id){
return _4a9d;
}
}
return null;
};
draw2d.Workflow.prototype.getFigures=function(){
return this.figures;
};
draw2d.Workflow.prototype.getDocument=function(){
return new draw2d.CanvasDocument(this);
};
draw2d.Workflow.prototype.addSelectionListener=function(w){
if(w!==null){
if(w.onSelectionChanged){
this.selectionListeners.add(w);
}else{
throw "Object doesn't implement required callback method [onSelectionChanged]";
}
}
};
draw2d.Workflow.prototype.removeSelectionListener=function(w){
this.selectionListeners.remove(w);
};
draw2d.Workflow.prototype.setCurrentSelection=function(_4aa0){
if(_4aa0===null||this.currentSelection!=_4aa0){
this.hideResizeHandles();
this.hideLineResizeHandles();
}
this.currentSelection=_4aa0;
for(var i=0;i<this.selectionListeners.getSize();i++){
var w=this.selectionListeners.get(i);
if(w.onSelectionChanged){
w.onSelectionChanged(this.currentSelection,this.currentSelection?this.currentSelection.getModel():null);
}
}
if(_4aa0 instanceof draw2d.Line){
this.showLineResizeHandles(_4aa0);
if(!(_4aa0 instanceof draw2d.Connection)){
this.draggingLineCommand=line.createCommand(new draw2d.EditPolicy(draw2d.EditPolicy.MOVE));
if(this.draggingLineCommand!==null){
this.draggingLine=_4aa0;
}
}
}
};
draw2d.Workflow.prototype.getCurrentSelection=function(){
return this.currentSelection;
};
draw2d.Workflow.prototype.getLine=function(id){
var count=this.lines.getSize();
for(var i=0;i<count;i++){
var line=this.lines.get(i);
if(line.getId()==id){
return line;
}
}
return null;
};
draw2d.Workflow.prototype.getLines=function(){
return this.lines;
};
draw2d.Workflow.prototype.registerPort=function(port){
port.draggable.targets=this.dropTargets;
this.commonPorts.add(port);
this.dropTargets.add(port.dropable);
};
draw2d.Workflow.prototype.unregisterPort=function(port){
port.draggable.targets=null;
this.commonPorts.remove(port);
this.dropTargets.remove(port.dropable);
};
draw2d.Workflow.prototype.getCommandStack=function(){
return this.commandStack;
};
draw2d.Workflow.prototype.showConnectionLine=function(x1,y1,x2,y2){
this.connectionLine.setStartPoint(x1,y1);
this.connectionLine.setEndPoint(x2,y2);
if(this.connectionLine.canvas===null){
draw2d.Canvas.prototype.addFigure.call(this,this.connectionLine);
}
};
draw2d.Workflow.prototype.hideConnectionLine=function(){
if(this.connectionLine.canvas!==null){
draw2d.Canvas.prototype.removeFigure.call(this,this.connectionLine);
}
};
draw2d.Workflow.prototype.showLineResizeHandles=function(_4aad){
var _4aae=this.resizeHandleStart.getWidth()/2;
var _4aaf=this.resizeHandleStart.getHeight()/2;
var _4ab0=_4aad.getStartPoint();
var _4ab1=_4aad.getEndPoint();
draw2d.Canvas.prototype.addFigure.call(this,this.resizeHandleStart,_4ab0.x-_4aae,_4ab0.y-_4aae);
draw2d.Canvas.prototype.addFigure.call(this,this.resizeHandleEnd,_4ab1.x-_4aae,_4ab1.y-_4aae);
this.resizeHandleStart.setCanDrag(_4aad.isResizeable());
this.resizeHandleEnd.setCanDrag(_4aad.isResizeable());
if(_4aad.isResizeable()){
this.resizeHandleStart.setBackgroundColor(draw2d.Workflow.COLOR_GREEN);
this.resizeHandleEnd.setBackgroundColor(draw2d.Workflow.COLOR_GREEN);
this.resizeHandleStart.draggable.targets=this.dropTargets;
this.resizeHandleEnd.draggable.targets=this.dropTargets;
}else{
this.resizeHandleStart.setBackgroundColor(null);
this.resizeHandleEnd.setBackgroundColor(null);
}
};
draw2d.Workflow.prototype.hideLineResizeHandles=function(){
if(this.resizeHandleStart.canvas!==null){
draw2d.Canvas.prototype.removeFigure.call(this,this.resizeHandleStart);
}
if(this.resizeHandleEnd.canvas!==null){
draw2d.Canvas.prototype.removeFigure.call(this,this.resizeHandleEnd);
}
};
draw2d.Workflow.prototype.showResizeHandles=function(_4ab2){
this.hideLineResizeHandles();
this.hideResizeHandles();
if(this.getEnableSmoothFigureHandling()==true&&this.getCurrentSelection()!=_4ab2){
this.resizeHandle1.setAlpha(0.01);
this.resizeHandle2.setAlpha(0.01);
this.resizeHandle3.setAlpha(0.01);
this.resizeHandle4.setAlpha(0.01);
this.resizeHandle5.setAlpha(0.01);
this.resizeHandle6.setAlpha(0.01);
this.resizeHandle7.setAlpha(0.01);
this.resizeHandle8.setAlpha(0.01);
}
var _4ab3=this.resizeHandle1.getWidth();
var _4ab4=this.resizeHandle1.getHeight();
var _4ab5=_4ab2.getHeight();
var _4ab6=_4ab2.getWidth();
var xPos=_4ab2.getX();
var yPos=_4ab2.getY();
draw2d.Canvas.prototype.addFigure.call(this,this.resizeHandle1,xPos-_4ab3,yPos-_4ab4);
draw2d.Canvas.prototype.addFigure.call(this,this.resizeHandle3,xPos+_4ab6,yPos-_4ab4);
draw2d.Canvas.prototype.addFigure.call(this,this.resizeHandle5,xPos+_4ab6,yPos+_4ab5);
draw2d.Canvas.prototype.addFigure.call(this,this.resizeHandle7,xPos-_4ab3,yPos+_4ab5);
this.moveFront(this.resizeHandle1);
this.moveFront(this.resizeHandle3);
this.moveFront(this.resizeHandle5);
this.moveFront(this.resizeHandle7);
this.resizeHandle1.setCanDrag(_4ab2.isResizeable());
this.resizeHandle3.setCanDrag(_4ab2.isResizeable());
this.resizeHandle5.setCanDrag(_4ab2.isResizeable());
this.resizeHandle7.setCanDrag(_4ab2.isResizeable());
if(_4ab2.isResizeable()){
var green=new draw2d.Color(0,255,0);
this.resizeHandle1.setBackgroundColor(green);
this.resizeHandle3.setBackgroundColor(green);
this.resizeHandle5.setBackgroundColor(green);
this.resizeHandle7.setBackgroundColor(green);
}else{
this.resizeHandle1.setBackgroundColor(null);
this.resizeHandle3.setBackgroundColor(null);
this.resizeHandle5.setBackgroundColor(null);
this.resizeHandle7.setBackgroundColor(null);
}
if(_4ab2.isStrechable()&&_4ab2.isResizeable()){
this.resizeHandle2.setCanDrag(_4ab2.isResizeable());
this.resizeHandle4.setCanDrag(_4ab2.isResizeable());
this.resizeHandle6.setCanDrag(_4ab2.isResizeable());
this.resizeHandle8.setCanDrag(_4ab2.isResizeable());
draw2d.Canvas.prototype.addFigure.call(this,this.resizeHandle2,xPos+(_4ab6/2)-this.resizeHandleHalfWidth,yPos-_4ab4);
draw2d.Canvas.prototype.addFigure.call(this,this.resizeHandle4,xPos+_4ab6,yPos+(_4ab5/2)-(_4ab4/2));
draw2d.Canvas.prototype.addFigure.call(this,this.resizeHandle6,xPos+(_4ab6/2)-this.resizeHandleHalfWidth,yPos+_4ab5);
draw2d.Canvas.prototype.addFigure.call(this,this.resizeHandle8,xPos-_4ab3,yPos+(_4ab5/2)-(_4ab4/2));
this.moveFront(this.resizeHandle2);
this.moveFront(this.resizeHandle4);
this.moveFront(this.resizeHandle6);
this.moveFront(this.resizeHandle8);
}
};
draw2d.Workflow.prototype.hideResizeHandles=function(){
if(this.resizeHandle1.canvas!==null){
draw2d.Canvas.prototype.removeFigure.call(this,this.resizeHandle1);
}
if(this.resizeHandle2.canvas!==null){
draw2d.Canvas.prototype.removeFigure.call(this,this.resizeHandle2);
}
if(this.resizeHandle3.canvas!==null){
draw2d.Canvas.prototype.removeFigure.call(this,this.resizeHandle3);
}
if(this.resizeHandle4.canvas!==null){
draw2d.Canvas.prototype.removeFigure.call(this,this.resizeHandle4);
}
if(this.resizeHandle5.canvas!==null){
draw2d.Canvas.prototype.removeFigure.call(this,this.resizeHandle5);
}
if(this.resizeHandle6.canvas!==null){
draw2d.Canvas.prototype.removeFigure.call(this,this.resizeHandle6);
}
if(this.resizeHandle7.canvas!==null){
draw2d.Canvas.prototype.removeFigure.call(this,this.resizeHandle7);
}
if(this.resizeHandle8.canvas!==null){
draw2d.Canvas.prototype.removeFigure.call(this,this.resizeHandle8);
}
};
draw2d.Workflow.prototype.moveResizeHandles=function(_4aba){
var _4abb=this.resizeHandle1.getWidth();
var _4abc=this.resizeHandle1.getHeight();
var _4abd=_4aba.getHeight();
var _4abe=_4aba.getWidth();
var xPos=_4aba.getX();
var yPos=_4aba.getY();
this.resizeHandle1.setPosition(xPos-_4abb,yPos-_4abc);
this.resizeHandle3.setPosition(xPos+_4abe,yPos-_4abc);
this.resizeHandle5.setPosition(xPos+_4abe,yPos+_4abd);
this.resizeHandle7.setPosition(xPos-_4abb,yPos+_4abd);
if(_4aba.isStrechable()){
this.resizeHandle2.setPosition(xPos+(_4abe/2)-this.resizeHandleHalfWidth,yPos-_4abc);
this.resizeHandle4.setPosition(xPos+_4abe,yPos+(_4abd/2)-(_4abc/2));
this.resizeHandle6.setPosition(xPos+(_4abe/2)-this.resizeHandleHalfWidth,yPos+_4abd);
this.resizeHandle8.setPosition(xPos-_4abb,yPos+(_4abd/2)-(_4abc/2));
}
};
draw2d.Workflow.prototype.onMouseDown=function(x,y){
this.dragging=true;
this.mouseDownPosX=x;
this.mouseDownPosY=y;
if(this.toolPalette!==null&&this.toolPalette.getActiveTool()!==null){
this.toolPalette.getActiveTool().execute(x,y);
}
this.showMenu(null);
var line=this.getBestLine(x,y);
if(line!==null&&line.isSelectable()){
this.setCurrentSelection(line);
}else{
this.setCurrentSelection(null);
}
};
draw2d.Workflow.prototype.onMouseUp=function(x,y){
this.dragging=false;
if(this.draggingLineCommand!==null){
this.getCommandStack().execute(this.draggingLineCommand);
this.draggingLine=null;
this.draggingLineCommand=null;
}
};
draw2d.Workflow.prototype.onMouseMove=function(x,y){
if(this.dragging===true&&this.draggingLine!==null){
var diffX=x-this.mouseDownPosX;
var diffY=y-this.mouseDownPosY;
this.draggingLine.startX=this.draggingLine.getStartX()+diffX;
this.draggingLine.startY=this.draggingLine.getStartY()+diffY;
this.draggingLine.setEndPoint(this.draggingLine.getEndX()+diffX,this.draggingLine.getEndY()+diffY);
this.mouseDownPosX=x;
this.mouseDownPosY=y;
this.showLineResizeHandles(this.currentSelection);
}else{
if(this.dragging===true&&this.panning===true){
var diffX=x-this.mouseDownPosX;
var diffY=y-this.mouseDownPosY;
this.scrollTo(this.getScrollLeft()-diffX,this.getScrollTop()-diffY,true);
this.onScroll();
}
}
};
draw2d.Workflow.prototype.onKeyDown=function(_4aca,ctrl){
if(_4aca==46&&this.currentSelection!==null){
this.commandStack.execute(this.currentSelection.createCommand(new draw2d.EditPolicy(draw2d.EditPolicy.DELETE)));
}else{
if(_4aca==90&&ctrl){
this.commandStack.undo();
}else{
if(_4aca==89&&ctrl){
this.commandStack.redo();
}
}
}
};
draw2d.Workflow.prototype.setDocumentDirty=function(){
try{
for(var i=0;i<this.dialogs.getSize();i++){
var d=this.dialogs.get(i);
if(d!==null&&d.onSetDocumentDirty){
d.onSetDocumentDirty();
}
}
if(this.snapToGeometryHelper!==null){
this.snapToGeometryHelper.onSetDocumentDirty();
}
if(this.snapToGridHelper!==null){
this.snapToGridHelper.onSetDocumentDirty();
}
}
catch(e){
pushErrorStack(e,"draw2d.Workflow.prototype.setDocumentDirty=function()");
}
};
draw2d.Workflow.prototype.snapToHelper=function(_4ace,pos){
if(this.snapToGeometryHelper!==null){
if(_4ace instanceof draw2d.ResizeHandle){
var _4ad0=_4ace.getSnapToGridAnchor();
pos.x+=_4ad0.x;
pos.y+=_4ad0.y;
var _4ad1=new draw2d.Point(pos.x,pos.y);
var _4ad2=_4ace.getSnapToDirection();
var _4ad3=this.snapToGeometryHelper.snapPoint(_4ad2,pos,_4ad1);
if((_4ad2&draw2d.SnapToHelper.EAST_WEST)&&!(_4ad3&draw2d.SnapToHelper.EAST_WEST)){
this.showSnapToHelperLineVertical(_4ad1.x);
}else{
this.hideSnapToHelperLineVertical();
}
if((_4ad2&draw2d.SnapToHelper.NORTH_SOUTH)&&!(_4ad3&draw2d.SnapToHelper.NORTH_SOUTH)){
this.showSnapToHelperLineHorizontal(_4ad1.y);
}else{
this.hideSnapToHelperLineHorizontal();
}
_4ad1.x-=_4ad0.x;
_4ad1.y-=_4ad0.y;
return _4ad1;
}else{
var _4ad4=new draw2d.Dimension(pos.x,pos.y,_4ace.getWidth(),_4ace.getHeight());
var _4ad1=new draw2d.Dimension(pos.x,pos.y,_4ace.getWidth(),_4ace.getHeight());
var _4ad2=draw2d.SnapToHelper.NSEW;
var _4ad3=this.snapToGeometryHelper.snapRectangle(_4ad4,_4ad1);
if((_4ad2&draw2d.SnapToHelper.WEST)&&!(_4ad3&draw2d.SnapToHelper.WEST)){
this.showSnapToHelperLineVertical(_4ad1.x);
}else{
if((_4ad2&draw2d.SnapToHelper.EAST)&&!(_4ad3&draw2d.SnapToHelper.EAST)){
this.showSnapToHelperLineVertical(_4ad1.getX()+_4ad1.getWidth());
}else{
this.hideSnapToHelperLineVertical();
}
}
if((_4ad2&draw2d.SnapToHelper.NORTH)&&!(_4ad3&draw2d.SnapToHelper.NORTH)){
this.showSnapToHelperLineHorizontal(_4ad1.y);
}else{
if((_4ad2&draw2d.SnapToHelper.SOUTH)&&!(_4ad3&draw2d.SnapToHelper.SOUTH)){
this.showSnapToHelperLineHorizontal(_4ad1.getY()+_4ad1.getHeight());
}else{
this.hideSnapToHelperLineHorizontal();
}
}
return _4ad1.getTopLeft();
}
}else{
if(this.snapToGridHelper!==null){
var _4ad0=_4ace.getSnapToGridAnchor();
pos.x=pos.x+_4ad0.x;
pos.y=pos.y+_4ad0.y;
var _4ad1=new draw2d.Point(pos.x,pos.y);
this.snapToGridHelper.snapPoint(0,pos,_4ad1);
_4ad1.x=_4ad1.x-_4ad0.x;
_4ad1.y=_4ad1.y-_4ad0.y;
return _4ad1;
}
}
return pos;
};
draw2d.Workflow.prototype.showSnapToHelperLineHorizontal=function(_4ad5){
if(this.horizontalSnapToHelperLine===null){
this.horizontalSnapToHelperLine=new draw2d.Line();
this.horizontalSnapToHelperLine.setColor(new draw2d.Color(175,175,255));
this.addFigure(this.horizontalSnapToHelperLine);
}
this.horizontalSnapToHelperLine.setStartPoint(0,_4ad5);
this.horizontalSnapToHelperLine.setEndPoint(this.getWidth(),_4ad5);
};
draw2d.Workflow.prototype.showSnapToHelperLineVertical=function(_4ad6){
if(this.verticalSnapToHelperLine===null){
this.verticalSnapToHelperLine=new draw2d.Line();
this.verticalSnapToHelperLine.setColor(new draw2d.Color(175,175,255));
this.addFigure(this.verticalSnapToHelperLine);
}
this.verticalSnapToHelperLine.setStartPoint(_4ad6,0);
this.verticalSnapToHelperLine.setEndPoint(_4ad6,this.getHeight());
};
draw2d.Workflow.prototype.hideSnapToHelperLines=function(){
this.hideSnapToHelperLineHorizontal();
this.hideSnapToHelperLineVertical();
};
draw2d.Workflow.prototype.hideSnapToHelperLineHorizontal=function(){
if(this.horizontalSnapToHelperLine!==null){
this.removeFigure(this.horizontalSnapToHelperLine);
this.horizontalSnapToHelperLine=null;
}
};
draw2d.Workflow.prototype.hideSnapToHelperLineVertical=function(){
if(this.verticalSnapToHelperLine!==null){
this.removeFigure(this.verticalSnapToHelperLine);
this.verticalSnapToHelperLine=null;
}
};
draw2d.WindowFigure=function(title){
this.title=title;
this.titlebar=null;
draw2d.Figure.call(this);
this.setDeleteable(false);
this.setCanSnapToHelper(false);
this.setZOrder(draw2d.WindowFigure.ZOrderIndex);
};
draw2d.WindowFigure.prototype=new draw2d.Figure();
draw2d.WindowFigure.prototype.type=":draw2d.WindowFigure";
draw2d.WindowFigure.ZOrderIndex=50000;
draw2d.WindowFigure.setZOrderBaseIndex=function(index){
draw2d.WindowFigure.ZOrderBaseIndex=index;
};
draw2d.WindowFigure.prototype.hasFixedPosition=function(){
return true;
};
draw2d.WindowFigure.prototype.hasTitleBar=function(){
return true;
};
draw2d.WindowFigure.prototype.createHTMLElement=function(){
var item=draw2d.Figure.prototype.createHTMLElement.call(this);
item.style.margin="0px";
item.style.padding="0px";
item.style.border="1px solid black";
item.style.backgroundImage="url(window_bg.png)";
item.style.zIndex=draw2d.WindowFigure.ZOrderIndex;
item.style.cursor=null;
item.className="WindowFigure";
if(this.hasTitleBar()){
this.titlebar=document.createElement("div");
this.titlebar.style.position="absolute";
this.titlebar.style.left="0px";
this.titlebar.style.top="0px";
this.titlebar.style.width=this.getWidth()+"px";
this.titlebar.style.height="15px";
this.titlebar.style.margin="0px";
this.titlebar.style.padding="0px";
this.titlebar.style.font="normal 10px verdana";
this.titlebar.style.backgroundColor="blue";
this.titlebar.style.borderBottom="2px solid gray";
this.titlebar.style.whiteSpace="nowrap";
this.titlebar.style.textAlign="center";
this.titlebar.style.backgroundImage="url(window_toolbar.png)";
this.titlebar.className="WindowFigure_titlebar";
this.textNode=document.createTextNode(this.title);
this.titlebar.appendChild(this.textNode);
this.disableTextSelection(this.titlebar);
item.appendChild(this.titlebar);
}
return item;
};
draw2d.WindowFigure.prototype.setDocumentDirty=function(_5670){
};
draw2d.WindowFigure.prototype.onDragend=function(){
};
draw2d.WindowFigure.prototype.onDragstart=function(x,y){
if(this.titlebar===null){
return false;
}
if(this.canDrag===true&&x<parseInt(this.titlebar.style.width)&&y<parseInt(this.titlebar.style.height)){
return true;
}
return false;
};
draw2d.WindowFigure.prototype.isSelectable=function(){
return false;
};
draw2d.WindowFigure.prototype.setCanDrag=function(flag){
draw2d.Figure.prototype.setCanDrag.call(this,flag);
this.html.style.cursor="";
if(this.titlebar===null){
return;
}
if(flag){
this.titlebar.style.cursor="move";
}else{
this.titlebar.style.cursor="";
}
};
draw2d.WindowFigure.prototype.setWorkflow=function(_5674){
var _5675=this.workflow;
draw2d.Figure.prototype.setWorkflow.call(this,_5674);
if(_5675!==null){
_5675.removeSelectionListener(this);
}
if(this.workflow!==null){
this.workflow.addSelectionListener(this);
}
};
draw2d.WindowFigure.prototype.setDimension=function(w,h){
draw2d.Figure.prototype.setDimension.call(this,w,h);
if(this.titlebar!==null){
this.titlebar.style.width=this.getWidth()+"px";
}
};
draw2d.WindowFigure.prototype.setTitle=function(title){
this.title=title;
};
draw2d.WindowFigure.prototype.getMinWidth=function(){
return 50;
};
draw2d.WindowFigure.prototype.getMinHeight=function(){
return 50;
};
draw2d.WindowFigure.prototype.isResizeable=function(){
return false;
};
draw2d.WindowFigure.prototype.setAlpha=function(_5679){
};
draw2d.WindowFigure.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.bgColor!==null){
this.html.style.backgroundColor=this.bgColor.getHTMLStyle();
}else{
this.html.style.backgroundColor="transparent";
this.html.style.backgroundImage="";
}
};
draw2d.WindowFigure.prototype.setColor=function(color){
this.lineColor=color;
if(this.lineColor!==null){
this.html.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
}else{
this.html.style.border="0px";
}
};
draw2d.WindowFigure.prototype.setLineWidth=function(w){
this.lineStroke=w;
this.html.style.border=this.lineStroke+"px solid black";
};
draw2d.WindowFigure.prototype.onSelectionChanged=function(_567d,model){
};
draw2d.Button=function(_5547,width,_5549){
this.x=0;
this.y=0;
this.width=24;
this.height=24;
this.id=draw2d.UUID.create();
this.enabled=true;
this.active=false;
this.palette=_5547;
this.html=this.createHTMLElement();
if(width!==undefined&&_5549!==undefined){
this.setDimension(width,_5549);
}else{
this.setDimension(24,24);
}
};
draw2d.Button.prototype.type="draw2d.Button";
draw2d.Button.prototype.dispose=function(){
};
draw2d.Button.prototype.getImageUrl=function(){
return this.type+".png";
};
draw2d.Button.prototype.createHTMLElement=function(){
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
if(this.getImageUrl()!==null){
item.style.backgroundImage="url("+this.getImageUrl()+")";
}else{
item.style.backgroundImage="";
}
var oThis=this;
this.omousedown=function(event){
if(oThis.enabled){
oThis.setActive(true);
}
event.cancelBubble=true;
event.returnValue=false;
};
this.omouseup=function(event){
if(oThis.enabled){
oThis.setActive(false);
oThis.execute();
oThis.palette.setActiveTool(null);
}
event.cancelBubble=true;
event.returnValue=false;
};
if(item.addEventListener){
item.addEventListener("mousedown",this.omousedown,false);
item.addEventListener("mouseup",this.omouseup,false);
}else{
if(item.attachEvent){
item.attachEvent("onmousedown",this.omousedown);
item.attachEvent("onmouseup",this.omouseup);
}
}
return item;
};
draw2d.Button.prototype.getHTMLElement=function(){
if(this.html===null){
this.html=this.createHTMLElement();
}
return this.html;
};
draw2d.Button.prototype.execute=function(){
};
draw2d.Button.prototype.setTooltip=function(_554e){
this.tooltip=_554e;
if(this.tooltip!==null){
this.html.title=this.tooltip;
}else{
this.html.title="";
}
};
draw2d.Button.prototype.getWorkflow=function(){
return this.getToolPalette().getWorkflow();
};
draw2d.Button.prototype.getToolPalette=function(){
return this.palette;
};
draw2d.Button.prototype.setActive=function(flag){
if(!this.enabled){
return;
}
this.active=flag;
if(flag===true){
this.html.style.border="1px inset";
}else{
this.html.style.border="0px";
}
};
draw2d.Button.prototype.isActive=function(){
return this.active;
};
draw2d.Button.prototype.setEnabled=function(flag){
this.enabled=flag;
if(flag){
this.html.style.filter="alpha(opacity=100)";
this.html.style.opacity="1.0";
}else{
this.html.style.filter="alpha(opacity=30)";
this.html.style.opacity="0.3";
}
};
draw2d.Button.prototype.setDimension=function(w,h){
this.width=w;
this.height=h;
if(this.html===null){
return;
}
this.html.style.width=this.width+"px";
this.html.style.height=this.height+"px";
};
draw2d.Button.prototype.setPosition=function(xPos,yPos){
this.x=Math.max(0,xPos);
this.y=Math.max(0,yPos);
if(this.html===null){
return;
}
this.html.style.left=this.x+"px";
this.html.style.top=this.y+"px";
};
draw2d.Button.prototype.getWidth=function(){
return this.width;
};
draw2d.Button.prototype.getHeight=function(){
return this.height;
};
draw2d.Button.prototype.getY=function(){
return this.y;
};
draw2d.Button.prototype.getX=function(){
return this.x;
};
draw2d.Button.prototype.getPosition=function(){
return new draw2d.Point(this.x,this.y);
};
draw2d.ToggleButton=function(_58b5){
draw2d.Button.call(this,_58b5);
this.isDownFlag=false;
};
draw2d.ToggleButton.prototype=new draw2d.Button();
draw2d.ToggleButton.prototype.type="draw2d.ToggleButton";
draw2d.ToggleButton.prototype.createHTMLElement=function(){
var item=document.createElement("div");
item.id=this.id;
item.style.position="absolute";
item.style.left=this.x+"px";
item.style.top=this.y+"px";
item.style.height="24px";
item.style.width="24px";
item.style.margin="0px";
item.style.padding="0px";
if(this.getImageUrl()!==null){
item.style.backgroundImage="url("+this.getImageUrl()+")";
}else{
item.style.backgroundImage="";
}
var oThis=this;
this.omousedown=function(event){
if(oThis.enabled){
if(!oThis.isDown()){
draw2d.Button.prototype.setActive.call(oThis,true);
}
}
event.cancelBubble=true;
event.returnValue=false;
};
this.omouseup=function(event){
if(oThis.enabled){
if(oThis.isDown()){
draw2d.Button.prototype.setActive.call(oThis,false);
}
oThis.isDownFlag=!oThis.isDownFlag;
oThis.execute();
}
event.cancelBubble=true;
event.returnValue=false;
};
if(item.addEventListener){
item.addEventListener("mousedown",this.omousedown,false);
item.addEventListener("mouseup",this.omouseup,false);
}else{
if(item.attachEvent){
item.attachEvent("onmousedown",this.omousedown);
item.attachEvent("onmouseup",this.omouseup);
}
}
return item;
};
draw2d.ToggleButton.prototype.isDown=function(){
return this.isDownFlag;
};
draw2d.ToggleButton.prototype.setActive=function(flag){
draw2d.Button.prototype.setActive.call(this,flag);
this.isDownFlag=flag;
};
draw2d.ToggleButton.prototype.execute=function(){
};
draw2d.ToolGeneric=function(_58a7){
this.x=0;
this.y=0;
this.enabled=true;
this.tooltip=null;
this.palette=_58a7;
this.html=this.createHTMLElement();
this.setDimension(10,10);
};
draw2d.ToolGeneric.prototype.type="draw2d.ToolGeneric";
draw2d.ToolGeneric.prototype.dispose=function(){
};
draw2d.ToolGeneric.prototype.getImageUrl=function(){
return this.type+".png";
};
draw2d.ToolGeneric.prototype.getWorkflow=function(){
return this.getToolPalette().getWorkflow();
};
draw2d.ToolGeneric.prototype.getToolPalette=function(){
return this.palette;
};
draw2d.ToolGeneric.prototype.createHTMLElement=function(){
var item=document.createElement("div");
item.id=this.id;
item.style.position="absolute";
item.style.left=this.x+"px";
item.style.top=this.y+"px";
item.style.height="24px";
item.style.width="24px";
item.style.margin="0px";
item.style.padding="0px";
if(this.getImageUrl()!==null){
item.style.backgroundImage="url("+this.getImageUrl()+")";
}else{
item.style.backgroundImage="";
}
var oThis=this;
this.click=function(event){
if(oThis.enabled){
oThis.palette.setActiveTool(oThis);
}
event.cancelBubble=true;
event.returnValue=false;
};
if(item.addEventListener){
item.addEventListener("click",this.click,false);
}else{
if(item.attachEvent){
item.attachEvent("onclick",this.click);
}
}
if(this.tooltip!==null){
item.title=this.tooltip;
}else{
item.title="";
}
return item;
};
draw2d.ToolGeneric.prototype.getHTMLElement=function(){
if(this.html===null){
this.html=this.createHTMLElement();
}
return this.html;
};
draw2d.ToolGeneric.prototype.execute=function(x,y){
if(this.enabled){
this.palette.setActiveTool(null);
}
};
draw2d.ToolGeneric.prototype.setTooltip=function(_58ad){
this.tooltip=_58ad;
if(this.tooltip!==null){
this.html.title=this.tooltip;
}else{
this.html.title="";
}
};
draw2d.ToolGeneric.prototype.setActive=function(flag){
if(!this.enabled){
return;
}
if(flag===true){
this.html.style.border="1px inset";
}else{
this.html.style.border="0px";
}
};
draw2d.ToolGeneric.prototype.setEnabled=function(flag){
this.enabled=flag;
if(flag){
this.html.style.filter="alpha(opacity=100)";
this.html.style.opacity="1.0";
}else{
this.html.style.filter="alpha(opacity=30)";
this.html.style.opacity="0.3";
}
};
draw2d.ToolGeneric.prototype.setDimension=function(w,h){
this.width=w;
this.height=h;
if(this.html===null){
return;
}
this.html.style.width=this.width+"px";
this.html.style.height=this.height+"px";
};
draw2d.ToolGeneric.prototype.setPosition=function(xPos,yPos){
this.x=Math.max(0,xPos);
this.y=Math.max(0,yPos);
if(this.html===null){
return;
}
this.html.style.left=this.x+"px";
this.html.style.top=this.y+"px";
};
draw2d.ToolGeneric.prototype.getWidth=function(){
return this.width;
};
draw2d.ToolGeneric.prototype.getHeight=function(){
return this.height;
};
draw2d.ToolGeneric.prototype.getY=function(){
return this.y;
};
draw2d.ToolGeneric.prototype.getX=function(){
return this.x;
};
draw2d.ToolGeneric.prototype.getPosition=function(){
return new draw2d.Point(this.x,this.y);
};
draw2d.ToolPalette=function(title){
draw2d.WindowFigure.call(this,title);
this.setDimension(75,400);
this.activeTool=null;
this.children={};
};
draw2d.ToolPalette.prototype=new draw2d.WindowFigure();
draw2d.ToolPalette.prototype.type="draw2d.ToolPalette";
draw2d.ToolPalette.prototype.dispose=function(){
draw2d.WindowFigure.prototype.dispose.call(this);
};
draw2d.ToolPalette.prototype.createHTMLElement=function(){
var item=draw2d.WindowFigure.prototype.createHTMLElement.call(this);
this.scrollarea=document.createElement("div");
this.scrollarea.style.position="absolute";
this.scrollarea.style.left="0px";
if(this.hasTitleBar()){
this.scrollarea.style.top="15px";
}else{
this.scrollarea.style.top="0px";
}
this.scrollarea.style.width=this.getWidth()+"px";
this.scrollarea.style.height="15px";
this.scrollarea.style.margin="0px";
this.scrollarea.style.padding="0px";
this.scrollarea.style.font="normal 10px verdana";
this.scrollarea.style.borderBottom="2px solid gray";
this.scrollarea.style.whiteSpace="nowrap";
this.scrollarea.style.textAlign="center";
this.scrollarea.style.overflowX="auto";
this.scrollarea.style.overflowY="auto";
this.scrollarea.style.overflow="auto";
item.appendChild(this.scrollarea);
return item;
};
draw2d.ToolPalette.prototype.setDimension=function(w,h){
draw2d.WindowFigure.prototype.setDimension.call(this,w,h);
if(this.scrollarea!==null){
this.scrollarea.style.width=this.getWidth()+"px";
if(this.hasTitleBar()){
this.scrollarea.style.height=(this.getHeight()-15)+"px";
}else{
this.scrollarea.style.height=this.getHeight()+"px";
}
}
};
draw2d.ToolPalette.prototype.addChild=function(item){
this.children[item.id]=item;
this.scrollarea.appendChild(item.getHTMLElement());
};
draw2d.ToolPalette.prototype.getChild=function(id){
return this.children[id];
};
draw2d.ToolPalette.prototype.getActiveTool=function(){
return this.activeTool;
};
draw2d.ToolPalette.prototype.setActiveTool=function(tool){
if(this.activeTool!=tool&&this.activeTool!==null){
this.activeTool.setActive(false);
}
if(tool!==null){
tool.setActive(true);
}
this.activeTool=tool;
};
draw2d.Dialog=function(title){
this.buttonbar=null;
if(title){
draw2d.WindowFigure.call(this,title);
}else{
draw2d.WindowFigure.call(this,"Dialog");
}
this.setDimension(400,300);
};
draw2d.Dialog.prototype=new draw2d.WindowFigure();
draw2d.Dialog.prototype.type="draw2d.Dialog";
draw2d.Dialog.prototype.createHTMLElement=function(){
var item=draw2d.WindowFigure.prototype.createHTMLElement.call(this);
var oThis=this;
this.buttonbar=document.createElement("div");
this.buttonbar.style.position="absolute";
this.buttonbar.style.left="0px";
this.buttonbar.style.bottom="0px";
this.buttonbar.style.width=this.getWidth()+"px";
this.buttonbar.style.height="30px";
this.buttonbar.style.margin="0px";
this.buttonbar.style.padding="0px";
this.buttonbar.style.font="normal 10px verdana";
this.buttonbar.style.backgroundColor="#c0c0c0";
this.buttonbar.style.borderBottom="2px solid gray";
this.buttonbar.style.whiteSpace="nowrap";
this.buttonbar.style.textAlign="center";
this.buttonbar.className="Dialog_buttonbar";
this.okbutton=document.createElement("button");
this.okbutton.style.border="1px solid gray";
this.okbutton.style.font="normal 10px verdana";
this.okbutton.style.width="80px";
this.okbutton.style.margin="5px";
this.okbutton.className="Dialog_okbutton";
this.okbutton.innerHTML="Ok";
this.okbutton.onclick=function(){
var error=null;
try{
oThis.onOk();
}
catch(e){
error=e;
}
oThis.workflow.removeFigure(oThis);
if(error!==null){
throw error;
}
};
this.buttonbar.appendChild(this.okbutton);
this.cancelbutton=document.createElement("button");
this.cancelbutton.innerHTML="Cancel";
this.cancelbutton.style.font="normal 10px verdana";
this.cancelbutton.style.border="1px solid gray";
this.cancelbutton.style.width="80px";
this.cancelbutton.style.margin="5px";
this.cancelbutton.className="Dialog_cancelbutton";
this.cancelbutton.onclick=function(){
var error=null;
try{
oThis.onCancel();
}
catch(e){
error=e;
}
oThis.workflow.removeFigure(oThis);
if(error!==null){
throw error;
}
};
this.buttonbar.appendChild(this.cancelbutton);
item.appendChild(this.buttonbar);
return item;
};
draw2d.Dialog.prototype.onOk=function(){
};
draw2d.Dialog.prototype.onCancel=function(){
};
draw2d.Dialog.prototype.setDimension=function(w,h){
draw2d.WindowFigure.prototype.setDimension.call(this,w,h);
if(this.buttonbar!==null){
this.buttonbar.style.width=this.getWidth()+"px";
}
};
draw2d.Dialog.prototype.setWorkflow=function(_5afa){
draw2d.WindowFigure.prototype.setWorkflow.call(this,_5afa);
this.setFocus();
};
draw2d.Dialog.prototype.setFocus=function(){
};
draw2d.Dialog.prototype.onSetDocumentDirty=function(){
};
draw2d.InputDialog=function(){
draw2d.Dialog.call(this);
this.setDimension(400,100);
};
draw2d.InputDialog.prototype=new draw2d.Dialog();
draw2d.InputDialog.prototype.type="draw2d.InputDialog";
draw2d.InputDialog.prototype.createHTMLElement=function(){
var item=draw2d.Dialog.prototype.createHTMLElement.call(this);
return item;
};
draw2d.InputDialog.prototype.onOk=function(){
this.workflow.removeFigure(this);
};
draw2d.InputDialog.prototype.onCancel=function(){
this.workflow.removeFigure(this);
};
draw2d.PropertyDialog=function(_4b52,_4b53,label){
this.figure=_4b52;
this.propertyName=_4b53;
this.label=label;
draw2d.Dialog.call(this);
this.setDimension(400,120);
};
draw2d.PropertyDialog.prototype=new draw2d.Dialog();
draw2d.PropertyDialog.prototype.type="draw2d.PropertyDialog";
draw2d.PropertyDialog.prototype.createHTMLElement=function(){
var item=draw2d.Dialog.prototype.createHTMLElement.call(this);
var _4b56=document.createElement("form");
_4b56.style.position="absolute";
_4b56.style.left="10px";
_4b56.style.top="30px";
_4b56.style.width="375px";
_4b56.style.font="normal 10px verdana";
item.appendChild(_4b56);
this.labelDiv=document.createElement("div");
this.labelDiv.innerHTML=this.label;
this.disableTextSelection(this.labelDiv);
_4b56.appendChild(this.labelDiv);
this.input=document.createElement("input");
this.input.style.border="1px solid gray";
this.input.style.font="normal 10px verdana";
this.input.type="text";
var value=this.figure.getProperty(this.propertyName);
if(value){
this.input.value=value;
}else{
this.input.value="";
}
this.input.style.width="100%";
_4b56.appendChild(this.input);
this.input.focus();
return item;
};
draw2d.PropertyDialog.prototype.onOk=function(){
draw2d.Dialog.prototype.onOk.call(this);
this.figure.setProperty(this.propertyName,this.input.value);
};
draw2d.AnnotationDialog=function(_57e2){
this.figure=_57e2;
draw2d.Dialog.call(this);
this.setDimension(400,100);
};
draw2d.AnnotationDialog.prototype=new draw2d.Dialog();
draw2d.AnnotationDialog.prototype.type="draw2d.AnnotationDialog";
draw2d.AnnotationDialog.prototype.createHTMLElement=function(){
var item=draw2d.Dialog.prototype.createHTMLElement.call(this);
var _57e4=document.createElement("form");
_57e4.style.position="absolute";
_57e4.style.left="10px";
_57e4.style.top="30px";
_57e4.style.width="375px";
_57e4.style.font="normal 10px verdana";
item.appendChild(_57e4);
this.label=document.createTextNode("Text");
_57e4.appendChild(this.label);
this.input=document.createElement("input");
this.input.style.border="1px solid gray";
this.input.style.font="normal 10px verdana";
this.input.type="text";
var value=this.figure.getText();
if(value){
this.input.value=value;
}else{
this.input.value="";
}
this.input.style.width="100%";
_57e4.appendChild(this.input);
this.input.focus();
return item;
};
draw2d.AnnotationDialog.prototype.onOk=function(){
this.workflow.getCommandStack().execute(new draw2d.CommandSetText(this.figure,this.input.value));
this.workflow.removeFigure(this);
};
draw2d.PropertyWindow=function(){
this.currentSelection=null;
draw2d.WindowFigure.call(this,"Property Window");
this.setDimension(200,100);
};
draw2d.PropertyWindow.prototype=new draw2d.WindowFigure();
draw2d.PropertyWindow.prototype.type="draw2d.PropertyWindow";
draw2d.PropertyWindow.prototype.dispose=function(){
draw2d.WindowFigure.prototype.dispose.call(this);
};
draw2d.PropertyWindow.prototype.createHTMLElement=function(){
var item=draw2d.WindowFigure.prototype.createHTMLElement.call(this);
item.appendChild(this.createLabel("Type:",15,25));
item.appendChild(this.createLabel("X :",15,50));
item.appendChild(this.createLabel("Y :",15,70));
item.appendChild(this.createLabel("Width :",85,50));
item.appendChild(this.createLabel("Height :",85,70));
this.labelType=this.createLabel("",50,25);
this.labelX=this.createLabel("",40,50);
this.labelY=this.createLabel("",40,70);
this.labelWidth=this.createLabel("",135,50);
this.labelHeight=this.createLabel("",135,70);
this.labelType.style.fontWeight="normal";
this.labelX.style.fontWeight="normal";
this.labelY.style.fontWeight="normal";
this.labelWidth.style.fontWeight="normal";
this.labelHeight.style.fontWeight="normal";
item.appendChild(this.labelType);
item.appendChild(this.labelX);
item.appendChild(this.labelY);
item.appendChild(this.labelWidth);
item.appendChild(this.labelHeight);
return item;
};
draw2d.PropertyWindow.prototype.onSelectionChanged=function(_4b59){
draw2d.WindowFigure.prototype.onSelectionChanged.call(this,_4b59);
if(this.currentSelection!==null){
this.currentSelection.detachMoveListener(this);
}
this.currentSelection=_4b59;
if(_4b59!==null&&_4b59!=this){
this.labelType.innerHTML=_4b59.type;
if(_4b59.getX){
this.labelX.innerHTML=_4b59.getX();
this.labelY.innerHTML=_4b59.getY();
this.labelWidth.innerHTML=_4b59.getWidth();
this.labelHeight.innerHTML=_4b59.getHeight();
this.currentSelection=_4b59;
this.currentSelection.attachMoveListener(this);
}else{
this.labelX.innerHTML="";
this.labelY.innerHTML="";
this.labelWidth.innerHTML="";
this.labelHeight.innerHTML="";
}
}else{
this.labelType.innerHTML="&lt;none&gt;";
this.labelX.innerHTML="";
this.labelY.innerHTML="";
this.labelWidth.innerHTML="";
this.labelHeight.innerHTML="";
}
};
draw2d.PropertyWindow.prototype.getCurrentSelection=function(){
return this.currentSelection;
};
draw2d.PropertyWindow.prototype.onOtherFigureMoved=function(_4b5a){
if(_4b5a==this.currentSelection){
this.onSelectionChanged(_4b5a);
}
};
draw2d.PropertyWindow.prototype.createLabel=function(text,x,y){
var l=document.createElement("div");
l.style.position="absolute";
l.style.left=x+"px";
l.style.top=y+"px";
l.style.font="normal 10px verdana";
l.style.whiteSpace="nowrap";
l.style.fontWeight="bold";
l.innerHTML=text;
return l;
};
draw2d.ColorDialog=function(){
this.maxValue={"h":"359","s":"100","v":"100"};
this.HSV={0:359,1:100,2:100};
this.slideHSV={0:359,1:100,2:100};
this.SVHeight=165;
this.wSV=162;
this.wH=162;
draw2d.Dialog.call(this,"Color Chooser");
this.loadSV();
this.setColor(new draw2d.Color(255,0,0));
this.setDimension(219,244);
};
draw2d.ColorDialog.prototype=new draw2d.Dialog();
draw2d.ColorDialog.prototype.type="draw2d.ColorDialog";
draw2d.ColorDialog.prototype.createHTMLElement=function(){
var oThis=this;
var item=draw2d.Dialog.prototype.createHTMLElement.call(this);
this.outerDiv=document.createElement("div");
this.outerDiv.id="plugin";
this.outerDiv.style.top="15px";
this.outerDiv.style.left="0px";
this.outerDiv.style.width="201px";
this.outerDiv.style.position="absolute";
this.outerDiv.style.padding="9px";
this.outerDiv.display="block";
this.outerDiv.style.background="#0d0d0d";
this.plugHEX=document.createElement("div");
this.plugHEX.id="plugHEX";
this.plugHEX.innerHTML="F1FFCC";
this.plugHEX.style.color="white";
this.plugHEX.style.font="normal 10px verdana";
this.outerDiv.appendChild(this.plugHEX);
this.SV=document.createElement("div");
this.SV.onmousedown=function(event){
oThis.mouseDownSV(oThis.SVslide,event);
};
this.SV.id="SV";
this.SV.style.cursor="crosshair";
this.SV.style.background="#FF0000 url(SatVal.png)";
this.SV.style.position="absolute";
this.SV.style.height="166px";
this.SV.style.width="167px";
this.SV.style.marginRight="10px";
this.SV.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='SatVal.png', sizingMethod='scale')";
this.SV.style["float"]="left";
this.outerDiv.appendChild(this.SV);
this.SVslide=document.createElement("div");
this.SVslide.onmousedown=function(event){
oThis.mouseDownSV(event);
};
this.SVslide.style.top="40px";
this.SVslide.style.left="40px";
this.SVslide.style.position="absolute";
this.SVslide.style.cursor="crosshair";
this.SVslide.style.background="url(slide.gif)";
this.SVslide.style.height="9px";
this.SVslide.style.width="9px";
this.SVslide.style.lineHeight="1px";
this.outerDiv.appendChild(this.SVslide);
this.H=document.createElement("form");
this.H.id="H";
this.H.onmousedown=function(event){
oThis.mouseDownH(event);
};
this.H.style.border="1px solid #000000";
this.H.style.cursor="crosshair";
this.H.style.position="absolute";
this.H.style.width="19px";
this.H.style.top="28px";
this.H.style.left="191px";
this.outerDiv.appendChild(this.H);
this.Hslide=document.createElement("div");
this.Hslide.style.top="-7px";
this.Hslide.style.left="-8px";
this.Hslide.style.background="url(slideHue.gif)";
this.Hslide.style.height="5px";
this.Hslide.style.width="33px";
this.Hslide.style.position="absolute";
this.Hslide.style.lineHeight="1px";
this.H.appendChild(this.Hslide);
this.Hmodel=document.createElement("div");
this.Hmodel.style.height="1px";
this.Hmodel.style.width="19px";
this.Hmodel.style.lineHeight="1px";
this.Hmodel.style.margin="0px";
this.Hmodel.style.padding="0px";
this.Hmodel.style.fontSize="1px";
this.H.appendChild(this.Hmodel);
item.appendChild(this.outerDiv);
return item;
};
draw2d.ColorDialog.prototype.onOk=function(){
draw2d.Dialog.prototype.onOk.call(this);
};
draw2d.browser=function(v){
return (Math.max(navigator.userAgent.toLowerCase().indexOf(v),0));
};
draw2d.ColorDialog.prototype.showColor=function(c){
this.plugHEX.style.background="#"+c;
this.plugHEX.innerHTML=c;
};
draw2d.ColorDialog.prototype.getSelectedColor=function(){
var rgb=this.hex2rgb(this.plugHEX.innerHTML);
return new draw2d.Color(rgb[0],rgb[1],rgb[2]);
};
draw2d.ColorDialog.prototype.setColor=function(color){
if(color===null){
color=new draw2d.Color(100,100,100);
}
var hex=this.rgb2hex(Array(color.getRed(),color.getGreen(),color.getBlue()));
this.updateH(hex);
};
draw2d.ColorDialog.prototype.XY=function(e,v){
var z=draw2d.browser("msie")?Array(event.clientX+document.body.scrollLeft,event.clientY+document.body.scrollTop):Array(e.pageX,e.pageY);
return z[v];
};
draw2d.ColorDialog.prototype.mkHSV=function(a,b,c){
return (Math.min(a,Math.max(0,Math.ceil((parseInt(c)/b)*a))));
};
draw2d.ColorDialog.prototype.ckHSV=function(a,b){
if(a>=0&&a<=b){
return (a);
}else{
if(a>b){
return (b);
}else{
if(a<0){
return ("-"+oo);
}
}
}
};
draw2d.ColorDialog.prototype.mouseDownH=function(e){
this.slideHSV[0]=this.HSV[0];
var oThis=this;
this.H.onmousemove=function(e){
oThis.dragH(e);
};
this.H.onmouseup=function(e){
oThis.H.onmousemove="";
oThis.H.onmouseup="";
};
this.dragH(e);
};
draw2d.ColorDialog.prototype.dragH=function(e){
var y=this.XY(e,1)-this.getY()-40;
this.Hslide.style.top=(this.ckHSV(y,this.wH)-5)+"px";
this.slideHSV[0]=this.mkHSV(359,this.wH,this.Hslide.style.top);
this.updateSV();
this.showColor(this.commit());
this.SV.style.backgroundColor="#"+this.hsv2hex(Array(this.HSV[0],100,100));
};
draw2d.ColorDialog.prototype.mouseDownSV=function(o,e){
this.slideHSV[0]=this.HSV[0];
var oThis=this;
function reset(){
oThis.SV.onmousemove="";
oThis.SV.onmouseup="";
oThis.SVslide.onmousemove="";
oThis.SVslide.onmouseup="";
}
this.SV.onmousemove=function(e){
oThis.dragSV(e);
};
this.SV.onmouseup=reset;
this.SVslide.onmousemove=function(e){
oThis.dragSV(e);
};
this.SVslide.onmouseup=reset;
this.dragSV(e);
};
draw2d.ColorDialog.prototype.dragSV=function(e){
var x=this.XY(e,0)-this.getX()-1;
var y=this.XY(e,1)-this.getY()-20;
this.SVslide.style.left=this.ckHSV(x,this.wSV)+"px";
this.SVslide.style.top=this.ckHSV(y,this.wSV)+"px";
this.slideHSV[1]=this.mkHSV(100,this.wSV,this.SVslide.style.left);
this.slideHSV[2]=100-this.mkHSV(100,this.wSV,this.SVslide.style.top);
this.updateSV();
};
draw2d.ColorDialog.prototype.commit=function(){
var r="hsv";
var z={};
var j="";
for(var i=0;i<=r.length-1;i++){
j=r.substr(i,1);
z[i]=(j=="h")?this.maxValue[j]-this.mkHSV(this.maxValue[j],this.wH,this.Hslide.style.top):this.HSV[i];
}
return (this.updateSV(this.hsv2hex(z)));
};
draw2d.ColorDialog.prototype.updateSV=function(v){
this.HSV=v?this.hex2hsv(v):Array(this.slideHSV[0],this.slideHSV[1],this.slideHSV[2]);
if(!v){
v=this.hsv2hex(Array(this.slideHSV[0],this.slideHSV[1],this.slideHSV[2]));
}
this.showColor(v);
return v;
};
draw2d.ColorDialog.prototype.loadSV=function(){
var z="";
for(var i=this.SVHeight;i>=0;i--){
z+="<div style=\"background:#"+this.hsv2hex(Array(Math.round((359/this.SVHeight)*i),100,100))+";\"><br/></div>";
}
this.Hmodel.innerHTML=z;
};
draw2d.ColorDialog.prototype.updateH=function(v){
this.plugHEX.innerHTML=v;
this.HSV=this.hex2hsv(v);
this.SV.style.backgroundColor="#"+this.hsv2hex(Array(this.HSV[0],100,100));
this.SVslide.style.top=(parseInt(this.wSV-this.wSV*(this.HSV[1]/100))+20)+"px";
this.SVslide.style.left=(parseInt(this.wSV*(this.HSV[1]/100))+5)+"px";
this.Hslide.style.top=(parseInt(this.wH*((this.maxValue["h"]-this.HSV[0])/this.maxValue["h"]))-7)+"px";
};
draw2d.ColorDialog.prototype.toHex=function(v){
v=Math.round(Math.min(Math.max(0,v),255));
return ("0123456789ABCDEF".charAt((v-v%16)/16)+"0123456789ABCDEF".charAt(v%16));
};
draw2d.ColorDialog.prototype.hex2rgb=function(r){
return ({0:parseInt(r.substr(0,2),16),1:parseInt(r.substr(2,2),16),2:parseInt(r.substr(4,2),16)});
};
draw2d.ColorDialog.prototype.rgb2hex=function(r){
return (this.toHex(r[0])+this.toHex(r[1])+this.toHex(r[2]));
};
draw2d.ColorDialog.prototype.hsv2hex=function(h){
return (this.rgb2hex(this.hsv2rgb(h)));
};
draw2d.ColorDialog.prototype.hex2hsv=function(v){
return (this.rgb2hsv(this.hex2rgb(v)));
};
draw2d.ColorDialog.prototype.rgb2hsv=function(r){
var max=Math.max(r[0],r[1],r[2]);
var delta=max-Math.min(r[0],r[1],r[2]);
var H;
var S;
var V;
if(max!=0){
S=Math.round(delta/max*100);
if(r[0]==max){
H=(r[1]-r[2])/delta;
}else{
if(r[1]==max){
H=2+(r[2]-r[0])/delta;
}else{
if(r[2]==max){
H=4+(r[0]-r[1])/delta;
}
}
}
var H=Math.min(Math.round(H*60),360);
if(H<0){
H+=360;
}
}
return ({0:H?H:0,1:S?S:0,2:Math.round((max/255)*100)});
};
draw2d.ColorDialog.prototype.hsv2rgb=function(r){
var R;
var B;
var G;
var S=r[1]/100;
var V=r[2]/100;
var H=r[0]/360;
if(S>0){
if(H>=1){
H=0;
}
H=6*H;
F=H-Math.floor(H);
A=Math.round(255*V*(1-S));
B=Math.round(255*V*(1-(S*F)));
C=Math.round(255*V*(1-(S*(1-F))));
V=Math.round(255*V);
switch(Math.floor(H)){
case 0:
R=V;
G=C;
B=A;
break;
case 1:
R=B;
G=V;
B=A;
break;
case 2:
R=A;
G=V;
B=C;
break;
case 3:
R=A;
G=B;
B=V;
break;
case 4:
R=C;
G=A;
B=V;
break;
case 5:
R=V;
G=A;
B=B;
break;
}
return ({0:R?R:0,1:G?G:0,2:B?B:0});
}else{
return ({0:(V=Math.round(V*255)),1:V,2:V});
}
};
draw2d.LineColorDialog=function(_5a35){
draw2d.ColorDialog.call(this);
this.figure=_5a35;
var color=_5a35.getColor();
this.updateH(this.rgb2hex(color.getRed(),color.getGreen(),color.getBlue()));
};
draw2d.LineColorDialog.prototype=new draw2d.ColorDialog();
draw2d.LineColorDialog.prototype.type="draw2d.LineColorDialog";
draw2d.LineColorDialog.prototype.onOk=function(){
var _5a37=this.workflow;
draw2d.ColorDialog.prototype.onOk.call(this);
if(typeof this.figure.setColor=="function"){
_5a37.getCommandStack().execute(new draw2d.CommandSetColor(this.figure,this.getSelectedColor()));
if(_5a37.getCurrentSelection()==this.figure){
_5a37.setCurrentSelection(this.figure);
}
}
};
draw2d.BackgroundColorDialog=function(_5b1f){
draw2d.ColorDialog.call(this);
this.figure=_5b1f;
var color=_5b1f.getBackgroundColor();
if(color!==null){
this.updateH(this.rgb2hex(color.getRed(),color.getGreen(),color.getBlue()));
}
};
draw2d.BackgroundColorDialog.prototype=new draw2d.ColorDialog();
draw2d.BackgroundColorDialog.prototype.type="draw2d.BackgroundColorDialog";
draw2d.BackgroundColorDialog.prototype.onOk=function(){
var _5b21=this.workflow;
draw2d.ColorDialog.prototype.onOk.call(this);
if(typeof this.figure.setBackgroundColor=="function"){
_5b21.getCommandStack().execute(new draw2d.CommandSetBackgroundColor(this.figure,this.getSelectedColor()));
if(_5b21.getCurrentSelection()==this.figure){
_5b21.setCurrentSelection(this.figure);
}
}
};
draw2d.AnnotationDialog=function(_57e2){
this.figure=_57e2;
draw2d.Dialog.call(this);
this.setDimension(400,100);
};
draw2d.AnnotationDialog.prototype=new draw2d.Dialog();
draw2d.AnnotationDialog.prototype.type="draw2d.AnnotationDialog";
draw2d.AnnotationDialog.prototype.createHTMLElement=function(){
var item=draw2d.Dialog.prototype.createHTMLElement.call(this);
var _57e4=document.createElement("form");
_57e4.style.position="absolute";
_57e4.style.left="10px";
_57e4.style.top="30px";
_57e4.style.width="375px";
_57e4.style.font="normal 10px verdana";
item.appendChild(_57e4);
this.label=document.createTextNode("Text");
_57e4.appendChild(this.label);
this.input=document.createElement("input");
this.input.style.border="1px solid gray";
this.input.style.font="normal 10px verdana";
this.input.type="text";
var value=this.figure.getText();
if(value){
this.input.value=value;
}else{
this.input.value="";
}
this.input.style.width="100%";
_57e4.appendChild(this.input);
this.input.focus();
return item;
};
draw2d.AnnotationDialog.prototype.onOk=function(){
this.workflow.getCommandStack().execute(new draw2d.CommandSetText(this.figure,this.input.value));
this.workflow.removeFigure(this);
};
draw2d.Command=function(label){
this.label=label;
};
draw2d.Command.prototype.type="draw2d.Command";
draw2d.Command.prototype.getLabel=function(){
return this.label;
};
draw2d.Command.prototype.canExecute=function(){
return true;
};
draw2d.Command.prototype.execute=function(){
};
draw2d.Command.prototype.cancel=function(){
};
draw2d.Command.prototype.undo=function(){
};
draw2d.Command.prototype.redo=function(){
};
draw2d.CommandStack=function(){
this.undostack=[];
this.redostack=[];
this.maxundo=50;
this.eventListeners=new draw2d.ArrayList();
};
draw2d.CommandStack.PRE_EXECUTE=1;
draw2d.CommandStack.PRE_REDO=2;
draw2d.CommandStack.PRE_UNDO=4;
draw2d.CommandStack.POST_EXECUTE=8;
draw2d.CommandStack.POST_REDO=16;
draw2d.CommandStack.POST_UNDO=32;
draw2d.CommandStack.POST_MASK=draw2d.CommandStack.POST_EXECUTE|draw2d.CommandStack.POST_UNDO|draw2d.CommandStack.POST_REDO;
draw2d.CommandStack.PRE_MASK=draw2d.CommandStack.PRE_EXECUTE|draw2d.CommandStack.PRE_UNDO|draw2d.CommandStack.PRE_REDO;
draw2d.CommandStack.prototype.type="draw2d.CommandStack";
draw2d.CommandStack.prototype.setUndoLimit=function(count){
this.maxundo=count;
};
draw2d.CommandStack.prototype.markSaveLocation=function(){
this.undostack=[];
this.redostack=[];
};
draw2d.CommandStack.prototype.execute=function(_557a){
if(_557a===null){
return;
}
if(_557a.canExecute()==false){
return;
}
this.notifyListeners(_557a,draw2d.CommandStack.PRE_EXECUTE);
this.undostack.push(_557a);
_557a.execute();
this.redostack=[];
if(this.undostack.length>this.maxundo){
this.undostack=this.undostack.slice(this.undostack.length-this.maxundo);
}
this.notifyListeners(_557a,draw2d.CommandStack.POST_EXECUTE);
};
draw2d.CommandStack.prototype.undo=function(){
var _557b=this.undostack.pop();
if(_557b){
this.notifyListeners(_557b,draw2d.CommandStack.PRE_UNDO);
this.redostack.push(_557b);
_557b.undo();
this.notifyListeners(_557b,draw2d.CommandStack.POST_UNDO);
}
};
draw2d.CommandStack.prototype.redo=function(){
var _557c=this.redostack.pop();
if(_557c){
this.notifyListeners(_557c,draw2d.CommandStack.PRE_REDO);
this.undostack.push(_557c);
_557c.redo();
this.notifyListeners(_557c,draw2d.CommandStack.POST_REDO);
}
};
draw2d.CommandStack.prototype.canRedo=function(){
return this.redostack.length>0;
};
draw2d.CommandStack.prototype.canUndo=function(){
return this.undostack.length>0;
};
draw2d.CommandStack.prototype.addCommandStackEventListener=function(_557d){
this.eventListeners.add(_557d);
};
draw2d.CommandStack.prototype.removeCommandStackEventListener=function(_557e){
this.eventListeners.remove(_557e);
};
draw2d.CommandStack.prototype.notifyListeners=function(_557f,state){
var event=new draw2d.CommandStackEvent(_557f,state);
var size=this.eventListeners.getSize();
for(var i=0;i<size;i++){
this.eventListeners.get(i).stackChanged(event);
}
};
draw2d.CommandStackEvent=function(_507d,_507e){
this.command=_507d;
this.details=_507e;
};
draw2d.CommandStackEvent.prototype.type="draw2d.CommandStackEvent";
draw2d.CommandStackEvent.prototype.getCommand=function(){
return this.command;
};
draw2d.CommandStackEvent.prototype.getDetails=function(){
return this.details;
};
draw2d.CommandStackEvent.prototype.isPostChangeEvent=function(){
return 0!=(this.getDetails()&draw2d.CommandStack.POST_MASK);
};
draw2d.CommandStackEvent.prototype.isPreChangeEvent=function(){
return 0!=(this.getDetails()&draw2d.CommandStack.PRE_MASK);
};
draw2d.CommandStackEventListener=function(){
};
draw2d.CommandStackEventListener.prototype.type="draw2d.CommandStackEventListener";
draw2d.CommandStackEventListener.prototype.stackChanged=function(event){
};
draw2d.CommandAdd=function(_58bb,_58bc,x,y,_58bf){
draw2d.Command.call(this,"add figure");
if(_58bf===undefined){
_58bf=null;
}
this.parent=_58bf;
this.figure=_58bc;
this.x=x;
this.y=y;
this.workflow=_58bb;
};
draw2d.CommandAdd.prototype=new draw2d.Command();
draw2d.CommandAdd.prototype.type="draw2d.CommandAdd";
draw2d.CommandAdd.prototype.execute=function(){
this.redo();
};
draw2d.CommandAdd.prototype.redo=function(){
if(this.x&&this.y){
this.workflow.addFigure(this.figure,this.x,this.y);
}else{
this.workflow.addFigure(this.figure);
}
this.workflow.setCurrentSelection(this.figure);
if(this.parent!==null){
this.parent.addChild(this.figure);
}
};
draw2d.CommandAdd.prototype.undo=function(){
this.workflow.removeFigure(this.figure);
this.workflow.setCurrentSelection(null);
if(this.parent!==null){
this.parent.removeChild(this.figure);
}
};
draw2d.CommandDelete=function(_553d){
draw2d.Command.call(this,"delete figure");
this.parent=_553d.parent;
this.figure=_553d;
this.workflow=_553d.workflow;
this.connections=null;
this.compartmentDeleteCommands=null;
};
draw2d.CommandDelete.prototype=new draw2d.Command();
draw2d.CommandDelete.prototype.type="draw2d.CommandDelete";
draw2d.CommandDelete.prototype.execute=function(){
this.redo();
};
draw2d.CommandDelete.prototype.undo=function(){
if(this.figure instanceof draw2d.CompartmentFigure){
for(var i=0;i<this.compartmentDeleteCommands.getSize();i++){
var _553f=this.compartmentDeleteCommands.get(i);
this.figure.addChild(_553f.figure);
this.workflow.getCommandStack().undo();
}
}
this.workflow.addFigure(this.figure);
if(this.figure instanceof draw2d.Connection){
this.figure.reconnect();
}
this.workflow.setCurrentSelection(this.figure);
if(this.parent!==null){
this.parent.addChild(this.figure);
}
for(var i=0;i<this.connections.getSize();++i){
this.workflow.addFigure(this.connections.get(i));
this.connections.get(i).reconnect();
}
};
draw2d.CommandDelete.prototype.redo=function(){
if(this.figure instanceof draw2d.CompartmentFigure){
if(this.compartmentDeleteCommands===null){
this.compartmentDeleteCommands=new draw2d.ArrayList();
var _5540=this.figure.getChildren().clone();
for(var i=0;i<_5540.getSize();i++){
var child=_5540.get(i);
this.figure.removeChild(child);
var _5543=new draw2d.CommandDelete(child);
this.compartmentDeleteCommands.add(_5543);
this.workflow.getCommandStack().execute(_5543);
}
}else{
for(var i=0;i<this.compartmentDeleteCommands.getSize();i++){
this.workflow.redo();
}
}
}
this.workflow.removeFigure(this.figure);
this.workflow.setCurrentSelection(null);
if(this.figure instanceof draw2d.Node&&this.connections===null){
this.connections=new draw2d.ArrayList();
var ports=this.figure.getPorts();
for(var i=0;i<ports.getSize();i++){
var port=ports.get(i);
for(var c=0,c_size=port.getConnections().getSize();c<c_size;c++){
if(!this.connections.contains(port.getConnections().get(c))){
this.connections.add(port.getConnections().get(c));
}
}
}
}
if(this.connections===null){
this.connections=new draw2d.ArrayList();
}
if(this.parent!==null){
this.parent.removeChild(this.figure);
}
for(var i=0;i<this.connections.getSize();++i){
this.workflow.removeFigure(this.connections.get(i));
}
};
draw2d.CommandMove=function(_5002,x,y){
draw2d.Command.call(this,"move figure");
this.figure=_5002;
if(x==undefined){
this.oldX=_5002.getX();
this.oldY=_5002.getY();
}else{
this.oldX=x;
this.oldY=y;
}
this.oldCompartment=_5002.getParent();
};
draw2d.CommandMove.prototype=new draw2d.Command();
draw2d.CommandMove.prototype.type="draw2d.CommandMove";
draw2d.CommandMove.prototype.setStartPosition=function(x,y){
this.oldX=x;
this.oldY=y;
};
draw2d.CommandMove.prototype.setPosition=function(x,y){
this.newX=x;
this.newY=y;
this.newCompartment=this.figure.workflow.getBestCompartmentFigure(x,y,this.figure);
};
draw2d.CommandMove.prototype.canExecute=function(){
return this.newX!=this.oldX||this.newY!=this.oldY;
};
draw2d.CommandMove.prototype.execute=function(){
this.redo();
};
draw2d.CommandMove.prototype.undo=function(){
this.figure.setPosition(this.oldX,this.oldY);
if(this.newCompartment!==null){
this.newCompartment.removeChild(this.figure);
}
if(this.oldCompartment!==null){
this.oldCompartment.addChild(this.figure);
}
this.figure.workflow.moveResizeHandles(this.figure);
};
draw2d.CommandMove.prototype.redo=function(){
this.figure.setPosition(this.newX,this.newY);
if(this.oldCompartment!==null){
this.oldCompartment.removeChild(this.figure);
}
if(this.newCompartment!==null){
this.newCompartment.addChild(this.figure);
}
this.figure.workflow.moveResizeHandles(this.figure);
};
draw2d.CommandResize=function(_5573,width,_5575){
draw2d.Command.call(this,"resize figure");
this.figure=_5573;
if(width===undefined){
this.oldWidth=_5573.getWidth();
this.oldHeight=_5573.getHeight();
}else{
this.oldWidth=width;
this.oldHeight=_5575;
}
};
draw2d.CommandResize.prototype=new draw2d.Command();
draw2d.CommandResize.prototype.type="draw2d.CommandResize";
draw2d.CommandResize.prototype.setDimension=function(width,_5577){
this.newWidth=width;
this.newHeight=_5577;
};
draw2d.CommandResize.prototype.canExecute=function(){
return this.newWidth!=this.oldWidth||this.newHeight!=this.oldHeight;
};
draw2d.CommandResize.prototype.execute=function(){
this.redo();
};
draw2d.CommandResize.prototype.undo=function(){
this.figure.setDimension(this.oldWidth,this.oldHeight);
this.figure.workflow.moveResizeHandles(this.figure);
};
draw2d.CommandResize.prototype.redo=function(){
this.figure.setDimension(this.newWidth,this.newHeight);
this.figure.workflow.moveResizeHandles(this.figure);
};
draw2d.CommandSetText=function(_5774,text){
draw2d.Command.call(this,"set text");
this.figure=_5774;
this.newText=text;
this.oldText=_5774.getText();
};
draw2d.CommandSetText.prototype=new draw2d.Command();
draw2d.CommandSetText.prototype.type="draw2d.CommandSetText";
draw2d.CommandSetText.prototype.execute=function(){
this.redo();
};
draw2d.CommandSetText.prototype.redo=function(){
this.figure.setText(this.newText);
};
draw2d.CommandSetText.prototype.undo=function(){
this.figure.setText(this.oldText);
};
draw2d.CommandSetColor=function(_5584,color){
draw2d.Command.call(this,"set color");
this.figure=_5584;
this.newColor=color;
this.oldColor=_5584.getColor();
};
draw2d.CommandSetColor.prototype=new draw2d.Command();
draw2d.CommandSetColor.prototype.type="draw2d.CommandSetColor";
draw2d.CommandSetColor.prototype.execute=function(){
this.redo();
};
draw2d.CommandSetColor.prototype.undo=function(){
this.figure.setColor(this.oldColor);
};
draw2d.CommandSetColor.prototype.redo=function(){
this.figure.setColor(this.newColor);
};
draw2d.CommandSetBackgroundColor=function(_5731,color){
draw2d.Command.call(this,"set background color");
this.figure=_5731;
this.newColor=color;
this.oldColor=_5731.getBackgroundColor();
};
draw2d.CommandSetBackgroundColor.prototype=new draw2d.Command();
draw2d.CommandSetBackgroundColor.prototype.type="draw2d.CommandSetBackgroundColor";
draw2d.CommandSetBackgroundColor.prototype.execute=function(){
this.redo();
};
draw2d.CommandSetBackgroundColor.prototype.undo=function(){
this.figure.setBackgroundColor(this.oldColor);
};
draw2d.CommandSetBackgroundColor.prototype.redo=function(){
this.figure.setBackgroundColor(this.newColor);
};
draw2d.CommandConnect=function(_5954,_5955,_5956){
draw2d.Command.call(this,"create connection");
this.workflow=_5954;
this.source=_5955;
this.target=_5956;
this.connection=null;
};
draw2d.CommandConnect.prototype=new draw2d.Command();
draw2d.CommandConnect.prototype.type="draw2d.CommandConnect";
draw2d.CommandConnect.prototype.setConnection=function(_5957){
this.connection=_5957;
};
draw2d.CommandConnect.prototype.execute=function(){
if(this.connection===null){
this.connection=new draw2d.Connection();
}
this.connection.setSource(this.source);
this.connection.setTarget(this.target);
this.workflow.addFigure(this.connection);
};
draw2d.CommandConnect.prototype.redo=function(){
this.workflow.addFigure(this.connection);
this.connection.reconnect();
};
draw2d.CommandConnect.prototype.undo=function(){
this.workflow.removeFigure(this.connection);
};
draw2d.CommandReconnect=function(con){
draw2d.Command.call(this,"reconnect connection");
this.con=con;
this.oldSourcePort=con.getSource();
this.oldTargetPort=con.getTarget();
this.oldRouter=con.getRouter();
this.con.setRouter(new draw2d.NullConnectionRouter());
};
draw2d.CommandReconnect.prototype=new draw2d.Command();
draw2d.CommandReconnect.prototype.type="draw2d.CommandReconnect";
draw2d.CommandReconnect.prototype.canExecute=function(){
return true;
};
draw2d.CommandReconnect.prototype.setNewPorts=function(_5587,_5588){
this.newSourcePort=_5587;
this.newTargetPort=_5588;
};
draw2d.CommandReconnect.prototype.execute=function(){
this.redo();
};
draw2d.CommandReconnect.prototype.cancel=function(){
var start=this.con.sourceAnchor.getLocation(this.con.targetAnchor.getReferencePoint());
var end=this.con.targetAnchor.getLocation(this.con.sourceAnchor.getReferencePoint());
this.con.setStartPoint(start.x,start.y);
this.con.setEndPoint(end.x,end.y);
this.con.getWorkflow().showLineResizeHandles(this.con);
this.con.setRouter(this.oldRouter);
};
draw2d.CommandReconnect.prototype.undo=function(){
this.con.setSource(this.oldSourcePort);
this.con.setTarget(this.oldTargetPort);
this.con.setRouter(this.oldRouter);
if(this.con.getWorkflow().getCurrentSelection()==this.con){
this.con.getWorkflow().showLineResizeHandles(this.con);
}
};
draw2d.CommandReconnect.prototype.redo=function(){
this.con.setSource(this.newSourcePort);
this.con.setTarget(this.newTargetPort);
this.con.setRouter(this.oldRouter);
if(this.con.getWorkflow().getCurrentSelection()==this.con){
this.con.getWorkflow().showLineResizeHandles(this.con);
}
};
draw2d.CommandMoveLine=function(line,_57a6,_57a7,endX,endY){
draw2d.Command.call(this,"move line");
this.line=line;
this.startX1=_57a6;
this.startY1=_57a7;
this.endX1=endX;
this.endY1=endY;
};
draw2d.CommandMoveLine.prototype=new draw2d.Command();
draw2d.CommandMoveLine.prototype.type="draw2d.CommandMoveLine";
draw2d.CommandMoveLine.prototype.canExecute=function(){
return this.startX1!=this.startX2||this.startY1!=this.startY2||this.endX1!=this.endX2||this.endY1!=this.endY2;
};
draw2d.CommandMoveLine.prototype.execute=function(){
this.startX2=this.line.getStartX();
this.startY2=this.line.getStartY();
this.endX2=this.line.getEndX();
this.endY2=this.line.getEndY();
this.redo();
};
draw2d.CommandMoveLine.prototype.undo=function(){
this.line.setStartPoint(this.startX1,this.startY1);
this.line.setEndPoint(this.endX1,this.endY1);
if(this.line.workflow.getCurrentSelection()==this.line){
this.line.workflow.showLineResizeHandles(this.line);
}
};
draw2d.CommandMoveLine.prototype.redo=function(){
this.line.setStartPoint(this.startX2,this.startY2);
this.line.setEndPoint(this.endX2,this.endY2);
if(this.line.workflow.getCurrentSelection()==this.line){
this.line.workflow.showLineResizeHandles(this.line);
}
};
draw2d.CommandMovePort=function(port){
draw2d.Command.call(this,"move port");
this.port=port;
};
draw2d.CommandMovePort.prototype=new draw2d.Command();
draw2d.CommandMovePort.prototype.type="draw2d.CommandMovePort";
draw2d.CommandMovePort.prototype.execute=function(){
this.port.setAlpha(1);
this.port.setPosition(this.port.originX,this.port.originY);
this.port.parentNode.workflow.hideConnectionLine();
};
draw2d.CommandMovePort.prototype.undo=function(){
};
draw2d.CommandMovePort.prototype.redo=function(){
};
draw2d.CommandMovePort.prototype.setPosition=function(x,y){
};
draw2d.Menu=function(){
this.menuItems=new draw2d.ArrayList();
draw2d.Figure.call(this);
this.setSelectable(false);
this.setDeleteable(false);
this.setCanDrag(false);
this.setResizeable(false);
this.setSelectable(false);
this.setZOrder(10000);
this.dirty=false;
};
draw2d.Menu.prototype=new draw2d.Figure();
draw2d.Menu.prototype.type="draw2d.Menu";
draw2d.Menu.prototype.createHTMLElement=function(){
var item=document.createElement("div");
item.style.position="absolute";
item.style.left=this.x+"px";
item.style.top=this.y+"px";
item.style.margin="0px";
item.style.padding="0px";
item.style.zIndex=""+draw2d.Figure.ZOrderBaseIndex;
item.style.border="1px solid gray";
item.style.background="lavender";
item.style.cursor="pointer";
item.style.width="auto";
item.style.height="auto";
item.className="Menu";
return item;
};
draw2d.Menu.prototype.setWorkflow=function(_5088){
this.workflow=_5088;
};
draw2d.Menu.prototype.setDimension=function(w,h){
};
draw2d.Menu.prototype.appendMenuItem=function(item){
this.menuItems.add(item);
item.parentMenu=this;
this.dirty=true;
};
draw2d.Menu.prototype.getHTMLElement=function(){
var html=draw2d.Figure.prototype.getHTMLElement.call(this);
if(this.dirty){
this.createList();
}
return html;
};
draw2d.Menu.prototype.createList=function(){
this.dirty=false;
this.html.innerHTML="";
var oThis=this;
for(var i=0;i<this.menuItems.getSize();i++){
var item=this.menuItems.get(i);
var li=document.createElement("a");
li.innerHTML=item.getLabel();
li.style.display="block";
li.style.fontFamily="Verdana, Arial, Helvetica, sans-serif";
li.style.fontSize="9pt";
li.style.color="dimgray";
li.style.borderBottom="1px solid silver";
li.style.paddingLeft="5px";
li.style.paddingRight="5px";
li.style.whiteSpace="nowrap";
li.style.cursor="pointer";
li.className="MenuItem";
this.html.appendChild(li);
li.menuItem=item;
if(li.addEventListener){
li.addEventListener("click",function(event){
var _5092=arguments[0]||window.event;
_5092.cancelBubble=true;
_5092.returnValue=false;
var diffX=_5092.clientX;
var diffY=_5092.clientY;
var _5095=document.body.parentNode.scrollLeft;
var _5096=document.body.parentNode.scrollTop;
this.menuItem.execute(diffX+_5095,diffY+_5096);
},false);
li.addEventListener("mouseup",function(event){
event.cancelBubble=true;
event.returnValue=false;
},false);
li.addEventListener("mousedown",function(event){
event.cancelBubble=true;
event.returnValue=false;
},false);
li.addEventListener("mouseover",function(event){
this.style.backgroundColor="silver";
},false);
li.addEventListener("mouseout",function(event){
this.style.backgroundColor="transparent";
},false);
}else{
if(li.attachEvent){
li.attachEvent("onclick",function(event){
var _509c=arguments[0]||window.event;
_509c.cancelBubble=true;
_509c.returnValue=false;
var diffX=_509c.clientX;
var diffY=_509c.clientY;
var _509f=document.body.parentNode.scrollLeft;
var _50a0=document.body.parentNode.scrollTop;
event.srcElement.menuItem.execute(diffX+_509f,diffY+_50a0);
});
li.attachEvent("onmousedown",function(event){
event.cancelBubble=true;
event.returnValue=false;
});
li.attachEvent("onmouseup",function(event){
event.cancelBubble=true;
event.returnValue=false;
});
li.attachEvent("onmouseover",function(event){
event.srcElement.style.backgroundColor="silver";
});
li.attachEvent("onmouseout",function(event){
event.srcElement.style.backgroundColor="transparent";
});
}
}
}
};
draw2d.MenuItem=function(label,_4f06,_4f07){
this.label=label;
this.iconUrl=_4f06;
this.parentMenu=null;
this.action=_4f07;
};
draw2d.MenuItem.prototype.type="draw2d.MenuItem";
draw2d.MenuItem.prototype.isEnabled=function(){
return true;
};
draw2d.MenuItem.prototype.getLabel=function(){
return this.label;
};
draw2d.MenuItem.prototype.execute=function(x,y){
this.parentMenu.workflow.showMenu(null);
this.action(x,y);
};
draw2d.Locator=function(){
};
draw2d.Locator.prototype.type="draw2d.Locator";
draw2d.Locator.prototype.relocate=function(_507a){
};
draw2d.ConnectionLocator=function(_57d8){
draw2d.Locator.call(this);
this.connection=_57d8;
};
draw2d.ConnectionLocator.prototype=new draw2d.Locator;
draw2d.ConnectionLocator.prototype.type="draw2d.ConnectionLocator";
draw2d.ConnectionLocator.prototype.getConnection=function(){
return this.connection;
};
draw2d.ManhattanMidpointLocator=function(_6040){
draw2d.ConnectionLocator.call(this,_6040);
};
draw2d.ManhattanMidpointLocator.prototype=new draw2d.ConnectionLocator;
draw2d.ManhattanMidpointLocator.prototype.type="draw2d.ManhattanMidpointLocator";
draw2d.ManhattanMidpointLocator.prototype.relocate=function(_6041){
var conn=this.getConnection();
var p=new draw2d.Point();
var _6044=conn.getPoints();
var index=Math.floor((_6044.getSize()-2)/2);
if(_6044.getSize()<=index+1){
return;
}
var p1=_6044.get(index);
var p2=_6044.get(index+1);
p.x=(p2.x-p1.x)/2+p1.x+5;
p.y=(p2.y-p1.y)/2+p1.y+5;
_6041.setPosition(p.x,p.y);
};
draw2d.EditPartFactory=function(){
};
draw2d.EditPartFactory.prototype.type="draw2d.EditPartFactory";
draw2d.EditPartFactory.prototype.createEditPart=function(model){
};
draw2d.AbstractObjectModel=function(){
this.listeners=new draw2d.ArrayList();
this.id=draw2d.UUID.create();
};
draw2d.AbstractObjectModel.EVENT_ELEMENT_ADDED="element added";
draw2d.AbstractObjectModel.EVENT_ELEMENT_REMOVED="element removed";
draw2d.AbstractObjectModel.EVENT_CONNECTION_ADDED="connection addedx";
draw2d.AbstractObjectModel.EVENT_CONNECTION_REMOVED="connection removed";
draw2d.AbstractObjectModel.prototype.type="draw2d.AbstractObjectModel";
draw2d.AbstractObjectModel.prototype.getModelChildren=function(){
return new draw2d.ArrayList();
};
draw2d.AbstractObjectModel.prototype.getModelParent=function(){
return this.modelParent;
};
draw2d.AbstractObjectModel.prototype.setModelParent=function(_5562){
this.modelParent=_5562;
};
draw2d.AbstractObjectModel.prototype.getId=function(){
return this.id;
};
draw2d.AbstractObjectModel.prototype.firePropertyChange=function(_5563,_5564,_5565){
var count=this.listeners.getSize();
if(count===0){
return;
}
var event=new draw2d.PropertyChangeEvent(this,_5563,_5564,_5565);
for(var i=0;i<count;i++){
try{
this.listeners.get(i).propertyChange(event);
}
catch(e){
alert("Method: draw2d.AbstractObjectModel.prototype.firePropertyChange\n"+e+"\nProperty: "+_5563+"\nListener Class:"+this.listeners.get(i).type);
}
}
};
draw2d.AbstractObjectModel.prototype.addPropertyChangeListener=function(_5569){
if(_5569!==null){
this.listeners.add(_5569);
}
};
draw2d.AbstractObjectModel.prototype.removePropertyChangeListener=function(_556a){
if(_556a!==null){
this.listeners.remove(_556a);
}
};
draw2d.AbstractObjectModel.prototype.getPersistentAttributes=function(){
return {id:this.id};
};
draw2d.AbstractConnectionModel=function(){
draw2d.AbstractObjectModel.call(this);
};
draw2d.AbstractConnectionModel.prototype=new draw2d.AbstractObjectModel();
draw2d.AbstractConnectionModel.prototype.type="draw2d.AbstractConnectionModel";
draw2d.AbstractConnectionModel.prototype.getSourceModel=function(){
throw "you must override the method [AbstractConnectionModel.prototype.getSourceModel]";
};
draw2d.AbstractConnectionModel.prototype.getTargetModel=function(){
throw "you must override the method [AbstractConnectionModel.prototype.getTargetModel]";
};
draw2d.AbstractConnectionModel.prototype.getSourcePortName=function(){
throw "you must override the method [AbstractConnectionModel.prototype.getSourcePortName]";
};
draw2d.AbstractConnectionModel.prototype.getTargetPortName=function(){
throw "you must override the method [AbstractConnectionModel.prototype.getTargetPortName]";
};
draw2d.AbstractConnectionModel.prototype.getSourcePortModel=function(){
throw "you must override the method [AbstractConnectionModel.prototype.getSourcePortModel]";
};
draw2d.AbstractConnectionModel.prototype.getTargetPortModel=function(){
throw "you must override the method [AbstractConnectionModel.prototype.getTargetPortModel]";
};
draw2d.PropertyChangeEvent=function(model,_591c,_591d,_591e){
this.model=model;
this.property=_591c;
this.oldValue=_591d;
this.newValue=_591e;
};
draw2d.PropertyChangeEvent.prototype.type="draw2d.PropertyChangeEvent";
draw2d.GraphicalViewer=function(id){
try{
draw2d.Workflow.call(this,id);
this.factory=null;
this.model=null;
this.initDone=false;
}
catch(e){
pushErrorStack(e,"draw2d.GraphicalViewer=function(/*:String*/ id)");
}
};
draw2d.GraphicalViewer.prototype=new draw2d.Workflow();
draw2d.GraphicalViewer.prototype.type="draw2d.GraphicalViewer";
draw2d.GraphicalViewer.prototype.setEditPartFactory=function(_56c6){
this.factory=_56c6;
this.checkInit();
};
draw2d.GraphicalViewer.prototype.setModel=function(model){
try{
if(model instanceof draw2d.AbstractObjectModel){
this.model=model;
this.checkInit();
this.model.addPropertyChangeListener(this);
}else{
alert("Invalid model class type:"+model.type);
}
}
catch(e){
pushErrorStack(e,"draw2d.GraphicalViewer.prototype.setModel=function(/*:draw2d.AbstractObjectModel*/ model )");
}
};
draw2d.GraphicalViewer.prototype.propertyChange=function(event){
switch(event.property){
case draw2d.AbstractObjectModel.EVENT_ELEMENT_REMOVED:
var _56c9=this.getFigure(event.oldValue.getId());
this.removeFigure(_56c9);
break;
case draw2d.AbstractObjectModel.EVENT_ELEMENT_ADDED:
var _56c9=this.factory.createEditPart(event.newValue);
_56c9.setId(event.newValue.getId());
this.addFigure(_56c9);
this.setCurrentSelection(_56c9);
break;
}
};
draw2d.GraphicalViewer.prototype.checkInit=function(){
if(this.factory!==null&&this.model!==null&&this.initDone==false){
try{
var _56ca=this.model.getModelChildren();
var count=_56ca.getSize();
for(var i=0;i<count;i++){
var child=_56ca.get(i);
var _56ce=this.factory.createEditPart(child);
_56ce.setId(child.getId());
this.addFigure(_56ce);
}
}
catch(e){
pushErrorStack(e,"draw2d.GraphicalViewer.prototype.checkInit=function()[addFigures]");
}
try{
var _56cf=this.getDocument().getFigures();
var count=_56cf.getSize();
for(var i=0;i<count;i++){
var _56ce=_56cf.get(i);
if(_56ce instanceof draw2d.Node){
this.refreshConnections(_56ce);
}
}
}
catch(e){
pushErrorStack(e,"draw2d.GraphicalViewer.prototype.checkInit=function()[refreshConnections]");
}
}
};
draw2d.GraphicalViewer.prototype.refreshConnections=function(node){
try{
var _56d1=new draw2d.ArrayList();
var _56d2=node.getModelSourceConnections();
var count=_56d2.getSize();
for(var i=0;i<count;i++){
var _56d5=_56d2.get(i);
_56d1.add(_56d5.getId());
var _56d6=this.getLine(_56d5.getId());
if(_56d6===null){
_56d6=this.factory.createEditPart(_56d5);
var _56d7=_56d5.getSourceModel();
var _56d8=_56d5.getTargetModel();
var _56d9=this.getFigure(_56d7.getId());
var _56da=this.getFigure(_56d8.getId());
var _56db=_56d9.getOutputPort(_56d5.getSourcePortName());
var _56dc=_56da.getInputPort(_56d5.getTargetPortName());
_56d6.setTarget(_56dc);
_56d6.setSource(_56db);
_56d6.setId(_56d5.getId());
this.addFigure(_56d6);
this.setCurrentSelection(_56d6);
}
}
var ports=node.getOutputPorts();
count=ports.getSize();
for(var i=0;i<count;i++){
var _56de=ports.get(i).getConnections();
var _56df=_56de.getSize();
for(var ii=0;ii<_56df;ii++){
var _56e1=_56de.get(ii);
if(!_56d1.contains(_56e1.getId())){
this.removeFigure(_56e1);
_56d1.add(_56e1.getId());
}
}
}
}
catch(e){
pushErrorStack(e,"draw2d.GraphicalViewer.prototype.refreshConnections=function(/*:draw2d.Node*/ node )");
}
};
draw2d.GraphicalEditor=function(id){
try{
this.view=new draw2d.GraphicalViewer(id);
this.initializeGraphicalViewer();
}
catch(e){
pushErrorStack(e,"draw2d.GraphicalEditor=function(/*:String*/ id)");
}
};
draw2d.GraphicalEditor.prototype.type="draw2d.GraphicalEditor";
draw2d.GraphicalEditor.prototype.initializeGraphicalViewer=function(){
};
draw2d.GraphicalEditor.prototype.getGraphicalViewer=function(){
return this.view;
};
var whitespace="\n\r\t ";
XMLP=function(_5960){
_5960=SAXStrings.replace(_5960,null,null,"\r\n","\n");
_5960=SAXStrings.replace(_5960,null,null,"\r","\n");
this.m_xml=_5960;
this.m_iP=0;
this.m_iState=XMLP._STATE_PROLOG;
this.m_stack=new Stack();
this._clearAttributes();
};
XMLP._NONE=0;
XMLP._ELM_B=1;
XMLP._ELM_E=2;
XMLP._ELM_EMP=3;
XMLP._ATT=4;
XMLP._TEXT=5;
XMLP._ENTITY=6;
XMLP._PI=7;
XMLP._CDATA=8;
XMLP._COMMENT=9;
XMLP._DTD=10;
XMLP._ERROR=11;
XMLP._CONT_XML=0;
XMLP._CONT_ALT=1;
XMLP._ATT_NAME=0;
XMLP._ATT_VAL=1;
XMLP._STATE_PROLOG=1;
XMLP._STATE_DOCUMENT=2;
XMLP._STATE_MISC=3;
XMLP._errs=[];
XMLP._errs[XMLP.ERR_CLOSE_PI=0]="PI: missing closing sequence";
XMLP._errs[XMLP.ERR_CLOSE_DTD=1]="DTD: missing closing sequence";
XMLP._errs[XMLP.ERR_CLOSE_COMMENT=2]="Comment: missing closing sequence";
XMLP._errs[XMLP.ERR_CLOSE_CDATA=3]="CDATA: missing closing sequence";
XMLP._errs[XMLP.ERR_CLOSE_ELM=4]="Element: missing closing sequence";
XMLP._errs[XMLP.ERR_CLOSE_ENTITY=5]="Entity: missing closing sequence";
XMLP._errs[XMLP.ERR_PI_TARGET=6]="PI: target is required";
XMLP._errs[XMLP.ERR_ELM_EMPTY=7]="Element: cannot be both empty and closing";
XMLP._errs[XMLP.ERR_ELM_NAME=8]="Element: name must immediatly follow \"<\"";
XMLP._errs[XMLP.ERR_ELM_LT_NAME=9]="Element: \"<\" not allowed in element names";
XMLP._errs[XMLP.ERR_ATT_VALUES=10]="Attribute: values are required and must be in quotes";
XMLP._errs[XMLP.ERR_ATT_LT_NAME=11]="Element: \"<\" not allowed in attribute names";
XMLP._errs[XMLP.ERR_ATT_LT_VALUE=12]="Attribute: \"<\" not allowed in attribute values";
XMLP._errs[XMLP.ERR_ATT_DUP=13]="Attribute: duplicate attributes not allowed";
XMLP._errs[XMLP.ERR_ENTITY_UNKNOWN=14]="Entity: unknown entity";
XMLP._errs[XMLP.ERR_INFINITELOOP=15]="Infininte loop";
XMLP._errs[XMLP.ERR_DOC_STRUCTURE=16]="Document: only comments, processing instructions, or whitespace allowed outside of document element";
XMLP._errs[XMLP.ERR_ELM_NESTING=17]="Element: must be nested correctly";
XMLP.prototype._addAttribute=function(name,value){
this.m_atts[this.m_atts.length]=new Array(name,value);
};
XMLP.prototype._checkStructure=function(_5963){
if(XMLP._STATE_PROLOG==this.m_iState){
if((XMLP._TEXT==_5963)||(XMLP._ENTITY==_5963)){
if(SAXStrings.indexOfNonWhitespace(this.getContent(),this.getContentBegin(),this.getContentEnd())!=-1){
return this._setErr(XMLP.ERR_DOC_STRUCTURE);
}
}
if((XMLP._ELM_B==_5963)||(XMLP._ELM_EMP==_5963)){
this.m_iState=XMLP._STATE_DOCUMENT;
}
}
if(XMLP._STATE_DOCUMENT==this.m_iState){
if((XMLP._ELM_B==_5963)||(XMLP._ELM_EMP==_5963)){
this.m_stack.push(this.getName());
}
if((XMLP._ELM_E==_5963)||(XMLP._ELM_EMP==_5963)){
var _5964=this.m_stack.pop();
if((_5964===null)||(_5964!=this.getName())){
return this._setErr(XMLP.ERR_ELM_NESTING);
}
}
if(this.m_stack.count()===0){
this.m_iState=XMLP._STATE_MISC;
return _5963;
}
}
if(XMLP._STATE_MISC==this.m_iState){
if((XMLP._ELM_B==_5963)||(XMLP._ELM_E==_5963)||(XMLP._ELM_EMP==_5963)||(XMLP.EVT_DTD==_5963)){
return this._setErr(XMLP.ERR_DOC_STRUCTURE);
}
if((XMLP._TEXT==_5963)||(XMLP._ENTITY==_5963)){
if(SAXStrings.indexOfNonWhitespace(this.getContent(),this.getContentBegin(),this.getContentEnd())!=-1){
return this._setErr(XMLP.ERR_DOC_STRUCTURE);
}
}
}
return _5963;
};
XMLP.prototype._clearAttributes=function(){
this.m_atts=[];
};
XMLP.prototype._findAttributeIndex=function(name){
for(var i=0;i<this.m_atts.length;i++){
if(this.m_atts[i][XMLP._ATT_NAME]==name){
return i;
}
}
return -1;
};
XMLP.prototype.getAttributeCount=function(){
return this.m_atts?this.m_atts.length:0;
};
XMLP.prototype.getAttributeName=function(index){
return ((index<0)||(index>=this.m_atts.length))?null:this.m_atts[index][XMLP._ATT_NAME];
};
XMLP.prototype.getAttributeValue=function(index){
return ((index<0)||(index>=this.m_atts.length))?null:__unescapeString(this.m_atts[index][XMLP._ATT_VAL]);
};
XMLP.prototype.getAttributeValueByName=function(name){
return this.getAttributeValue(this._findAttributeIndex(name));
};
XMLP.prototype.getColumnNumber=function(){
return SAXStrings.getColumnNumber(this.m_xml,this.m_iP);
};
XMLP.prototype.getContent=function(){
return (this.m_cSrc==XMLP._CONT_XML)?this.m_xml:this.m_cAlt;
};
XMLP.prototype.getContentBegin=function(){
return this.m_cB;
};
XMLP.prototype.getContentEnd=function(){
return this.m_cE;
};
XMLP.prototype.getLineNumber=function(){
return SAXStrings.getLineNumber(this.m_xml,this.m_iP);
};
XMLP.prototype.getName=function(){
return this.m_name;
};
XMLP.prototype.next=function(){
return this._checkStructure(this._parse());
};
XMLP.prototype._parse=function(){
if(this.m_iP==this.m_xml.length){
return XMLP._NONE;
}
if(this.m_iP==this.m_xml.indexOf("<?",this.m_iP)){
return this._parsePI(this.m_iP+2);
}else{
if(this.m_iP==this.m_xml.indexOf("<!DOCTYPE",this.m_iP)){
return this._parseDTD(this.m_iP+9);
}else{
if(this.m_iP==this.m_xml.indexOf("<!--",this.m_iP)){
return this._parseComment(this.m_iP+4);
}else{
if(this.m_iP==this.m_xml.indexOf("<![CDATA[",this.m_iP)){
return this._parseCDATA(this.m_iP+9);
}else{
if(this.m_iP==this.m_xml.indexOf("<",this.m_iP)){
return this._parseElement(this.m_iP+1);
}else{
if(this.m_iP==this.m_xml.indexOf("&",this.m_iP)){
return this._parseEntity(this.m_iP+1);
}else{
return this._parseText(this.m_iP);
}
}
}
}
}
}
};
XMLP.prototype._parseAttribute=function(iB,iE){
var iNB,iNE,iEq,iVB,iVE;
var _596d,strN,strV;
this.m_cAlt="";
iNB=SAXStrings.indexOfNonWhitespace(this.m_xml,iB,iE);
if((iNB==-1)||(iNB>=iE)){
return iNB;
}
iEq=this.m_xml.indexOf("=",iNB);
if((iEq==-1)||(iEq>iE)){
return this._setErr(XMLP.ERR_ATT_VALUES);
}
iNE=SAXStrings.lastIndexOfNonWhitespace(this.m_xml,iNB,iEq);
iVB=SAXStrings.indexOfNonWhitespace(this.m_xml,iEq+1,iE);
if((iVB==-1)||(iVB>iE)){
return this._setErr(XMLP.ERR_ATT_VALUES);
}
_596d=this.m_xml.charAt(iVB);
if(SAXStrings.QUOTES.indexOf(_596d)==-1){
return this._setErr(XMLP.ERR_ATT_VALUES);
}
iVE=this.m_xml.indexOf(_596d,iVB+1);
if((iVE==-1)||(iVE>iE)){
return this._setErr(XMLP.ERR_ATT_VALUES);
}
strN=this.m_xml.substring(iNB,iNE+1);
strV=this.m_xml.substring(iVB+1,iVE);
if(strN.indexOf("<")!=-1){
return this._setErr(XMLP.ERR_ATT_LT_NAME);
}
if(strV.indexOf("<")!=-1){
return this._setErr(XMLP.ERR_ATT_LT_VALUE);
}
strV=SAXStrings.replace(strV,null,null,"\n"," ");
strV=SAXStrings.replace(strV,null,null,"\t"," ");
iRet=this._replaceEntities(strV);
if(iRet==XMLP._ERROR){
return iRet;
}
strV=this.m_cAlt;
if(this._findAttributeIndex(strN)==-1){
this._addAttribute(strN,strV);
}else{
return this._setErr(XMLP.ERR_ATT_DUP);
}
this.m_iP=iVE+2;
return XMLP._ATT;
};
XMLP.prototype._parseCDATA=function(iB){
var iE=this.m_xml.indexOf("]]>",iB);
if(iE==-1){
return this._setErr(XMLP.ERR_CLOSE_CDATA);
}
this._setContent(XMLP._CONT_XML,iB,iE);
this.m_iP=iE+3;
return XMLP._CDATA;
};
XMLP.prototype._parseComment=function(iB){
var iE=this.m_xml.indexOf("-"+"->",iB);
if(iE==-1){
return this._setErr(XMLP.ERR_CLOSE_COMMENT);
}
this._setContent(XMLP._CONT_XML,iB,iE);
this.m_iP=iE+3;
return XMLP._COMMENT;
};
XMLP.prototype._parseDTD=function(iB){
var iE,strClose,iInt,iLast;
iE=this.m_xml.indexOf(">",iB);
if(iE==-1){
return this._setErr(XMLP.ERR_CLOSE_DTD);
}
iInt=this.m_xml.indexOf("[",iB);
strClose=((iInt!=-1)&&(iInt<iE))?"]>":">";
while(true){
if(iE==iLast){
return this._setErr(XMLP.ERR_INFINITELOOP);
}
iLast=iE;
iE=this.m_xml.indexOf(strClose,iB);
if(iE==-1){
return this._setErr(XMLP.ERR_CLOSE_DTD);
}
if(this.m_xml.substring(iE-1,iE+2)!="]]>"){
break;
}
}
this.m_iP=iE+strClose.length;
return XMLP._DTD;
};
XMLP.prototype._parseElement=function(iB){
var iE,iDE,iNE,iRet;
var iType,strN,iLast;
iDE=iE=this.m_xml.indexOf(">",iB);
if(iE==-1){
return this._setErr(XMLP.ERR_CLOSE_ELM);
}
if(this.m_xml.charAt(iB)=="/"){
iType=XMLP._ELM_E;
iB++;
}else{
iType=XMLP._ELM_B;
}
if(this.m_xml.charAt(iE-1)=="/"){
if(iType==XMLP._ELM_E){
return this._setErr(XMLP.ERR_ELM_EMPTY);
}
iType=XMLP._ELM_EMP;
iDE--;
}
iDE=SAXStrings.lastIndexOfNonWhitespace(this.m_xml,iB,iDE);
if(iE-iB!=1){
if(SAXStrings.indexOfNonWhitespace(this.m_xml,iB,iDE)!=iB){
return this._setErr(XMLP.ERR_ELM_NAME);
}
}
this._clearAttributes();
iNE=SAXStrings.indexOfWhitespace(this.m_xml,iB,iDE);
if(iNE==-1){
iNE=iDE+1;
}else{
this.m_iP=iNE;
while(this.m_iP<iDE){
if(this.m_iP==iLast){
return this._setErr(XMLP.ERR_INFINITELOOP);
}
iLast=this.m_iP;
iRet=this._parseAttribute(this.m_iP,iDE);
if(iRet==XMLP._ERROR){
return iRet;
}
}
}
strN=this.m_xml.substring(iB,iNE);
if(strN.indexOf("<")!=-1){
return this._setErr(XMLP.ERR_ELM_LT_NAME);
}
this.m_name=strN;
this.m_iP=iE+1;
return iType;
};
XMLP.prototype._parseEntity=function(iB){
var iE=this.m_xml.indexOf(";",iB);
if(iE==-1){
return this._setErr(XMLP.ERR_CLOSE_ENTITY);
}
this.m_iP=iE+1;
return this._replaceEntity(this.m_xml,iB,iE);
};
XMLP.prototype._parsePI=function(iB){
var iE,iTB,iTE,iCB,iCE;
iE=this.m_xml.indexOf("?>",iB);
if(iE==-1){
return this._setErr(XMLP.ERR_CLOSE_PI);
}
iTB=SAXStrings.indexOfNonWhitespace(this.m_xml,iB,iE);
if(iTB==-1){
return this._setErr(XMLP.ERR_PI_TARGET);
}
iTE=SAXStrings.indexOfWhitespace(this.m_xml,iTB,iE);
if(iTE==-1){
iTE=iE;
}
iCB=SAXStrings.indexOfNonWhitespace(this.m_xml,iTE,iE);
if(iCB==-1){
iCB=iE;
}
iCE=SAXStrings.lastIndexOfNonWhitespace(this.m_xml,iCB,iE);
if(iCE==-1){
iCE=iE-1;
}
this.m_name=this.m_xml.substring(iTB,iTE);
this._setContent(XMLP._CONT_XML,iCB,iCE+1);
this.m_iP=iE+2;
return XMLP._PI;
};
XMLP.prototype._parseText=function(iB){
var iE,iEE;
iE=this.m_xml.indexOf("<",iB);
if(iE==-1){
iE=this.m_xml.length;
}
iEE=this.m_xml.indexOf("&",iB);
if((iEE!=-1)&&(iEE<=iE)){
iE=iEE;
}
this._setContent(XMLP._CONT_XML,iB,iE);
this.m_iP=iE;
return XMLP._TEXT;
};
XMLP.prototype._replaceEntities=function(strD,iB,iE){
if(SAXStrings.isEmpty(strD)){
return "";
}
iB=iB||0;
iE=iE||strD.length;
var iEB,iEE,strRet="";
iEB=strD.indexOf("&",iB);
iEE=iB;
while((iEB>0)&&(iEB<iE)){
strRet+=strD.substring(iEE,iEB);
iEE=strD.indexOf(";",iEB)+1;
if((iEE===0)||(iEE>iE)){
return this._setErr(XMLP.ERR_CLOSE_ENTITY);
}
iRet=this._replaceEntity(strD,iEB+1,iEE-1);
if(iRet==XMLP._ERROR){
return iRet;
}
strRet+=this.m_cAlt;
iEB=strD.indexOf("&",iEE);
}
if(iEE!=iE){
strRet+=strD.substring(iEE,iE);
}
this._setContent(XMLP._CONT_ALT,strRet);
return XMLP._ENTITY;
};
XMLP.prototype._replaceEntity=function(strD,iB,iE){
if(SAXStrings.isEmpty(strD)){
return -1;
}
iB=iB||0;
iE=iE||strD.length;
switch(strD.substring(iB,iE)){
case "amp":
strEnt="&";
break;
case "lt":
strEnt="<";
break;
case "gt":
strEnt=">";
break;
case "apos":
strEnt="'";
break;
case "quot":
strEnt="\"";
break;
default:
if(strD.charAt(iB)=="#"){
strEnt=String.fromCharCode(parseInt(strD.substring(iB+1,iE)));
}else{
return this._setErr(XMLP.ERR_ENTITY_UNKNOWN);
}
break;
}
this._setContent(XMLP._CONT_ALT,strEnt);
return XMLP._ENTITY;
};
XMLP.prototype._setContent=function(iSrc){
var args=arguments;
if(XMLP._CONT_XML==iSrc){
this.m_cAlt=null;
this.m_cB=args[1];
this.m_cE=args[2];
}else{
this.m_cAlt=args[1];
this.m_cB=0;
this.m_cE=args[1].length;
}
this.m_cSrc=iSrc;
};
XMLP.prototype._setErr=function(iErr){
var _5987=XMLP._errs[iErr];
this.m_cAlt=_5987;
this.m_cB=0;
this.m_cE=_5987.length;
this.m_cSrc=XMLP._CONT_ALT;
return XMLP._ERROR;
};
SAXDriver=function(){
this.m_hndDoc=null;
this.m_hndErr=null;
this.m_hndLex=null;
};
SAXDriver.DOC_B=1;
SAXDriver.DOC_E=2;
SAXDriver.ELM_B=3;
SAXDriver.ELM_E=4;
SAXDriver.CHARS=5;
SAXDriver.PI=6;
SAXDriver.CD_B=7;
SAXDriver.CD_E=8;
SAXDriver.CMNT=9;
SAXDriver.DTD_B=10;
SAXDriver.DTD_E=11;
SAXDriver.prototype.parse=function(strD){
var _5989=new XMLP(strD);
if(this.m_hndDoc&&this.m_hndDoc.setDocumentLocator){
this.m_hndDoc.setDocumentLocator(this);
}
this.m_parser=_5989;
this.m_bErr=false;
if(!this.m_bErr){
this._fireEvent(SAXDriver.DOC_B);
}
this._parseLoop();
if(!this.m_bErr){
this._fireEvent(SAXDriver.DOC_E);
}
this.m_xml=null;
this.m_iP=0;
};
SAXDriver.prototype.setDocumentHandler=function(hnd){
this.m_hndDoc=hnd;
};
SAXDriver.prototype.setErrorHandler=function(hnd){
this.m_hndErr=hnd;
};
SAXDriver.prototype.setLexicalHandler=function(hnd){
this.m_hndLex=hnd;
};
SAXDriver.prototype.getColumnNumber=function(){
return this.m_parser.getColumnNumber();
};
SAXDriver.prototype.getLineNumber=function(){
return this.m_parser.getLineNumber();
};
SAXDriver.prototype.getMessage=function(){
return this.m_strErrMsg;
};
SAXDriver.prototype.getPublicId=function(){
return null;
};
SAXDriver.prototype.getSystemId=function(){
return null;
};
SAXDriver.prototype.getLength=function(){
return this.m_parser.getAttributeCount();
};
SAXDriver.prototype.getName=function(index){
return this.m_parser.getAttributeName(index);
};
SAXDriver.prototype.getValue=function(index){
return this.m_parser.getAttributeValue(index);
};
SAXDriver.prototype.getValueByName=function(name){
return this.m_parser.getAttributeValueByName(name);
};
SAXDriver.prototype._fireError=function(_5990){
this.m_strErrMsg=_5990;
this.m_bErr=true;
if(this.m_hndErr&&this.m_hndErr.fatalError){
this.m_hndErr.fatalError(this);
}
};
SAXDriver.prototype._fireEvent=function(iEvt){
var hnd,func,args=arguments,iLen=args.length-1;
if(this.m_bErr){
return;
}
if(SAXDriver.DOC_B==iEvt){
func="startDocument";
hnd=this.m_hndDoc;
}else{
if(SAXDriver.DOC_E==iEvt){
func="endDocument";
hnd=this.m_hndDoc;
}else{
if(SAXDriver.ELM_B==iEvt){
func="startElement";
hnd=this.m_hndDoc;
}else{
if(SAXDriver.ELM_E==iEvt){
func="endElement";
hnd=this.m_hndDoc;
}else{
if(SAXDriver.CHARS==iEvt){
func="characters";
hnd=this.m_hndDoc;
}else{
if(SAXDriver.PI==iEvt){
func="processingInstruction";
hnd=this.m_hndDoc;
}else{
if(SAXDriver.CD_B==iEvt){
func="startCDATA";
hnd=this.m_hndLex;
}else{
if(SAXDriver.CD_E==iEvt){
func="endCDATA";
hnd=this.m_hndLex;
}else{
if(SAXDriver.CMNT==iEvt){
func="comment";
hnd=this.m_hndLex;
}
}
}
}
}
}
}
}
}
if(hnd&&hnd[func]){
if(0==iLen){
hnd[func]();
}else{
if(1==iLen){
hnd[func](args[1]);
}else{
if(2==iLen){
hnd[func](args[1],args[2]);
}else{
if(3==iLen){
hnd[func](args[1],args[2],args[3]);
}
}
}
}
}
};
SAXDriver.prototype._parseLoop=function(_5993){
var _5994,_5993;
_5993=this.m_parser;
while(!this.m_bErr){
_5994=_5993.next();
if(_5994==XMLP._ELM_B){
this._fireEvent(SAXDriver.ELM_B,_5993.getName(),this);
}else{
if(_5994==XMLP._ELM_E){
this._fireEvent(SAXDriver.ELM_E,_5993.getName());
}else{
if(_5994==XMLP._ELM_EMP){
this._fireEvent(SAXDriver.ELM_B,_5993.getName(),this);
this._fireEvent(SAXDriver.ELM_E,_5993.getName());
}else{
if(_5994==XMLP._TEXT){
this._fireEvent(SAXDriver.CHARS,_5993.getContent(),_5993.getContentBegin(),_5993.getContentEnd()-_5993.getContentBegin());
}else{
if(_5994==XMLP._ENTITY){
this._fireEvent(SAXDriver.CHARS,_5993.getContent(),_5993.getContentBegin(),_5993.getContentEnd()-_5993.getContentBegin());
}else{
if(_5994==XMLP._PI){
this._fireEvent(SAXDriver.PI,_5993.getName(),_5993.getContent().substring(_5993.getContentBegin(),_5993.getContentEnd()));
}else{
if(_5994==XMLP._CDATA){
this._fireEvent(SAXDriver.CD_B);
this._fireEvent(SAXDriver.CHARS,_5993.getContent(),_5993.getContentBegin(),_5993.getContentEnd()-_5993.getContentBegin());
this._fireEvent(SAXDriver.CD_E);
}else{
if(_5994==XMLP._COMMENT){
this._fireEvent(SAXDriver.CMNT,_5993.getContent(),_5993.getContentBegin(),_5993.getContentEnd()-_5993.getContentBegin());
}else{
if(_5994==XMLP._DTD){
}else{
if(_5994==XMLP._ERROR){
this._fireError(_5993.getContent());
}else{
if(_5994==XMLP._NONE){
return;
}
}
}
}
}
}
}
}
}
}
}
}
};
SAXStrings=function(){
};
SAXStrings.WHITESPACE=" \t\n\r";
SAXStrings.QUOTES="\"'";
SAXStrings.getColumnNumber=function(strD,iP){
if(SAXStrings.isEmpty(strD)){
return -1;
}
iP=iP||strD.length;
var arrD=strD.substring(0,iP).split("\n");
var _5998=arrD[arrD.length-1];
arrD.length--;
var _5999=arrD.join("\n").length;
return iP-_5999;
};
SAXStrings.getLineNumber=function(strD,iP){
if(SAXStrings.isEmpty(strD)){
return -1;
}
iP=iP||strD.length;
return strD.substring(0,iP).split("\n").length;
};
SAXStrings.indexOfNonWhitespace=function(strD,iB,iE){
if(SAXStrings.isEmpty(strD)){
return -1;
}
iB=iB||0;
iE=iE||strD.length;
for(var i=iB;i<iE;i++){
if(SAXStrings.WHITESPACE.indexOf(strD.charAt(i))==-1){
return i;
}
}
return -1;
};
SAXStrings.indexOfWhitespace=function(strD,iB,iE){
if(SAXStrings.isEmpty(strD)){
return -1;
}
iB=iB||0;
iE=iE||strD.length;
for(var i=iB;i<iE;i++){
if(SAXStrings.WHITESPACE.indexOf(strD.charAt(i))!=-1){
return i;
}
}
return -1;
};
SAXStrings.isEmpty=function(strD){
return (strD===null)||(strD.length===0);
};
SAXStrings.lastIndexOfNonWhitespace=function(strD,iB,iE){
if(SAXStrings.isEmpty(strD)){
return -1;
}
iB=iB||0;
iE=iE||strD.length;
for(var i=iE-1;i>=iB;i--){
if(SAXStrings.WHITESPACE.indexOf(strD.charAt(i))==-1){
return i;
}
}
return -1;
};
SAXStrings.replace=function(strD,iB,iE,strF,strR){
if(SAXStrings.isEmpty(strD)){
return "";
}
iB=iB||0;
iE=iE||strD.length;
return strD.substring(iB,iE).split(strF).join(strR);
};
Stack=function(){
this.m_arr=[];
};
Stack.prototype.clear=function(){
this.m_arr=[];
};
Stack.prototype.count=function(){
return this.m_arr.length;
};
Stack.prototype.destroy=function(){
this.m_arr=null;
};
Stack.prototype.peek=function(){
if(this.m_arr.length===0){
return null;
}
return this.m_arr[this.m_arr.length-1];
};
Stack.prototype.pop=function(){
if(this.m_arr.length===0){
return null;
}
var o=this.m_arr[this.m_arr.length-1];
this.m_arr.length--;
return o;
};
Stack.prototype.push=function(o){
this.m_arr[this.m_arr.length]=o;
};
function isEmpty(str){
return (str===null)||(str.length==0);
}
function trim(_59b1,_59b2,_59b3){
if(isEmpty(_59b1)){
return "";
}
if(_59b2===null){
_59b2=true;
}
if(_59b3===null){
_59b3=true;
}
var left=0;
var right=0;
var i=0;
var k=0;
if(_59b2==true){
while((i<_59b1.length)&&(whitespace.indexOf(_59b1.charAt(i++))!=-1)){
left++;
}
}
if(_59b3==true){
k=_59b1.length-1;
while((k>=left)&&(whitespace.indexOf(_59b1.charAt(k--))!=-1)){
right++;
}
}
return _59b1.substring(left,_59b1.length-right);
}
function __escapeString(str){
var _59b9=/&/g;
var _59ba=/</g;
var _59bb=/>/g;
var _59bc=/"/g;
var _59bd=/'/g;
str=str.replace(_59b9,"&amp;");
str=str.replace(_59ba,"&lt;");
str=str.replace(_59bb,"&gt;");
str=str.replace(_59bc,"&quot;");
str=str.replace(_59bd,"&apos;");
return str;
}
function __unescapeString(str){
var _59bf=/&amp;/g;
var _59c0=/&lt;/g;
var _59c1=/&gt;/g;
var _59c2=/&quot;/g;
var _59c3=/&apos;/g;
str=str.replace(_59bf,"&");
str=str.replace(_59c0,"<");
str=str.replace(_59c1,">");
str=str.replace(_59c2,"\"");
str=str.replace(_59c3,"'");
return str;
}
function addClass(_619e,_619f){
if(_619e){
if(_619e.indexOf("|"+_619f+"|")<0){
_619e+=_619f+"|";
}
}else{
_619e="|"+_619f+"|";
}
return _619e;
}
DOMException=function(code){
this._class=addClass(this._class,"DOMException");
this.code=code;
};
DOMException.INDEX_SIZE_ERR=1;
DOMException.DOMSTRING_SIZE_ERR=2;
DOMException.HIERARCHY_REQUEST_ERR=3;
DOMException.WRONG_DOCUMENT_ERR=4;
DOMException.INVALID_CHARACTER_ERR=5;
DOMException.NO_DATA_ALLOWED_ERR=6;
DOMException.NO_MODIFICATION_ALLOWED_ERR=7;
DOMException.NOT_FOUND_ERR=8;
DOMException.NOT_SUPPORTED_ERR=9;
DOMException.INUSE_ATTRIBUTE_ERR=10;
DOMException.INVALID_STATE_ERR=11;
DOMException.SYNTAX_ERR=12;
DOMException.INVALID_MODIFICATION_ERR=13;
DOMException.NAMESPACE_ERR=14;
DOMException.INVALID_ACCESS_ERR=15;
DOMImplementation=function(){
this._class=addClass(this._class,"DOMImplementation");
this._p=null;
this.preserveWhiteSpace=false;
this.namespaceAware=true;
this.errorChecking=true;
};
DOMImplementation.prototype.escapeString=function DOMNode__escapeString(str){
return __escapeString(str);
};
DOMImplementation.prototype.unescapeString=function DOMNode__unescapeString(str){
return __unescapeString(str);
};
DOMImplementation.prototype.hasFeature=function DOMImplementation_hasFeature(_61a3,_61a4){
var ret=false;
if(_61a3.toLowerCase()=="xml"){
ret=(!_61a4||(_61a4=="1.0")||(_61a4=="2.0"));
}else{
if(_61a3.toLowerCase()=="core"){
ret=(!_61a4||(_61a4=="2.0"));
}
}
return ret;
};
DOMImplementation.prototype.loadXML=function DOMImplementation_loadXML(_61a6){
var _61a7;
try{
_61a7=new XMLP(_61a6);
}
catch(e){
alert("Error Creating the SAX Parser. Did you include xmlsax.js or tinyxmlsax.js in your web page?\nThe SAX parser is needed to populate XML for <SCRIPT>'s W3C DOM Parser with data.");
}
var doc=new DOMDocument(this);
this._parseLoop(doc,_61a7);
doc._parseComplete=true;
return doc;
};
DOMImplementation.prototype.translateErrCode=function DOMImplementation_translateErrCode(code){
var msg="";
switch(code){
case DOMException.INDEX_SIZE_ERR:
msg="INDEX_SIZE_ERR: Index out of bounds";
break;
case DOMException.DOMSTRING_SIZE_ERR:
msg="DOMSTRING_SIZE_ERR: The resulting string is too long to fit in a DOMString";
break;
case DOMException.HIERARCHY_REQUEST_ERR:
msg="HIERARCHY_REQUEST_ERR: The Node can not be inserted at this location";
break;
case DOMException.WRONG_DOCUMENT_ERR:
msg="WRONG_DOCUMENT_ERR: The source and the destination Documents are not the same";
break;
case DOMException.INVALID_CHARACTER_ERR:
msg="INVALID_CHARACTER_ERR: The string contains an invalid character";
break;
case DOMException.NO_DATA_ALLOWED_ERR:
msg="NO_DATA_ALLOWED_ERR: This Node / NodeList does not support data";
break;
case DOMException.NO_MODIFICATION_ALLOWED_ERR:
msg="NO_MODIFICATION_ALLOWED_ERR: This object cannot be modified";
break;
case DOMException.NOT_FOUND_ERR:
msg="NOT_FOUND_ERR: The item cannot be found";
break;
case DOMException.NOT_SUPPORTED_ERR:
msg="NOT_SUPPORTED_ERR: This implementation does not support function";
break;
case DOMException.INUSE_ATTRIBUTE_ERR:
msg="INUSE_ATTRIBUTE_ERR: The Attribute has already been assigned to another Element";
break;
case DOMException.INVALID_STATE_ERR:
msg="INVALID_STATE_ERR: The object is no longer usable";
break;
case DOMException.SYNTAX_ERR:
msg="SYNTAX_ERR: Syntax error";
break;
case DOMException.INVALID_MODIFICATION_ERR:
msg="INVALID_MODIFICATION_ERR: Cannot change the type of the object";
break;
case DOMException.NAMESPACE_ERR:
msg="NAMESPACE_ERR: The namespace declaration is incorrect";
break;
case DOMException.INVALID_ACCESS_ERR:
msg="INVALID_ACCESS_ERR: The object does not support this function";
break;
default:
msg="UNKNOWN: Unknown Exception Code ("+code+")";
}
return msg;
};
DOMImplementation.prototype._parseLoop=function DOMImplementation__parseLoop(doc,p){
var iEvt,iNode,iAttr,strName;
iNodeParent=doc;
var _61ae=0;
var _61af=[];
var _61b0=[];
if(this.namespaceAware){
var iNS=doc.createNamespace("");
iNS.setValue("http://www.w3.org/2000/xmlns/");
doc._namespaces.setNamedItem(iNS);
}
while(true){
iEvt=p.next();
if(iEvt==XMLP._ELM_B){
var pName=p.getName();
pName=trim(pName,true,true);
if(!this.namespaceAware){
iNode=doc.createElement(p.getName());
for(var i=0;i<p.getAttributeCount();i++){
strName=p.getAttributeName(i);
iAttr=iNode.getAttributeNode(strName);
if(!iAttr){
iAttr=doc.createAttribute(strName);
}
iAttr.setValue(p.getAttributeValue(i));
iNode.setAttributeNode(iAttr);
}
}else{
iNode=doc.createElementNS("",p.getName());
iNode._namespaces=iNodeParent._namespaces._cloneNodes(iNode);
for(var i=0;i<p.getAttributeCount();i++){
strName=p.getAttributeName(i);
if(this._isNamespaceDeclaration(strName)){
var _61b4=this._parseNSName(strName);
if(strName!="xmlns"){
iNS=doc.createNamespace(strName);
}else{
iNS=doc.createNamespace("");
}
iNS.setValue(p.getAttributeValue(i));
iNode._namespaces.setNamedItem(iNS);
}else{
iAttr=iNode.getAttributeNode(strName);
if(!iAttr){
iAttr=doc.createAttributeNS("",strName);
}
iAttr.setValue(p.getAttributeValue(i));
iNode.setAttributeNodeNS(iAttr);
if(this._isIdDeclaration(strName)){
iNode.id=p.getAttributeValue(i);
}
}
}
if(iNode._namespaces.getNamedItem(iNode.prefix)){
iNode.namespaceURI=iNode._namespaces.getNamedItem(iNode.prefix).value;
}
for(var i=0;i<iNode.attributes.length;i++){
if(iNode.attributes.item(i).prefix!=""){
if(iNode._namespaces.getNamedItem(iNode.attributes.item(i).prefix)){
iNode.attributes.item(i).namespaceURI=iNode._namespaces.getNamedItem(iNode.attributes.item(i).prefix).value;
}
}
}
}
if(iNodeParent.nodeType==DOMNode.DOCUMENT_NODE){
iNodeParent.documentElement=iNode;
}
iNodeParent.appendChild(iNode);
iNodeParent=iNode;
}else{
if(iEvt==XMLP._ELM_E){
iNodeParent=iNodeParent.parentNode;
}else{
if(iEvt==XMLP._ELM_EMP){
pName=p.getName();
pName=trim(pName,true,true);
if(!this.namespaceAware){
iNode=doc.createElement(pName);
for(var i=0;i<p.getAttributeCount();i++){
strName=p.getAttributeName(i);
iAttr=iNode.getAttributeNode(strName);
if(!iAttr){
iAttr=doc.createAttribute(strName);
}
iAttr.setValue(p.getAttributeValue(i));
iNode.setAttributeNode(iAttr);
}
}else{
iNode=doc.createElementNS("",p.getName());
iNode._namespaces=iNodeParent._namespaces._cloneNodes(iNode);
for(var i=0;i<p.getAttributeCount();i++){
strName=p.getAttributeName(i);
if(this._isNamespaceDeclaration(strName)){
var _61b4=this._parseNSName(strName);
if(strName!="xmlns"){
iNS=doc.createNamespace(strName);
}else{
iNS=doc.createNamespace("");
}
iNS.setValue(p.getAttributeValue(i));
iNode._namespaces.setNamedItem(iNS);
}else{
iAttr=iNode.getAttributeNode(strName);
if(!iAttr){
iAttr=doc.createAttributeNS("",strName);
}
iAttr.setValue(p.getAttributeValue(i));
iNode.setAttributeNodeNS(iAttr);
if(this._isIdDeclaration(strName)){
iNode.id=p.getAttributeValue(i);
}
}
}
if(iNode._namespaces.getNamedItem(iNode.prefix)){
iNode.namespaceURI=iNode._namespaces.getNamedItem(iNode.prefix).value;
}
for(var i=0;i<iNode.attributes.length;i++){
if(iNode.attributes.item(i).prefix!=""){
if(iNode._namespaces.getNamedItem(iNode.attributes.item(i).prefix)){
iNode.attributes.item(i).namespaceURI=iNode._namespaces.getNamedItem(iNode.attributes.item(i).prefix).value;
}
}
}
}
if(iNodeParent.nodeType==DOMNode.DOCUMENT_NODE){
iNodeParent.documentElement=iNode;
}
iNodeParent.appendChild(iNode);
}else{
if(iEvt==XMLP._TEXT||iEvt==XMLP._ENTITY){
var _61b5=p.getContent().substring(p.getContentBegin(),p.getContentEnd());
if(!this.preserveWhiteSpace){
if(trim(_61b5,true,true)==""){
_61b5="";
}
}
if(_61b5.length>0){
var _61b6=doc.createTextNode(_61b5);
iNodeParent.appendChild(_61b6);
if(iEvt==XMLP._ENTITY){
_61af[_61af.length]=_61b6;
}else{
_61b0[_61b0.length]=_61b6;
}
}
}else{
if(iEvt==XMLP._PI){
iNodeParent.appendChild(doc.createProcessingInstruction(p.getName(),p.getContent().substring(p.getContentBegin(),p.getContentEnd())));
}else{
if(iEvt==XMLP._CDATA){
_61b5=p.getContent().substring(p.getContentBegin(),p.getContentEnd());
if(!this.preserveWhiteSpace){
_61b5=trim(_61b5,true,true);
_61b5.replace(/ +/g," ");
}
if(_61b5.length>0){
iNodeParent.appendChild(doc.createCDATASection(_61b5));
}
}else{
if(iEvt==XMLP._COMMENT){
var _61b5=p.getContent().substring(p.getContentBegin(),p.getContentEnd());
if(!this.preserveWhiteSpace){
_61b5=trim(_61b5,true,true);
_61b5.replace(/ +/g," ");
}
if(_61b5.length>0){
iNodeParent.appendChild(doc.createComment(_61b5));
}
}else{
if(iEvt==XMLP._DTD){
}else{
if(iEvt==XMLP._ERROR){
throw (new DOMException(DOMException.SYNTAX_ERR));
}else{
if(iEvt==XMLP._NONE){
if(iNodeParent==doc){
break;
}else{
throw (new DOMException(DOMException.SYNTAX_ERR));
}
}
}
}
}
}
}
}
}
}
}
}
var _61b7=_61af.length;
for(intLoop=0;intLoop<_61b7;intLoop++){
var _61b8=_61af[intLoop];
var _61b9=_61b8.getParentNode();
if(_61b9){
_61b9.normalize();
if(!this.preserveWhiteSpace){
var _61ba=_61b9.getChildNodes();
var _61bb=_61ba.getLength();
for(intLoop2=0;intLoop2<_61bb;intLoop2++){
var child=_61ba.item(intLoop2);
if(child.getNodeType()==DOMNode.TEXT_NODE){
var _61bd=child.getData();
_61bd=trim(_61bd,true,true);
_61bd.replace(/ +/g," ");
child.setData(_61bd);
}
}
}
}
}
if(!this.preserveWhiteSpace){
var _61b7=_61b0.length;
for(intLoop=0;intLoop<_61b7;intLoop++){
var node=_61b0[intLoop];
if(node.getParentNode()!==null){
var _61bf=node.getData();
_61bf=trim(_61bf,true,true);
_61bf.replace(/ +/g," ");
node.setData(_61bf);
}
}
}
};
DOMImplementation.prototype._isNamespaceDeclaration=function DOMImplementation__isNamespaceDeclaration(_61c0){
return (_61c0.indexOf("xmlns")>-1);
};
DOMImplementation.prototype._isIdDeclaration=function DOMImplementation__isIdDeclaration(_61c1){
return (_61c1.toLowerCase()=="id");
};
DOMImplementation.prototype._isValidName=function DOMImplementation__isValidName(name){
return name.match(re_validName);
};
re_validName=/^[a-zA-Z_:][a-zA-Z0-9\.\-_:]*$/;
DOMImplementation.prototype._isValidString=function DOMImplementation__isValidString(name){
return (name.search(re_invalidStringChars)<0);
};
re_invalidStringChars=/\x01|\x02|\x03|\x04|\x05|\x06|\x07|\x08|\x0B|\x0C|\x0E|\x0F|\x10|\x11|\x12|\x13|\x14|\x15|\x16|\x17|\x18|\x19|\x1A|\x1B|\x1C|\x1D|\x1E|\x1F|\x7F/;
DOMImplementation.prototype._parseNSName=function DOMImplementation__parseNSName(_61c4){
var _61c5={};
_61c5.prefix=_61c4;
_61c5.namespaceName="";
delimPos=_61c4.indexOf(":");
if(delimPos>-1){
_61c5.prefix=_61c4.substring(0,delimPos);
_61c5.namespaceName=_61c4.substring(delimPos+1,_61c4.length);
}
return _61c5;
};
DOMImplementation.prototype._parseQName=function DOMImplementation__parseQName(_61c6){
var _61c7={};
_61c7.localName=_61c6;
_61c7.prefix="";
delimPos=_61c6.indexOf(":");
if(delimPos>-1){
_61c7.prefix=_61c6.substring(0,delimPos);
_61c7.localName=_61c6.substring(delimPos+1,_61c6.length);
}
return _61c7;
};
DOMNodeList=function(_61c8,_61c9){
this._class=addClass(this._class,"DOMNodeList");
this._nodes=[];
this.length=0;
this.parentNode=_61c9;
this.ownerDocument=_61c8;
this._readonly=false;
};
DOMNodeList.prototype.getLength=function DOMNodeList_getLength(){
return this.length;
};
DOMNodeList.prototype.item=function DOMNodeList_item(index){
var ret=null;
if((index>=0)&&(index<this._nodes.length)){
ret=this._nodes[index];
}
return ret;
};
DOMNodeList.prototype._findItemIndex=function DOMNodeList__findItemIndex(id){
var ret=-1;
if(id>-1){
for(var i=0;i<this._nodes.length;i++){
if(this._nodes[i]._id==id){
ret=i;
break;
}
}
}
return ret;
};
DOMNodeList.prototype._insertBefore=function DOMNodeList__insertBefore(_61cf,_61d0){
if((_61d0>=0)&&(_61d0<this._nodes.length)){
var _61d1=[];
_61d1=this._nodes.slice(0,_61d0);
if(_61cf.nodeType==DOMNode.DOCUMENT_FRAGMENT_NODE){
_61d1=_61d1.concat(_61cf.childNodes._nodes);
}else{
_61d1[_61d1.length]=_61cf;
}
this._nodes=_61d1.concat(this._nodes.slice(_61d0));
this.length=this._nodes.length;
}
};
DOMNodeList.prototype._replaceChild=function DOMNodeList__replaceChild(_61d2,_61d3){
var ret=null;
if((_61d3>=0)&&(_61d3<this._nodes.length)){
ret=this._nodes[_61d3];
if(_61d2.nodeType==DOMNode.DOCUMENT_FRAGMENT_NODE){
var _61d5=[];
_61d5=this._nodes.slice(0,_61d3);
_61d5=_61d5.concat(_61d2.childNodes._nodes);
this._nodes=_61d5.concat(this._nodes.slice(_61d3+1));
}else{
this._nodes[_61d3]=_61d2;
}
}
return ret;
};
DOMNodeList.prototype._removeChild=function DOMNodeList__removeChild(_61d6){
var ret=null;
if(_61d6>-1){
ret=this._nodes[_61d6];
var _61d8=[];
_61d8=this._nodes.slice(0,_61d6);
this._nodes=_61d8.concat(this._nodes.slice(_61d6+1));
this.length=this._nodes.length;
}
return ret;
};
DOMNodeList.prototype._appendChild=function DOMNodeList__appendChild(_61d9){
if(_61d9.nodeType==DOMNode.DOCUMENT_FRAGMENT_NODE){
this._nodes=this._nodes.concat(_61d9.childNodes._nodes);
}else{
this._nodes[this._nodes.length]=_61d9;
}
this.length=this._nodes.length;
};
DOMNodeList.prototype._cloneNodes=function DOMNodeList__cloneNodes(deep,_61db){
var _61dc=new DOMNodeList(this.ownerDocument,_61db);
for(var i=0;i<this._nodes.length;i++){
_61dc._appendChild(this._nodes[i].cloneNode(deep));
}
return _61dc;
};
DOMNodeList.prototype.toString=function DOMNodeList_toString(){
var ret="";
for(var i=0;i<this.length;i++){
ret+=this._nodes[i].toString();
}
return ret;
};
DOMNamedNodeMap=function(_61e0,_61e1){
this._class=addClass(this._class,"DOMNamedNodeMap");
this.DOMNodeList=DOMNodeList;
this.DOMNodeList(_61e0,_61e1);
};
DOMNamedNodeMap.prototype=new DOMNodeList;
DOMNamedNodeMap.prototype.getNamedItem=function DOMNamedNodeMap_getNamedItem(name){
var ret=null;
var _61e4=this._findNamedItemIndex(name);
if(_61e4>-1){
ret=this._nodes[_61e4];
}
return ret;
};
DOMNamedNodeMap.prototype.setNamedItem=function DOMNamedNodeMap_setNamedItem(arg){
if(this.ownerDocument.implementation.errorChecking){
if(this.ownerDocument!=arg.ownerDocument){
throw (new DOMException(DOMException.WRONG_DOCUMENT_ERR));
}
if(this._readonly||(this.parentNode&&this.parentNode._readonly)){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if(arg.ownerElement&&(arg.ownerElement!=this.parentNode)){
throw (new DOMException(DOMException.INUSE_ATTRIBUTE_ERR));
}
}
var _61e6=this._findNamedItemIndex(arg.name);
var ret=null;
if(_61e6>-1){
ret=this._nodes[_61e6];
if(this.ownerDocument.implementation.errorChecking&&ret._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}else{
this._nodes[_61e6]=arg;
}
}else{
this._nodes[this.length]=arg;
}
this.length=this._nodes.length;
arg.ownerElement=this.parentNode;
return ret;
};
DOMNamedNodeMap.prototype.removeNamedItem=function DOMNamedNodeMap_removeNamedItem(name){
var ret=null;
if(this.ownerDocument.implementation.errorChecking&&(this._readonly||(this.parentNode&&this.parentNode._readonly))){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
var _61ea=this._findNamedItemIndex(name);
if(this.ownerDocument.implementation.errorChecking&&(_61ea<0)){
throw (new DOMException(DOMException.NOT_FOUND_ERR));
}
var _61eb=this._nodes[_61ea];
if(this.ownerDocument.implementation.errorChecking&&_61eb._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
return this._removeChild(_61ea);
};
DOMNamedNodeMap.prototype.getNamedItemNS=function DOMNamedNodeMap_getNamedItemNS(_61ec,_61ed){
var ret=null;
var _61ef=this._findNamedItemNSIndex(_61ec,_61ed);
if(_61ef>-1){
ret=this._nodes[_61ef];
}
return ret;
};
DOMNamedNodeMap.prototype.setNamedItemNS=function DOMNamedNodeMap_setNamedItemNS(arg){
if(this.ownerDocument.implementation.errorChecking){
if(this._readonly||(this.parentNode&&this.parentNode._readonly)){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if(this.ownerDocument!=arg.ownerDocument){
throw (new DOMException(DOMException.WRONG_DOCUMENT_ERR));
}
if(arg.ownerElement&&(arg.ownerElement!=this.parentNode)){
throw (new DOMException(DOMException.INUSE_ATTRIBUTE_ERR));
}
}
var _61f1=this._findNamedItemNSIndex(arg.namespaceURI,arg.localName);
var ret=null;
if(_61f1>-1){
ret=this._nodes[_61f1];
if(this.ownerDocument.implementation.errorChecking&&ret._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}else{
this._nodes[_61f1]=arg;
}
}else{
this._nodes[this.length]=arg;
}
this.length=this._nodes.length;
arg.ownerElement=this.parentNode;
return ret;
};
DOMNamedNodeMap.prototype.removeNamedItemNS=function DOMNamedNodeMap_removeNamedItemNS(_61f3,_61f4){
var ret=null;
if(this.ownerDocument.implementation.errorChecking&&(this._readonly||(this.parentNode&&this.parentNode._readonly))){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
var _61f6=this._findNamedItemNSIndex(_61f3,_61f4);
if(this.ownerDocument.implementation.errorChecking&&(_61f6<0)){
throw (new DOMException(DOMException.NOT_FOUND_ERR));
}
var _61f7=this._nodes[_61f6];
if(this.ownerDocument.implementation.errorChecking&&_61f7._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
return this._removeChild(_61f6);
};
DOMNamedNodeMap.prototype._findNamedItemIndex=function DOMNamedNodeMap__findNamedItemIndex(name){
var ret=-1;
for(var i=0;i<this._nodes.length;i++){
if(this._nodes[i].name==name){
ret=i;
break;
}
}
return ret;
};
DOMNamedNodeMap.prototype._findNamedItemNSIndex=function DOMNamedNodeMap__findNamedItemNSIndex(_61fb,_61fc){
var ret=-1;
if(_61fc){
for(var i=0;i<this._nodes.length;i++){
if((this._nodes[i].namespaceURI==_61fb)&&(this._nodes[i].localName==_61fc)){
ret=i;
break;
}
}
}
return ret;
};
DOMNamedNodeMap.prototype._hasAttribute=function DOMNamedNodeMap__hasAttribute(name){
var ret=false;
var _6201=this._findNamedItemIndex(name);
if(_6201>-1){
ret=true;
}
return ret;
};
DOMNamedNodeMap.prototype._hasAttributeNS=function DOMNamedNodeMap__hasAttributeNS(_6202,_6203){
var ret=false;
var _6205=this._findNamedItemNSIndex(_6202,_6203);
if(_6205>-1){
ret=true;
}
return ret;
};
DOMNamedNodeMap.prototype._cloneNodes=function DOMNamedNodeMap__cloneNodes(_6206){
var _6207=new DOMNamedNodeMap(this.ownerDocument,_6206);
for(var i=0;i<this._nodes.length;i++){
_6207._appendChild(this._nodes[i].cloneNode(false));
}
return _6207;
};
DOMNamedNodeMap.prototype.toString=function DOMNamedNodeMap_toString(){
var ret="";
for(var i=0;i<this.length-1;i++){
ret+=this._nodes[i].toString()+" ";
}
if(this.length>0){
ret+=this._nodes[this.length-1].toString();
}
return ret;
};
DOMNamespaceNodeMap=function(_620b,_620c){
this._class=addClass(this._class,"DOMNamespaceNodeMap");
this.DOMNamedNodeMap=DOMNamedNodeMap;
this.DOMNamedNodeMap(_620b,_620c);
};
DOMNamespaceNodeMap.prototype=new DOMNamedNodeMap;
DOMNamespaceNodeMap.prototype._findNamedItemIndex=function DOMNamespaceNodeMap__findNamedItemIndex(_620d){
var ret=-1;
for(var i=0;i<this._nodes.length;i++){
if(this._nodes[i].localName==_620d){
ret=i;
break;
}
}
return ret;
};
DOMNamespaceNodeMap.prototype._cloneNodes=function DOMNamespaceNodeMap__cloneNodes(_6210){
var _6211=new DOMNamespaceNodeMap(this.ownerDocument,_6210);
for(var i=0;i<this._nodes.length;i++){
_6211._appendChild(this._nodes[i].cloneNode(false));
}
return _6211;
};
DOMNamespaceNodeMap.prototype.toString=function DOMNamespaceNodeMap_toString(){
var ret="";
for(var ind=0;ind<this._nodes.length;ind++){
var ns=null;
try{
var ns=this.parentNode.parentNode._namespaces.getNamedItem(this._nodes[ind].localName);
}
catch(e){
break;
}
if(!(ns&&(""+ns.nodeValue==""+this._nodes[ind].nodeValue))){
ret+=this._nodes[ind].toString()+" ";
}
}
return ret;
};
DOMNode=function(_6216){
this._class=addClass(this._class,"DOMNode");
if(_6216){
this._id=_6216._genId();
}
this.namespaceURI="";
this.prefix="";
this.localName="";
this.nodeName="";
this.nodeValue="";
this.nodeType=0;
this.parentNode=null;
this.childNodes=new DOMNodeList(_6216,this);
this.firstChild=null;
this.lastChild=null;
this.previousSibling=null;
this.nextSibling=null;
this.attributes=new DOMNamedNodeMap(_6216,this);
this.ownerDocument=_6216;
this._namespaces=new DOMNamespaceNodeMap(_6216,this);
this._readonly=false;
};
DOMNode.ELEMENT_NODE=1;
DOMNode.ATTRIBUTE_NODE=2;
DOMNode.TEXT_NODE=3;
DOMNode.CDATA_SECTION_NODE=4;
DOMNode.ENTITY_REFERENCE_NODE=5;
DOMNode.ENTITY_NODE=6;
DOMNode.PROCESSING_INSTRUCTION_NODE=7;
DOMNode.COMMENT_NODE=8;
DOMNode.DOCUMENT_NODE=9;
DOMNode.DOCUMENT_TYPE_NODE=10;
DOMNode.DOCUMENT_FRAGMENT_NODE=11;
DOMNode.NOTATION_NODE=12;
DOMNode.NAMESPACE_NODE=13;
DOMNode.prototype.hasAttributes=function DOMNode_hasAttributes(){
if(this.attributes.length===0){
return false;
}else{
return true;
}
};
DOMNode.prototype.getNodeName=function DOMNode_getNodeName(){
return this.nodeName;
};
DOMNode.prototype.getNodeValue=function DOMNode_getNodeValue(){
return this.nodeValue;
};
DOMNode.prototype.setNodeValue=function DOMNode_setNodeValue(_6217){
if(this.ownerDocument.implementation.errorChecking&&this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
this.nodeValue=_6217;
};
DOMNode.prototype.getNodeType=function DOMNode_getNodeType(){
return this.nodeType;
};
DOMNode.prototype.getParentNode=function DOMNode_getParentNode(){
return this.parentNode;
};
DOMNode.prototype.getChildNodes=function DOMNode_getChildNodes(){
return this.childNodes;
};
DOMNode.prototype.getFirstChild=function DOMNode_getFirstChild(){
return this.firstChild;
};
DOMNode.prototype.getLastChild=function DOMNode_getLastChild(){
return this.lastChild;
};
DOMNode.prototype.getPreviousSibling=function DOMNode_getPreviousSibling(){
return this.previousSibling;
};
DOMNode.prototype.getNextSibling=function DOMNode_getNextSibling(){
return this.nextSibling;
};
DOMNode.prototype.getAttributes=function DOMNode_getAttributes(){
return this.attributes;
};
DOMNode.prototype.getOwnerDocument=function DOMNode_getOwnerDocument(){
return this.ownerDocument;
};
DOMNode.prototype.getNamespaceURI=function DOMNode_getNamespaceURI(){
return this.namespaceURI;
};
DOMNode.prototype.getPrefix=function DOMNode_getPrefix(){
return this.prefix;
};
DOMNode.prototype.setPrefix=function DOMNode_setPrefix(_6218){
if(this.ownerDocument.implementation.errorChecking){
if(this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if(!this.ownerDocument.implementation._isValidName(_6218)){
throw (new DOMException(DOMException.INVALID_CHARACTER_ERR));
}
if(!this.ownerDocument._isValidNamespace(this.namespaceURI,_6218+":"+this.localName)){
throw (new DOMException(DOMException.NAMESPACE_ERR));
}
if((_6218=="xmlns")&&(this.namespaceURI!="http://www.w3.org/2000/xmlns/")){
throw (new DOMException(DOMException.NAMESPACE_ERR));
}
if((_6218=="")&&(this.localName=="xmlns")){
throw (new DOMException(DOMException.NAMESPACE_ERR));
}
}
this.prefix=_6218;
if(this.prefix!=""){
this.nodeName=this.prefix+":"+this.localName;
}else{
this.nodeName=this.localName;
}
};
DOMNode.prototype.getLocalName=function DOMNode_getLocalName(){
return this.localName;
};
DOMNode.prototype.insertBefore=function DOMNode_insertBefore(_6219,_621a){
var _621b;
if(this.ownerDocument.implementation.errorChecking){
if(this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if(this.ownerDocument!=_6219.ownerDocument){
throw (new DOMException(DOMException.WRONG_DOCUMENT_ERR));
}
if(this._isAncestor(_6219)){
throw (new DOMException(DOMException.HIERARCHY_REQUEST_ERR));
}
}
if(_621a){
var _621c=this.childNodes._findItemIndex(_621a._id);
if(this.ownerDocument.implementation.errorChecking&&(_621c<0)){
throw (new DOMException(DOMException.NOT_FOUND_ERR));
}
var _621d=_6219.parentNode;
if(_621d){
_621d.removeChild(_6219);
}
this.childNodes._insertBefore(_6219,this.childNodes._findItemIndex(_621a._id));
_621b=_621a.previousSibling;
if(_6219.nodeType==DOMNode.DOCUMENT_FRAGMENT_NODE){
if(_6219.childNodes._nodes.length>0){
for(var ind=0;ind<_6219.childNodes._nodes.length;ind++){
_6219.childNodes._nodes[ind].parentNode=this;
}
_621a.previousSibling=_6219.childNodes._nodes[_6219.childNodes._nodes.length-1];
}
}else{
_6219.parentNode=this;
_621a.previousSibling=_6219;
}
}else{
_621b=this.lastChild;
this.appendChild(_6219);
}
if(_6219.nodeType==DOMNode.DOCUMENT_FRAGMENT_NODE){
if(_6219.childNodes._nodes.length>0){
if(_621b){
_621b.nextSibling=_6219.childNodes._nodes[0];
}else{
this.firstChild=_6219.childNodes._nodes[0];
}
_6219.childNodes._nodes[0].previousSibling=_621b;
_6219.childNodes._nodes[_6219.childNodes._nodes.length-1].nextSibling=_621a;
}
}else{
if(_621b){
_621b.nextSibling=_6219;
}else{
this.firstChild=_6219;
}
_6219.previousSibling=_621b;
_6219.nextSibling=_621a;
}
return _6219;
};
DOMNode.prototype.replaceChild=function DOMNode_replaceChild(_621f,_6220){
var ret=null;
if(this.ownerDocument.implementation.errorChecking){
if(this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if(this.ownerDocument!=_621f.ownerDocument){
throw (new DOMException(DOMException.WRONG_DOCUMENT_ERR));
}
if(this._isAncestor(_621f)){
throw (new DOMException(DOMException.HIERARCHY_REQUEST_ERR));
}
}
var index=this.childNodes._findItemIndex(_6220._id);
if(this.ownerDocument.implementation.errorChecking&&(index<0)){
throw (new DOMException(DOMException.NOT_FOUND_ERR));
}
var _6223=_621f.parentNode;
if(_6223){
_6223.removeChild(_621f);
}
ret=this.childNodes._replaceChild(_621f,index);
if(_621f.nodeType==DOMNode.DOCUMENT_FRAGMENT_NODE){
if(_621f.childNodes._nodes.length>0){
for(var ind=0;ind<_621f.childNodes._nodes.length;ind++){
_621f.childNodes._nodes[ind].parentNode=this;
}
if(_6220.previousSibling){
_6220.previousSibling.nextSibling=_621f.childNodes._nodes[0];
}else{
this.firstChild=_621f.childNodes._nodes[0];
}
if(_6220.nextSibling){
_6220.nextSibling.previousSibling=_621f;
}else{
this.lastChild=_621f.childNodes._nodes[_621f.childNodes._nodes.length-1];
}
_621f.childNodes._nodes[0].previousSibling=_6220.previousSibling;
_621f.childNodes._nodes[_621f.childNodes._nodes.length-1].nextSibling=_6220.nextSibling;
}
}else{
_621f.parentNode=this;
if(_6220.previousSibling){
_6220.previousSibling.nextSibling=_621f;
}else{
this.firstChild=_621f;
}
if(_6220.nextSibling){
_6220.nextSibling.previousSibling=_621f;
}else{
this.lastChild=_621f;
}
_621f.previousSibling=_6220.previousSibling;
_621f.nextSibling=_6220.nextSibling;
}
return ret;
};
DOMNode.prototype.removeChild=function DOMNode_removeChild(_6225){
if(this.ownerDocument.implementation.errorChecking&&(this._readonly||_6225._readonly)){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
var _6226=this.childNodes._findItemIndex(_6225._id);
if(this.ownerDocument.implementation.errorChecking&&(_6226<0)){
throw (new DOMException(DOMException.NOT_FOUND_ERR));
}
this.childNodes._removeChild(_6226);
_6225.parentNode=null;
if(_6225.previousSibling){
_6225.previousSibling.nextSibling=_6225.nextSibling;
}else{
this.firstChild=_6225.nextSibling;
}
if(_6225.nextSibling){
_6225.nextSibling.previousSibling=_6225.previousSibling;
}else{
this.lastChild=_6225.previousSibling;
}
_6225.previousSibling=null;
_6225.nextSibling=null;
return _6225;
};
DOMNode.prototype.appendChild=function DOMNode_appendChild(_6227){
if(this.ownerDocument.implementation.errorChecking){
if(this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if(this.ownerDocument!=_6227.ownerDocument){
throw (new DOMException(DOMException.WRONG_DOCUMENT_ERR));
}
if(this._isAncestor(_6227)){
throw (new DOMException(DOMException.HIERARCHY_REQUEST_ERR));
}
}
var _6228=_6227.parentNode;
if(_6228){
_6228.removeChild(_6227);
}
this.childNodes._appendChild(_6227);
if(_6227.nodeType==DOMNode.DOCUMENT_FRAGMENT_NODE){
if(_6227.childNodes._nodes.length>0){
for(var ind=0;ind<_6227.childNodes._nodes.length;ind++){
_6227.childNodes._nodes[ind].parentNode=this;
}
if(this.lastChild){
this.lastChild.nextSibling=_6227.childNodes._nodes[0];
_6227.childNodes._nodes[0].previousSibling=this.lastChild;
this.lastChild=_6227.childNodes._nodes[_6227.childNodes._nodes.length-1];
}else{
this.lastChild=_6227.childNodes._nodes[_6227.childNodes._nodes.length-1];
this.firstChild=_6227.childNodes._nodes[0];
}
}
}else{
_6227.parentNode=this;
if(this.lastChild){
this.lastChild.nextSibling=_6227;
_6227.previousSibling=this.lastChild;
this.lastChild=_6227;
}else{
this.lastChild=_6227;
this.firstChild=_6227;
}
}
return _6227;
};
DOMNode.prototype.hasChildNodes=function DOMNode_hasChildNodes(){
return (this.childNodes.length>0);
};
DOMNode.prototype.cloneNode=function DOMNode_cloneNode(deep){
try{
return this.ownerDocument.importNode(this,deep);
}
catch(e){
return null;
}
};
DOMNode.prototype.normalize=function DOMNode_normalize(){
var inode;
var _622c=new DOMNodeList();
if(this.nodeType==DOMNode.ELEMENT_NODE||this.nodeType==DOMNode.DOCUMENT_NODE){
var _622d=null;
for(var i=0;i<this.childNodes.length;i++){
inode=this.childNodes.item(i);
if(inode.nodeType==DOMNode.TEXT_NODE){
if(inode.length<1){
_622c._appendChild(inode);
}else{
if(_622d){
_622d.appendData(inode.data);
_622c._appendChild(inode);
}else{
_622d=inode;
}
}
}else{
_622d=null;
inode.normalize();
}
}
for(var i=0;i<_622c.length;i++){
inode=_622c.item(i);
inode.parentNode.removeChild(inode);
}
}
};
DOMNode.prototype.isSupported=function DOMNode_isSupported(_622f,_6230){
return this.ownerDocument.implementation.hasFeature(_622f,_6230);
};
DOMNode.prototype.getElementsByTagName=function DOMNode_getElementsByTagName(_6231){
return this._getElementsByTagNameRecursive(_6231,new DOMNodeList(this.ownerDocument));
};
DOMNode.prototype._getElementsByTagNameRecursive=function DOMNode__getElementsByTagNameRecursive(_6232,_6233){
if(this.nodeType==DOMNode.ELEMENT_NODE||this.nodeType==DOMNode.DOCUMENT_NODE){
if((this.nodeName==_6232)||(_6232=="*")){
_6233._appendChild(this);
}
for(var i=0;i<this.childNodes.length;i++){
_6233=this.childNodes.item(i)._getElementsByTagNameRecursive(_6232,_6233);
}
}
return _6233;
};
DOMNode.prototype.getXML=function DOMNode_getXML(){
return this.toString();
};
DOMNode.prototype.getElementsByTagNameNS=function DOMNode_getElementsByTagNameNS(_6235,_6236){
return this._getElementsByTagNameNSRecursive(_6235,_6236,new DOMNodeList(this.ownerDocument));
};
DOMNode.prototype._getElementsByTagNameNSRecursive=function DOMNode__getElementsByTagNameNSRecursive(_6237,_6238,_6239){
if(this.nodeType==DOMNode.ELEMENT_NODE||this.nodeType==DOMNode.DOCUMENT_NODE){
if(((this.namespaceURI==_6237)||(_6237=="*"))&&((this.localName==_6238)||(_6238=="*"))){
_6239._appendChild(this);
}
for(var i=0;i<this.childNodes.length;i++){
_6239=this.childNodes.item(i)._getElementsByTagNameNSRecursive(_6237,_6238,_6239);
}
}
return _6239;
};
DOMNode.prototype._isAncestor=function DOMNode__isAncestor(node){
return ((this==node)||((this.parentNode)&&(this.parentNode._isAncestor(node))));
};
DOMNode.prototype.importNode=function DOMNode_importNode(_623c,deep){
var _623e;
this.getOwnerDocument()._performingImportNodeOperation=true;
try{
if(_623c.nodeType==DOMNode.ELEMENT_NODE){
if(!this.ownerDocument.implementation.namespaceAware){
_623e=this.ownerDocument.createElement(_623c.tagName);
for(var i=0;i<_623c.attributes.length;i++){
_623e.setAttribute(_623c.attributes.item(i).name,_623c.attributes.item(i).value);
}
}else{
_623e=this.ownerDocument.createElementNS(_623c.namespaceURI,_623c.nodeName);
for(var i=0;i<_623c.attributes.length;i++){
_623e.setAttributeNS(_623c.attributes.item(i).namespaceURI,_623c.attributes.item(i).name,_623c.attributes.item(i).value);
}
for(var i=0;i<_623c._namespaces.length;i++){
_623e._namespaces._nodes[i]=this.ownerDocument.createNamespace(_623c._namespaces.item(i).localName);
_623e._namespaces._nodes[i].setValue(_623c._namespaces.item(i).value);
}
}
}else{
if(_623c.nodeType==DOMNode.ATTRIBUTE_NODE){
if(!this.ownerDocument.implementation.namespaceAware){
_623e=this.ownerDocument.createAttribute(_623c.name);
}else{
_623e=this.ownerDocument.createAttributeNS(_623c.namespaceURI,_623c.nodeName);
for(var i=0;i<_623c._namespaces.length;i++){
_623e._namespaces._nodes[i]=this.ownerDocument.createNamespace(_623c._namespaces.item(i).localName);
_623e._namespaces._nodes[i].setValue(_623c._namespaces.item(i).value);
}
}
_623e.setValue(_623c.value);
}else{
if(_623c.nodeType==DOMNode.DOCUMENT_FRAGMENT){
_623e=this.ownerDocument.createDocumentFragment();
}else{
if(_623c.nodeType==DOMNode.NAMESPACE_NODE){
_623e=this.ownerDocument.createNamespace(_623c.nodeName);
_623e.setValue(_623c.value);
}else{
if(_623c.nodeType==DOMNode.TEXT_NODE){
_623e=this.ownerDocument.createTextNode(_623c.data);
}else{
if(_623c.nodeType==DOMNode.CDATA_SECTION_NODE){
_623e=this.ownerDocument.createCDATASection(_623c.data);
}else{
if(_623c.nodeType==DOMNode.PROCESSING_INSTRUCTION_NODE){
_623e=this.ownerDocument.createProcessingInstruction(_623c.target,_623c.data);
}else{
if(_623c.nodeType==DOMNode.COMMENT_NODE){
_623e=this.ownerDocument.createComment(_623c.data);
}else{
throw (new DOMException(DOMException.NOT_SUPPORTED_ERR));
}
}
}
}
}
}
}
}
if(deep){
for(var i=0;i<_623c.childNodes.length;i++){
_623e.appendChild(this.ownerDocument.importNode(_623c.childNodes.item(i),true));
}
}
this.getOwnerDocument()._performingImportNodeOperation=false;
return _623e;
}
catch(eAny){
this.getOwnerDocument()._performingImportNodeOperation=false;
throw eAny;
}
};
DOMNode.prototype.__escapeString=function DOMNode__escapeString(str){
return __escapeString(str);
};
DOMNode.prototype.__unescapeString=function DOMNode__unescapeString(str){
return __unescapeString(str);
};
DOMDocument=function(_6242){
this._class=addClass(this._class,"DOMDocument");
this.DOMNode=DOMNode;
this.DOMNode(this);
this.doctype=null;
this.implementation=_6242;
this.documentElement=null;
this.all=[];
this.nodeName="#document";
this.nodeType=DOMNode.DOCUMENT_NODE;
this._id=0;
this._lastId=0;
this._parseComplete=false;
this.ownerDocument=this;
this._performingImportNodeOperation=false;
};
DOMDocument.prototype=new DOMNode;
DOMDocument.prototype.getDoctype=function DOMDocument_getDoctype(){
return this.doctype;
};
DOMDocument.prototype.getImplementation=function DOMDocument_implementation(){
return this.implementation;
};
DOMDocument.prototype.getDocumentElement=function DOMDocument_getDocumentElement(){
return this.documentElement;
};
DOMDocument.prototype.createElement=function DOMDocument_createElement(_6243){
if(this.ownerDocument.implementation.errorChecking&&(!this.ownerDocument.implementation._isValidName(_6243))){
throw (new DOMException(DOMException.INVALID_CHARACTER_ERR));
}
var node=new DOMElement(this);
node.tagName=_6243;
node.nodeName=_6243;
this.all[this.all.length]=node;
return node;
};
DOMDocument.prototype.createDocumentFragment=function DOMDocument_createDocumentFragment(){
var node=new DOMDocumentFragment(this);
return node;
};
DOMDocument.prototype.createTextNode=function DOMDocument_createTextNode(data){
var node=new DOMText(this);
node.data=data;
node.nodeValue=data;
node.length=data.length;
return node;
};
DOMDocument.prototype.createComment=function DOMDocument_createComment(data){
var node=new DOMComment(this);
node.data=data;
node.nodeValue=data;
node.length=data.length;
return node;
};
DOMDocument.prototype.createCDATASection=function DOMDocument_createCDATASection(data){
var node=new DOMCDATASection(this);
node.data=data;
node.nodeValue=data;
node.length=data.length;
return node;
};
DOMDocument.prototype.createProcessingInstruction=function DOMDocument_createProcessingInstruction(_624c,data){
if(this.ownerDocument.implementation.errorChecking&&(!this.implementation._isValidName(_624c))){
throw (new DOMException(DOMException.INVALID_CHARACTER_ERR));
}
var node=new DOMProcessingInstruction(this);
node.target=_624c;
node.nodeName=_624c;
node.data=data;
node.nodeValue=data;
node.length=data.length;
return node;
};
DOMDocument.prototype.createAttribute=function DOMDocument_createAttribute(name){
if(this.ownerDocument.implementation.errorChecking&&(!this.ownerDocument.implementation._isValidName(name))){
throw (new DOMException(DOMException.INVALID_CHARACTER_ERR));
}
var node=new DOMAttr(this);
node.name=name;
node.nodeName=name;
return node;
};
DOMDocument.prototype.createElementNS=function DOMDocument_createElementNS(_6251,_6252){
if(this.ownerDocument.implementation.errorChecking){
if(!this.ownerDocument._isValidNamespace(_6251,_6252)){
throw (new DOMException(DOMException.NAMESPACE_ERR));
}
if(!this.ownerDocument.implementation._isValidName(_6252)){
throw (new DOMException(DOMException.INVALID_CHARACTER_ERR));
}
}
var node=new DOMElement(this);
var qname=this.implementation._parseQName(_6252);
node.nodeName=_6252;
node.namespaceURI=_6251;
node.prefix=qname.prefix;
node.localName=qname.localName;
node.tagName=_6252;
this.all[this.all.length]=node;
return node;
};
DOMDocument.prototype.createAttributeNS=function DOMDocument_createAttributeNS(_6255,_6256){
if(this.ownerDocument.implementation.errorChecking){
if(!this.ownerDocument._isValidNamespace(_6255,_6256,true)){
throw (new DOMException(DOMException.NAMESPACE_ERR));
}
if(!this.ownerDocument.implementation._isValidName(_6256)){
throw (new DOMException(DOMException.INVALID_CHARACTER_ERR));
}
}
var node=new DOMAttr(this);
var qname=this.implementation._parseQName(_6256);
node.nodeName=_6256;
node.namespaceURI=_6255;
node.prefix=qname.prefix;
node.localName=qname.localName;
node.name=_6256;
node.nodeValue="";
return node;
};
DOMDocument.prototype.createNamespace=function DOMDocument_createNamespace(_6259){
var node=new DOMNamespace(this);
var qname=this.implementation._parseQName(_6259);
node.nodeName=_6259;
node.prefix=qname.prefix;
node.localName=qname.localName;
node.name=_6259;
node.nodeValue="";
return node;
};
DOMDocument.prototype.getElementById=function DOMDocument_getElementById(_625c){
retNode=null;
for(var i=0;i<this.all.length;i++){
var node=this.all[i];
if((node.id==_625c)&&(node._isAncestor(node.ownerDocument.documentElement))){
retNode=node;
break;
}
}
return retNode;
};
DOMDocument.prototype._genId=function DOMDocument__genId(){
this._lastId+=1;
return this._lastId;
};
DOMDocument.prototype._isValidNamespace=function DOMDocument__isValidNamespace(_625f,_6260,_6261){
if(this._performingImportNodeOperation==true){
return true;
}
var valid=true;
var qName=this.implementation._parseQName(_6260);
if(this._parseComplete==true){
if(qName.localName.indexOf(":")>-1){
valid=false;
}
if((valid)&&(!_6261)){
if(!_625f){
valid=false;
}
}
if((valid)&&(qName.prefix=="")){
valid=false;
}
}
if((valid)&&(qName.prefix=="xml")&&(_625f!="http://www.w3.org/XML/1998/namespace")){
valid=false;
}
return valid;
};
DOMDocument.prototype.toString=function DOMDocument_toString(){
return ""+this.childNodes;
};
DOMElement=function(_6264){
this._class=addClass(this._class,"DOMElement");
this.DOMNode=DOMNode;
this.DOMNode(_6264);
this.tagName="";
this.id="";
this.nodeType=DOMNode.ELEMENT_NODE;
};
DOMElement.prototype=new DOMNode;
DOMElement.prototype.getTagName=function DOMElement_getTagName(){
return this.tagName;
};
DOMElement.prototype.getAttribute=function DOMElement_getAttribute(name){
var ret="";
var attr=this.attributes.getNamedItem(name);
if(attr){
ret=attr.value;
}
return ret;
};
DOMElement.prototype.setAttribute=function DOMElement_setAttribute(name,value){
var attr=this.attributes.getNamedItem(name);
if(!attr){
attr=this.ownerDocument.createAttribute(name);
}
var value=new String(value);
if(this.ownerDocument.implementation.errorChecking){
if(attr._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if(!this.ownerDocument.implementation._isValidString(value)){
throw (new DOMException(DOMException.INVALID_CHARACTER_ERR));
}
}
if(this.ownerDocument.implementation._isIdDeclaration(name)){
this.id=value;
}
attr.value=value;
attr.nodeValue=value;
if(value.length>0){
attr.specified=true;
}else{
attr.specified=false;
}
this.attributes.setNamedItem(attr);
};
DOMElement.prototype.removeAttribute=function DOMElement_removeAttribute(name){
return this.attributes.removeNamedItem(name);
};
DOMElement.prototype.getAttributeNode=function DOMElement_getAttributeNode(name){
return this.attributes.getNamedItem(name);
};
DOMElement.prototype.setAttributeNode=function DOMElement_setAttributeNode(_626d){
if(this.ownerDocument.implementation._isIdDeclaration(_626d.name)){
this.id=_626d.value;
}
return this.attributes.setNamedItem(_626d);
};
DOMElement.prototype.removeAttributeNode=function DOMElement_removeAttributeNode(_626e){
if(this.ownerDocument.implementation.errorChecking&&_626e._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
var _626f=this.attributes._findItemIndex(_626e._id);
if(this.ownerDocument.implementation.errorChecking&&(_626f<0)){
throw (new DOMException(DOMException.NOT_FOUND_ERR));
}
return this.attributes._removeChild(_626f);
};
DOMElement.prototype.getAttributeNS=function DOMElement_getAttributeNS(_6270,_6271){
var ret="";
var attr=this.attributes.getNamedItemNS(_6270,_6271);
if(attr){
ret=attr.value;
}
return ret;
};
DOMElement.prototype.setAttributeNS=function DOMElement_setAttributeNS(_6274,_6275,value){
var attr=this.attributes.getNamedItem(_6274,_6275);
if(!attr){
attr=this.ownerDocument.createAttributeNS(_6274,_6275);
}
var value=new String(value);
if(this.ownerDocument.implementation.errorChecking){
if(attr._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if(!this.ownerDocument._isValidNamespace(_6274,_6275)){
throw (new DOMException(DOMException.NAMESPACE_ERR));
}
if(!this.ownerDocument.implementation._isValidString(value)){
throw (new DOMException(DOMException.INVALID_CHARACTER_ERR));
}
}
if(this.ownerDocument.implementation._isIdDeclaration(name)){
this.id=value;
}
attr.value=value;
attr.nodeValue=value;
if(value.length>0){
attr.specified=true;
}else{
attr.specified=false;
}
this.attributes.setNamedItemNS(attr);
};
DOMElement.prototype.removeAttributeNS=function DOMElement_removeAttributeNS(_6278,_6279){
return this.attributes.removeNamedItemNS(_6278,_6279);
};
DOMElement.prototype.getAttributeNodeNS=function DOMElement_getAttributeNodeNS(_627a,_627b){
return this.attributes.getNamedItemNS(_627a,_627b);
};
DOMElement.prototype.setAttributeNodeNS=function DOMElement_setAttributeNodeNS(_627c){
if((_627c.prefix=="")&&this.ownerDocument.implementation._isIdDeclaration(_627c.name)){
this.id=_627c.value;
}
return this.attributes.setNamedItemNS(_627c);
};
DOMElement.prototype.hasAttribute=function DOMElement_hasAttribute(name){
return this.attributes._hasAttribute(name);
};
DOMElement.prototype.hasAttributeNS=function DOMElement_hasAttributeNS(_627e,_627f){
return this.attributes._hasAttributeNS(_627e,_627f);
};
DOMElement.prototype.toString=function DOMElement_toString(){
var ret="";
var ns=this._namespaces.toString();
if(ns.length>0){
ns=" "+ns;
}
var attrs=this.attributes.toString();
if(attrs.length>0){
attrs=" "+attrs;
}
ret+="<"+this.nodeName+ns+attrs+">";
ret+=this.childNodes.toString();
ret+="</"+this.nodeName+">";
return ret;
};
DOMAttr=function(_6283){
this._class=addClass(this._class,"DOMAttr");
this.DOMNode=DOMNode;
this.DOMNode(_6283);
this.name="";
this.specified=false;
this.value="";
this.nodeType=DOMNode.ATTRIBUTE_NODE;
this.ownerElement=null;
this.childNodes=null;
this.attributes=null;
};
DOMAttr.prototype=new DOMNode;
DOMAttr.prototype.getName=function DOMAttr_getName(){
return this.nodeName;
};
DOMAttr.prototype.getSpecified=function DOMAttr_getSpecified(){
return this.specified;
};
DOMAttr.prototype.getValue=function DOMAttr_getValue(){
return this.nodeValue;
};
DOMAttr.prototype.setValue=function DOMAttr_setValue(value){
if(this.ownerDocument.implementation.errorChecking&&this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
this.setNodeValue(value);
};
DOMAttr.prototype.setNodeValue=function DOMAttr_setNodeValue(value){
this.nodeValue=new String(value);
this.value=this.nodeValue;
this.specified=(this.value.length>0);
};
DOMAttr.prototype.toString=function DOMAttr_toString(){
var ret="";
ret+=this.nodeName+"=\""+this.__escapeString(this.nodeValue)+"\"";
return ret;
};
DOMAttr.prototype.getOwnerElement=function(){
return this.ownerElement;
};
DOMNamespace=function(_6287){
this._class=addClass(this._class,"DOMNamespace");
this.DOMNode=DOMNode;
this.DOMNode(_6287);
this.name="";
this.specified=false;
this.value="";
this.nodeType=DOMNode.NAMESPACE_NODE;
};
DOMNamespace.prototype=new DOMNode;
DOMNamespace.prototype.getValue=function DOMNamespace_getValue(){
return this.nodeValue;
};
DOMNamespace.prototype.setValue=function DOMNamespace_setValue(value){
this.nodeValue=new String(value);
this.value=this.nodeValue;
};
DOMNamespace.prototype.toString=function DOMNamespace_toString(){
var ret="";
if(this.nodeName!=""){
ret+=this.nodeName+"=\""+this.__escapeString(this.nodeValue)+"\"";
}else{
ret+="xmlns=\""+this.__escapeString(this.nodeValue)+"\"";
}
return ret;
};
DOMCharacterData=function(_628a){
this._class=addClass(this._class,"DOMCharacterData");
this.DOMNode=DOMNode;
this.DOMNode(_628a);
this.data="";
this.length=0;
};
DOMCharacterData.prototype=new DOMNode;
DOMCharacterData.prototype.getData=function DOMCharacterData_getData(){
return this.nodeValue;
};
DOMCharacterData.prototype.setData=function DOMCharacterData_setData(data){
this.setNodeValue(data);
};
DOMCharacterData.prototype.setNodeValue=function DOMCharacterData_setNodeValue(data){
if(this.ownerDocument.implementation.errorChecking&&this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
this.nodeValue=new String(data);
this.data=this.nodeValue;
this.length=this.nodeValue.length;
};
DOMCharacterData.prototype.getLength=function DOMCharacterData_getLength(){
return this.nodeValue.length;
};
DOMCharacterData.prototype.substringData=function DOMCharacterData_substringData(_628d,count){
var ret=null;
if(this.data){
if(this.ownerDocument.implementation.errorChecking&&((_628d<0)||(_628d>this.data.length)||(count<0))){
throw (new DOMException(DOMException.INDEX_SIZE_ERR));
}
if(!count){
ret=this.data.substring(_628d);
}else{
ret=this.data.substring(_628d,_628d+count);
}
}
return ret;
};
DOMCharacterData.prototype.appendData=function DOMCharacterData_appendData(arg){
if(this.ownerDocument.implementation.errorChecking&&this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
this.setData(""+this.data+arg);
};
DOMCharacterData.prototype.insertData=function DOMCharacterData_insertData(_6291,arg){
if(this.ownerDocument.implementation.errorChecking&&this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if(this.data){
if(this.ownerDocument.implementation.errorChecking&&((_6291<0)||(_6291>this.data.length))){
throw (new DOMException(DOMException.INDEX_SIZE_ERR));
}
this.setData(this.data.substring(0,_6291).concat(arg,this.data.substring(_6291)));
}else{
if(this.ownerDocument.implementation.errorChecking&&(_6291!=0)){
throw (new DOMException(DOMException.INDEX_SIZE_ERR));
}
this.setData(arg);
}
};
DOMCharacterData.prototype.deleteData=function DOMCharacterData_deleteData(_6293,count){
if(this.ownerDocument.implementation.errorChecking&&this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if(this.data){
if(this.ownerDocument.implementation.errorChecking&&((_6293<0)||(_6293>this.data.length)||(count<0))){
throw (new DOMException(DOMException.INDEX_SIZE_ERR));
}
if(!count||(_6293+count)>this.data.length){
this.setData(this.data.substring(0,_6293));
}else{
this.setData(this.data.substring(0,_6293).concat(this.data.substring(_6293+count)));
}
}
};
DOMCharacterData.prototype.replaceData=function DOMCharacterData_replaceData(_6295,count,arg){
if(this.ownerDocument.implementation.errorChecking&&this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if(this.data){
if(this.ownerDocument.implementation.errorChecking&&((_6295<0)||(_6295>this.data.length)||(count<0))){
throw (new DOMException(DOMException.INDEX_SIZE_ERR));
}
this.setData(this.data.substring(0,_6295).concat(arg,this.data.substring(_6295+count)));
}else{
this.setData(arg);
}
};
DOMText=function(_6298){
this._class=addClass(this._class,"DOMText");
this.DOMCharacterData=DOMCharacterData;
this.DOMCharacterData(_6298);
this.nodeName="#text";
this.nodeType=DOMNode.TEXT_NODE;
};
DOMText.prototype=new DOMCharacterData;
DOMText.prototype.splitText=function DOMText_splitText(_6299){
var data,inode;
if(this.ownerDocument.implementation.errorChecking){
if(this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if((_6299<0)||(_6299>this.data.length)){
throw (new DOMException(DOMException.INDEX_SIZE_ERR));
}
}
if(this.parentNode){
data=this.substringData(_6299);
inode=this.ownerDocument.createTextNode(data);
if(this.nextSibling){
this.parentNode.insertBefore(inode,this.nextSibling);
}else{
this.parentNode.appendChild(inode);
}
this.deleteData(_6299);
}
return inode;
};
DOMText.prototype.toString=function DOMText_toString(){
return this.__escapeString(""+this.nodeValue);
};
DOMCDATASection=function(_629b){
this._class=addClass(this._class,"DOMCDATASection");
this.DOMCharacterData=DOMCharacterData;
this.DOMCharacterData(_629b);
this.nodeName="#cdata-section";
this.nodeType=DOMNode.CDATA_SECTION_NODE;
};
DOMCDATASection.prototype=new DOMCharacterData;
DOMCDATASection.prototype.splitText=function DOMCDATASection_splitText(_629c){
var data,inode;
if(this.ownerDocument.implementation.errorChecking){
if(this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
if((_629c<0)||(_629c>this.data.length)){
throw (new DOMException(DOMException.INDEX_SIZE_ERR));
}
}
if(this.parentNode){
data=this.substringData(_629c);
inode=this.ownerDocument.createCDATASection(data);
if(this.nextSibling){
this.parentNode.insertBefore(inode,this.nextSibling);
}else{
this.parentNode.appendChild(inode);
}
this.deleteData(_629c);
}
return inode;
};
DOMCDATASection.prototype.toString=function DOMCDATASection_toString(){
var ret="";
ret+="<![CDATA["+this.nodeValue+"]]>";
return ret;
};
DOMComment=function(_629f){
this._class=addClass(this._class,"DOMComment");
this.DOMCharacterData=DOMCharacterData;
this.DOMCharacterData(_629f);
this.nodeName="#comment";
this.nodeType=DOMNode.COMMENT_NODE;
};
DOMComment.prototype=new DOMCharacterData;
DOMComment.prototype.toString=function DOMComment_toString(){
var ret="";
ret+="<!--"+this.nodeValue+"-->";
return ret;
};
DOMProcessingInstruction=function(_62a1){
this._class=addClass(this._class,"DOMProcessingInstruction");
this.DOMNode=DOMNode;
this.DOMNode(_62a1);
this.target="";
this.data="";
this.nodeType=DOMNode.PROCESSING_INSTRUCTION_NODE;
};
DOMProcessingInstruction.prototype=new DOMNode;
DOMProcessingInstruction.prototype.getTarget=function DOMProcessingInstruction_getTarget(){
return this.nodeName;
};
DOMProcessingInstruction.prototype.getData=function DOMProcessingInstruction_getData(){
return this.nodeValue;
};
DOMProcessingInstruction.prototype.setData=function DOMProcessingInstruction_setData(data){
this.setNodeValue(data);
};
DOMProcessingInstruction.prototype.setNodeValue=function DOMProcessingInstruction_setNodeValue(data){
if(this.ownerDocument.implementation.errorChecking&&this._readonly){
throw (new DOMException(DOMException.NO_MODIFICATION_ALLOWED_ERR));
}
this.nodeValue=new String(data);
this.data=this.nodeValue;
};
DOMProcessingInstruction.prototype.toString=function DOMProcessingInstruction_toString(){
var ret="";
ret+="<?"+this.nodeName+" "+this.nodeValue+" ?>";
return ret;
};
DOMDocumentFragment=function(_62a5){
this._class=addClass(this._class,"DOMDocumentFragment");
this.DOMNode=DOMNode;
this.DOMNode(_62a5);
this.nodeName="#document-fragment";
this.nodeType=DOMNode.DOCUMENT_FRAGMENT_NODE;
};
DOMDocumentFragment.prototype=new DOMNode;
DOMDocumentFragment.prototype.toString=function DOMDocumentFragment_toString(){
var xml="";
var _62a7=this.getChildNodes().getLength();
for(intLoop=0;intLoop<_62a7;intLoop++){
xml+=this.getChildNodes().item(intLoop).toString();
}
return xml;
};
DOMDocumentType=function(){
alert("DOMDocumentType.constructor(): Not Implemented");
};
DOMEntity=function(){
alert("DOMEntity.constructor(): Not Implemented");
};
DOMEntityReference=function(){
alert("DOMEntityReference.constructor(): Not Implemented");
};
DOMNotation=function(){
alert("DOMNotation.constructor(): Not Implemented");
};
Strings=new Object();
Strings.WHITESPACE=" \t\n\r";
Strings.QUOTES="\"'";
Strings.isEmpty=function Strings_isEmpty(strD){
return (strD===null)||(strD.length===0);
};
Strings.indexOfNonWhitespace=function Strings_indexOfNonWhitespace(strD,iB,iE){
if(Strings.isEmpty(strD)){
return -1;
}
iB=iB||0;
iE=iE||strD.length;
for(var i=iB;i<iE;i++){
if(Strings.WHITESPACE.indexOf(strD.charAt(i))==-1){
return i;
}
}
return -1;
};
Strings.lastIndexOfNonWhitespace=function Strings_lastIndexOfNonWhitespace(strD,iB,iE){
if(Strings.isEmpty(strD)){
return -1;
}
iB=iB||0;
iE=iE||strD.length;
for(var i=iE-1;i>=iB;i--){
if(Strings.WHITESPACE.indexOf(strD.charAt(i))==-1){
return i;
}
}
return -1;
};
Strings.indexOfWhitespace=function Strings_indexOfWhitespace(strD,iB,iE){
if(Strings.isEmpty(strD)){
return -1;
}
iB=iB||0;
iE=iE||strD.length;
for(var i=iB;i<iE;i++){
if(Strings.WHITESPACE.indexOf(strD.charAt(i))!=-1){
return i;
}
}
return -1;
};
Strings.replace=function Strings_replace(strD,iB,iE,strF,strR){
if(Strings.isEmpty(strD)){
return "";
}
iB=iB||0;
iE=iE||strD.length;
return strD.substring(iB,iE).split(strF).join(strR);
};
Strings.getLineNumber=function Strings_getLineNumber(strD,iP){
if(Strings.isEmpty(strD)){
return -1;
}
iP=iP||strD.length;
return strD.substring(0,iP).split("\n").length;
};
Strings.getColumnNumber=function Strings_getColumnNumber(strD,iP){
if(Strings.isEmpty(strD)){
return -1;
}
iP=iP||strD.length;
var arrD=strD.substring(0,iP).split("\n");
var _62bf=arrD[arrD.length-1];
arrD.length--;
var _62c0=arrD.join("\n").length;
return iP-_62c0;
};
StringBuffer=function(){
this._a=[];
};
StringBuffer.prototype.append=function StringBuffer_append(d){
this._a[this._a.length]=d;
};
StringBuffer.prototype.toString=function StringBuffer_toString(){
return this._a.join("");
};
draw2d.XMLSerializer=function(){
alert("do not init this class. Use the static methods instead");
};
draw2d.XMLSerializer.toXML=function(obj,_571a,_571b){
if(_571a==undefined){
_571a="model";
}
_571b=_571b?_571b:"";
var t=draw2d.XMLSerializer.getTypeName(obj);
var s=_571b+"<"+_571a+" type=\""+t+"\">";
switch(t){
case "int":
case "number":
case "boolean":
s+=obj;
break;
case "string":
s+=draw2d.XMLSerializer.xmlEncode(obj);
break;
case "date":
s+=obj.toLocaleString();
break;
case "Array":
case "array":
s+="\n";
var _571e=_571b+"   ";
for(var i=0;i<obj.length;i++){
s+=draw2d.XMLSerializer.toXML(obj[i],("element"),_571e);
}
s+=_571b;
break;
default:
if(obj!==null){
s+="\n";
if(obj instanceof draw2d.ArrayList){
obj.trimToSize();
}
var _5720=obj.getPersistentAttributes();
var _571e=_571b+"   ";
for(var name in _5720){
s+=draw2d.XMLSerializer.toXML(_5720[name],name,_571e);
}
s+=_571b;
}
break;
}
s+="</"+_571a+">\n";
return s;
};
draw2d.XMLSerializer.isSimpleVar=function(t){
switch(t){
case "int":
case "string":
case "String":
case "Number":
case "number":
case "Boolean":
case "boolean":
case "bool":
case "dateTime":
case "Date":
case "date":
case "float":
return true;
}
return false;
};
draw2d.XMLSerializer.getTypeName=function(obj){
if(obj===null){
return "undefined";
}
if(obj instanceof Array){
return "Array";
}
if(obj instanceof Date){
return "Date";
}
var t=typeof (obj);
if(t=="number"){
return (parseInt(obj).toString()==obj)?"int":"number";
}
if(draw2d.XMLSerializer.isSimpleVar(t)){
return t;
}
return obj.type.replace("@NAMESPACE"+"@","");
};
draw2d.XMLSerializer.xmlEncode=function(_5725){
var _5726=_5725;
var amp=/&/gi;
var gt=/>/gi;
var lt=/</gi;
var quot=/"/gi;
var apos=/'/gi;
var _572c="&#62;";
var _572d="&#38;#60;";
var _572e="&#38;#38;";
var _572f="&#34;";
var _5730="&#39;";
_5726=_5726.replace(amp,_572e);
_5726=_5726.replace(quot,_572f);
_5726=_5726.replace(lt,_572d);
_5726=_5726.replace(gt,_572c);
_5726=_5726.replace(apos,_5730);
return _5726;
};
draw2d.XMLDeserializer=function(){
alert("do not init this class. Use the static methods instead");
};
draw2d.XMLDeserializer.fromXML=function(node,_4fec){
var _4fed=""+node.getAttributes().getNamedItem("type").getNodeValue();
var value=node.getNodeValue();
switch(_4fed){
case "int":
try{
return parseInt(""+node.getChildNodes().item(0).getNodeValue());
}
catch(e){
alert("Error:"+e+"\nDataType:"+_4fed+"\nXML Node:"+node);
}
case "string":
case "String":
try{
if(node.getChildNodes().getLength()>0){
return ""+node.getChildNodes().item(0).getNodeValue();
}
return "";
}
catch(e){
alert("Error:"+e+"\nDataType:"+_4fed+"\nXML Node:"+node);
}
case "Number":
case "number":
try{
return parseFloat(""+node.getChildNodes().item(0).getNodeValue());
}
catch(e){
alert("Error:"+e+"\nDataType:"+_4fed+"\nXML Node:"+node);
}
case "Boolean":
case "boolean":
case "bool":
try{
return "true"==(""+node.getChildNodes().item(0).getNodeValue()).toLowerCase();
}
catch(e){
alert("Error:"+e+"\nDataType:"+_4fed+"\nXML Node:"+node);
}
case "dateTime":
case "Date":
case "date":
try{
return new Date(""+node.getChildNodes().item(0).getNodeValue());
}
catch(e){
alert("Error:"+e+"\nDataType:"+_4fed+"\nXML Node:"+node);
}
case "float":
try{
return parseFloat(""+node.getChildNodes().item(0).getNodeValue());
}
catch(e){
alert("Error:"+e+"\nDataType:"+_4fed+"\nXML Node:"+node);
}
break;
}
_4fed=_4fed.replace("@NAMESPACE"+"@","");
var obj=eval("new "+_4fed+"()");
if(_4fec!=undefined&&obj.setModelParent!=undefined){
obj.setModelParent(_4fec);
}
var _4ff0=node.getChildNodes();
for(var i=0;i<_4ff0.length;i++){
var child=_4ff0.item(i);
var _4ff3=child.getNodeName();
if(obj instanceof Array){
_4ff3=i;
}
obj[_4ff3]=draw2d.XMLDeserializer.fromXML(child,obj instanceof draw2d.AbstractObjectModel?obj:_4fec);
}
return obj;
};
draw2d.EditPolicy=function(_5cb5){
this.policy=_5cb5;
};
draw2d.EditPolicy.DELETE="DELETE";
draw2d.EditPolicy.MOVE="MOVE";
draw2d.EditPolicy.CONNECT="CONNECT";
draw2d.EditPolicy.RESIZE="RESIZE";
draw2d.EditPolicy.prototype.type="draw2d.EditPolicy";
draw2d.EditPolicy.prototype.getPolicy=function(){
return this.policy;
};
draw2d.AbstractPalettePart=function(){
this.x=0;
this.y=0;
this.html=null;
};
draw2d.AbstractPalettePart.prototype.type="draw2d.AbstractPalettePart";
draw2d.AbstractPalettePart.prototype=new draw2d.Draggable();
draw2d.AbstractPalettePart.prototype.createHTMLElement=function(){
var item=document.createElement("div");
item.id=this.id;
item.style.position="absolute";
item.style.height="24px";
item.style.width="24px";
return item;
};
draw2d.AbstractPalettePart.prototype.setEnviroment=function(_600c,_600d){
this.palette=_600d;
this.workflow=_600c;
};
draw2d.AbstractPalettePart.prototype.getHTMLElement=function(){
if(this.html===null){
this.html=this.createHTMLElement();
draw2d.Draggable.call(this,this.html);
}
return this.html;
};
draw2d.AbstractPalettePart.prototype.onDrop=function(_600e,_600f){
var _6010=this.workflow.getScrollLeft();
var _6011=this.workflow.getScrollTop();
var _6012=this.workflow.getAbsoluteX();
var _6013=this.workflow.getAbsoluteY();
this.setPosition(this.x,this.y);
this.execute(_600e+_6010-_6012,_600f+_6011-_6013);
};
draw2d.AbstractPalettePart.prototype.execute=function(x,y){
alert("inerited class should override the method 'draw2d.AbstractPalettePart.prototype.execute'");
};
draw2d.AbstractPalettePart.prototype.setTooltip=function(_6016){
this.tooltip=_6016;
if(this.tooltip!==null){
this.html.title=this.tooltip;
}else{
this.html.title="";
}
};
draw2d.AbstractPalettePart.prototype.setDimension=function(w,h){
this.width=w;
this.height=h;
if(this.html===null){
return;
}
this.html.style.width=this.width+"px";
this.html.style.height=this.height+"px";
};
draw2d.AbstractPalettePart.prototype.setPosition=function(xPos,yPos){
this.x=Math.max(0,xPos);
this.y=Math.max(0,yPos);
if(this.html===null){
return;
}
this.html.style.left=this.x+"px";
this.html.style.top=this.y+"px";
this.html.style.cursor="move";
};
draw2d.AbstractPalettePart.prototype.getWidth=function(){
return this.width;
};
draw2d.AbstractPalettePart.prototype.getHeight=function(){
return this.height;
};
draw2d.AbstractPalettePart.prototype.getY=function(){
return this.y;
};
draw2d.AbstractPalettePart.prototype.getX=function(){
return this.x;
};
draw2d.AbstractPalettePart.prototype.getPosition=function(){
return new draw2d.Point(this.x,this.y);
};
draw2d.AbstractPalettePart.prototype.disableTextSelection=function(e){
if(typeof e.onselectstart!="undefined"){
e.onselectstart=function(){
return false;
};
}else{
if(typeof e.style.MozUserSelect!="undefined"){
e.style.MozUserSelect="none";
}
}
};
draw2d.ExternalPalette=function(_5b2d,divId){
this.html=document.getElementById(divId);
this.workflow=_5b2d;
this.parts=new draw2d.ArrayList();
};
draw2d.ExternalPalette.prototype.type="draw2d.ExternalPalette";
draw2d.ExternalPalette.prototype.getHTMLElement=function(){
return this.html;
};
draw2d.ExternalPalette.prototype.addPalettePart=function(part){
if(!(part instanceof draw2d.AbstractPalettePart)){
throw "parameter is not instanceof [draw2d.AbstractPalettePart]";
}
this.parts.add(part);
this.html.appendChild(part.getHTMLElement());
part.setEnviroment(this.workflow,this);
};
