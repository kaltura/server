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
class Kaltura_Client_DropFolder_Enum_DropFolderFileOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const FILE_NAME_ASC = "+fileName";
	const FILE_NAME_DESC = "-fileName";
	const FILE_SIZE_ASC = "+fileSize";
	const FILE_SIZE_DESC = "-fileSize";
	const FILE_SIZE_LAST_SET_AT_ASC = "+fileSizeLastSetAt";
	const FILE_SIZE_LAST_SET_AT_DESC = "-fileSizeLastSetAt";
	const PARSED_SLUG_ASC = "+parsedSlug";
	const PARSED_SLUG_DESC = "-parsedSlug";
	const PARSED_FLAVOR_ASC = "+parsedFlavor";
	const PARSED_FLAVOR_DESC = "-parsedFlavor";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

