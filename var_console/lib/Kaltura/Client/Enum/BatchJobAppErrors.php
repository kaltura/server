<?php
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

/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Enum_BatchJobAppErrors
{
	const OUTPUT_FILE_DOESNT_EXIST = 11;
	const OUTPUT_FILE_WRONG_SIZE = 12;
	const CANNOT_CREATE_DIRECTORY = 13;
	const FILE_ALREADY_EXISTS = 14;
	const NFS_FILE_DOESNT_EXIST = 21;
	const EXTRACT_MEDIA_FAILED = 31;
	const CLOSER_TIMEOUT = 41;
	const ENGINE_NOT_FOUND = 51;
	const REMOTE_FILE_NOT_FOUND = 61;
	const REMOTE_DOWNLOAD_FAILED = 62;
	const BULK_FILE_NOT_FOUND = 71;
	const BULK_VALIDATION_FAILED = 72;
	const BULK_PARSE_ITEMS_FAILED = 73;
	const BULK_UNKNOWN_ERROR = 74;
	const BULK_INVLAID_BULK_REQUEST_COUNT = 75;
	const BULK_NO_ENTRIES_HANDLED = 76;
	const BULK_ACTION_NOT_SUPPORTED = 77;
	const BULK_MISSING_MANDATORY_PARAMETER = 78;
	const BULK_ITEM_VALIDATION_FAILED = 79;
	const BULK_ITEM_NOT_FOUND = 701;
	const BULK_ELEMENT_NOT_FOUND = 702;
	const CONVERSION_FAILED = 81;
	const THUMBNAIL_NOT_CREATED = 91;
	const MISSING_PARAMETERS = 92;
}

