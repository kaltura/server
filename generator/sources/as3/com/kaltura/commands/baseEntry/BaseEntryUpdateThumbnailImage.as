// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================
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
