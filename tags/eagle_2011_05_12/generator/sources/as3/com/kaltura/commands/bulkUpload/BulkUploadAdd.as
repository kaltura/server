package com.kaltura.commands.bulkUpload
{
	import com.kaltura.delegates.bulkUpload.BulkUploadAddDelegate;
	import com.kaltura.net.KalturaCall;
	
	import flash.net.FileReference;
	import flash.net.URLRequest;

	public class BulkUploadAdd extends KalturaCall
	{
		public var filterFields : String;
		private var _fr : FileReference;
		
		public function BulkUploadAdd( conversionProfileId : int, fr : FileReference )
		{
			service= 'bulkUpload';
			action= 'add';
			
			_fr = fr;
			
			var keyArr : Array = new Array();
			var valueArr : Array = new Array();
			
			keyArr.push( 'conversionProfileId' );
			valueArr.push( conversionProfileId );
			
			applySchema( keyArr , valueArr );
		}

		override public function execute() : void
		{
			setRequestArgument('filterFields',filterFields);
			delegate = new BulkUploadAddDelegate( this , config , _fr );
		}
	}
}
