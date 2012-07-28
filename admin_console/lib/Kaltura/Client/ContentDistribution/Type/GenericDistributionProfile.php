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
class Kaltura_Client_ContentDistribution_Type_GenericDistributionProfile extends Kaltura_Client_ContentDistribution_Type_DistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaGenericDistributionProfile';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->genericProviderId))
			$this->genericProviderId = (int)$xml->genericProviderId;
		if(!empty($xml->submitAction))
			$this->submitAction = Kaltura_Client_Client::unmarshalItem($xml->submitAction);
		if(!empty($xml->updateAction))
			$this->updateAction = Kaltura_Client_Client::unmarshalItem($xml->updateAction);
		if(!empty($xml->deleteAction))
			$this->deleteAction = Kaltura_Client_Client::unmarshalItem($xml->deleteAction);
		if(!empty($xml->fetchReportAction))
			$this->fetchReportAction = Kaltura_Client_Client::unmarshalItem($xml->fetchReportAction);
		$this->updateRequiredEntryFields = (string)$xml->updateRequiredEntryFields;
		$this->updateRequiredMetadataXPaths = (string)$xml->updateRequiredMetadataXPaths;
	}
	/**
	 * 
	 *
	 * @var int
	 * @insertonly
	 */
	public $genericProviderId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_GenericDistributionProfileAction
	 */
	public $submitAction;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_GenericDistributionProfileAction
	 */
	public $updateAction;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_GenericDistributionProfileAction
	 */
	public $deleteAction;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_GenericDistributionProfileAction
	 */
	public $fetchReportAction;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updateRequiredEntryFields = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updateRequiredMetadataXPaths = null;


}

