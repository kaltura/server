package com.kaltura.commands.baseEntry
{
	import com.kaltura.delegates.baseEntry.BaseEntryUpdateThumbnailJpegDelegate;
	import com.kaltura.net.KalturaFileCall;
	import flash.utils.ByteArray;

	public class BaseEntryUpdateThumbnailJpeg extends KalturaFileCall
	{
		public function BaseEntryUpdateThumbnailJpeg( entryId : String, fileData : ByteArray )
		{
			service= 'baseEntry';
			action= 'updateThumbnailJpeg';
			applySchema(['entryId'] , [entryId]);
			bytes = fileData;
		}

		override public function execute() : void
		{
			setRequestArgument('filterFields',filterFields);
			delegate = new BaseEntryUpdateThumbnailJpegDelegate( this , config );
		}
	}
}
