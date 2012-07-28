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
class Kaltura_Client_DropFolder_DropFolderFileService extends Kaltura_Client_ServiceBase
{
	function __construct(Kaltura_Client_Client $client = null)
	{
		parent::__construct($client);
	}

	function add(Kaltura_Client_DropFolder_Type_DropFolderFile $dropFolderFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFile", $dropFolderFile->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "add", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "Kaltura_Client_DropFolder_Type_DropFolderFile");
		return $resultObject;
	}

	function get($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "get", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "Kaltura_Client_DropFolder_Type_DropFolderFile");
		return $resultObject;
	}

	function update($dropFolderFileId, Kaltura_Client_DropFolder_Type_DropFolderFile $dropFolderFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->addParam($kparams, "dropFolderFile", $dropFolderFile->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "update", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "Kaltura_Client_DropFolder_Type_DropFolderFile");
		return $resultObject;
	}

	function updateStatus($dropFolderFileId, $status)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->addParam($kparams, "status", $status);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "updateStatus", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "Kaltura_Client_DropFolder_Type_DropFolderFile");
		return $resultObject;
	}

	function delete($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "Kaltura_Client_DropFolder_Type_DropFolderFile");
		return $resultObject;
	}

	function listAction(Kaltura_Client_DropFolder_Type_DropFolderFileFilter $filter = null, Kaltura_Client_Type_FilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "Kaltura_Client_DropFolder_Type_DropFolderFileListResponse");
		return $resultObject;
	}

	function ignore($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "ignore", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "Kaltura_Client_DropFolder_Type_DropFolderFile");
		return $resultObject;
	}
}
