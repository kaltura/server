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
class Kaltura_Client_Type_MailJob extends Kaltura_Client_Type_BaseJob
{
	public function getKalturaObjectType()
	{
		return 'KalturaMailJob';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->mailType = (string)$xml->mailType;
		if(count($xml->mailPriority))
			$this->mailPriority = (int)$xml->mailPriority;
		if(count($xml->status))
			$this->status = (int)$xml->status;
		$this->recipientName = (string)$xml->recipientName;
		$this->recipientEmail = (string)$xml->recipientEmail;
		if(count($xml->recipientId))
			$this->recipientId = (int)$xml->recipientId;
		$this->fromName = (string)$xml->fromName;
		$this->fromEmail = (string)$xml->fromEmail;
		$this->bodyParams = (string)$xml->bodyParams;
		$this->subjectParams = (string)$xml->subjectParams;
		$this->templatePath = (string)$xml->templatePath;
		if(count($xml->culture))
			$this->culture = (int)$xml->culture;
		if(count($xml->campaignId))
			$this->campaignId = (int)$xml->campaignId;
		if(count($xml->minSendDate))
			$this->minSendDate = (int)$xml->minSendDate;
	}
	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_MailType
	 */
	public $mailType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mailPriority = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_MailJobStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $recipientName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $recipientEmail = null;

	/**
	 * kuserId  
	 * 	 
	 *
	 * @var int
	 */
	public $recipientId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fromName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fromEmail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bodyParams = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $subjectParams = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $templatePath = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $culture = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $campaignId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $minSendDate = null;


}

