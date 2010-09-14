package com.kaltura.commands.baseEntry
{
	import com.kaltura.delegates.baseEntry.BaseEntryUpdateThumbnailImageDelegate;
	import com.kaltura.net.KalturaFileCall;
	
	import flash.net.FileReference;

	public class BaseEntryUpdateThumbnailImage extends KalturaFileCall
	{
		
		
		public var fileData:FileReference;
		
		public function BaseEntryUpdateThumbnailImage( entryId : String, fileData : FileReference )
		{
			service= 'baseEntry';
			action= 'updateThumbnailJpeg';
			applySchema(['entryId'] , [entryId]);
			this.fileData = fileData;
		}

		override public function execute() : void
		{
			setRequestArgument('filterFields',filterFields);
			delegate = new BaseEntryUpdateThumbnailImageDelegate( this , config );
		}
	}
}
