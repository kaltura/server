package com.kaltura.commands.media
{
	import com.kaltura.delegates.media.MediaUpdateThumbnailJpegDelegate;
	import com.kaltura.net.KalturaFileCall;

	import flash.utils.ByteArray;

	public class MediaUpdateThumbnailJpeg extends KalturaFileCall
	{
		public function MediaUpdateThumbnailJpeg( entryId : String, fileData : ByteArray )
		{
			service= 'media';
			action= 'updateThumbnailJpeg';
			applySchema(['entryId'] , [entryId]);
			bytes = fileData;
		}

		override public function execute() : void
		{
			delegate = new MediaUpdateThumbnailJpegDelegate( this , config );
		}
	}
}
