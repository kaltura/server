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
class Kaltura_Client_CaptionSearch_CaptionAssetItemService extends Kaltura_Client_ServiceBase
{
	function __construct(Kaltura_Client_Client $client = null)
	{
		parent::__construct($client);
	}

	function parse($captionAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "captionAssetId", $captionAssetId);
		$this->client->queueServiceActionCall("captionsearch_captionassetitem", "parse", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		return $resultObject;
	}

	function search(Kaltura_Client_Type_BaseEntryFilter $entryFilter = null, Kaltura_Client_CaptionSearch_Type_CaptionAssetItemFilter $captionAssetItemFilter = null, Kaltura_Client_Type_FilterPager $captionAssetItemPager = null)
	{
		$kparams = array();
		if ($entryFilter !== null)
			$this->client->addParam($kparams, "entryFilter", $entryFilter->toParams());
		if ($captionAssetItemFilter !== null)
			$this->client->addParam($kparams, "captionAssetItemFilter", $captionAssetItemFilter->toParams());
		if ($captionAssetItemPager !== null)
			$this->client->addParam($kparams, "captionAssetItemPager", $captionAssetItemPager->toParams());
		$this->client->queueServiceActionCall("captionsearch_captionassetitem", "search", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "Kaltura_Client_CaptionSearch_Type_CaptionAssetItemListResponse");
		return $resultObject;
	}
}
