package com.kaltura.delegates {
	import com.kaltura.commands.MultiRequest;
	import com.kaltura.commands.QueuedRequest;
	import com.kaltura.config.KalturaConfig;
	import com.kaltura.errors.KalturaError;
	import com.kaltura.net.KalturaCall;
	
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedClassName;

	public class QueuedRequestDelegate extends WebDelegateBase {

		public function QueuedRequestDelegate(call:KalturaCall = null, config:KalturaConfig = null) {
			super(call, config); 
		}
		
		override public function parse( result : XML ) : *
		{
			var resArr : Array = new Array();
			var resInd:int = 0;	// index for scanning result
			for ( var i:int=0; i<(call as QueuedRequest).calls.length; i++ )
			{
				var callClassName : String = getQualifiedClassName( (call as QueuedRequest).calls[i] );
				var commandName : String = callClassName.split("::")[1];
				var packageArr : Array =  (callClassName.split("::")[0]).split(".");
				var importFrom : String = packageArr[packageArr.length-1];
				
				var clsName : String ;
				var cls : Class;
				var myInst : Object;
				
				if (commandName == "MultiRequest") {
					clsName = "com.kaltura.delegates.MultiRequestDelegate"; 
					cls = getDefinitionByName( clsName ) as Class;
					myInst = new cls(null , null);
					// set the call after the constructor, so we don't fire execute()
					myInst.call = (call as QueuedRequest).calls[i];
				}
				else {
					clsName = "com.kaltura.delegates."+importFrom+"."+ commandName +"Delegate"; //'com.kaltura.delegates.session.SessionStartDelegate'
					cls = getDefinitionByName( clsName ) as Class;//(') as Class;
					myInst = new cls(null , null);
				}
				
				
				//build the result as a regular result
				var xml : String = "<result><result>";
				if (commandName == "MultiRequest") {
					// add as many items as the multirequest had
					var nActions:int = ((call as QueuedRequest).calls[i] as MultiRequest).actions.length;
					for (var j:int = 0; j<nActions ; j++) {
						xml += result.result.item[j + resInd].toXMLString();	
					}
					// skip to the result of the next call that wasn't part of the MR:
					resInd+= nActions;
				}
				else {
					xml += result.result.item[resInd].children().toString();
					resInd++;
				}
				xml +="</result></result>";
				
				// add the item or a matching error:
				var kErr:KalturaError = validateKalturaResponse(xml);
				
				if (kErr == null) {
					var res : XML = new XML(xml);
					try {
						var obj : Object = (myInst as WebDelegateBase).parse( res );
						resArr.push( obj );
					} catch (e:Error) {
						kErr = new KalturaError();
						kErr.errorCode = String(e.errorID);
						kErr.errorMsg = e.message;
						resArr.push( kErr );
					}
				}
				else {
					resArr.push(kErr);
				}
			}
			
			return resArr;
		}

		
	}
}