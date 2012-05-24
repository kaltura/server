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
class Kaltura_Client_EmailNotification_Type_EmailNotificationTemplate extends Kaltura_Client_EventNotification_Type_EventNotificationTemplate
{
	public function getKalturaObjectType()
	{
		return 'KalturaEmailNotificationTemplate';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->format = (string)$xml->format;
		$this->subject = (string)$xml->subject;
		$this->body = (string)$xml->body;
		$this->fromEmail = (string)$xml->fromEmail;
		$this->fromName = (string)$xml->fromName;
		$this->toEmail = (string)$xml->toEmail;
		$this->toName = (string)$xml->toName;
		if(count($xml->priority))
			$this->priority = (int)$xml->priority;
		if(empty($xml->contentParameters))
			$this->contentParameters = array();
		else
			$this->contentParameters = Kaltura_Client_Client::unmarshalItem($xml->contentParameters);
	}
	/**
	 * Define the email body format
	 * 	 
	 *
	 * @var Kaltura_Client_EmailNotification_Enum_EmailNotificationFormat
	 */
	public $format = null;

	/**
	 * Define the email subject 
	 * 	 
	 *
	 * @var string
	 */
	public $subject = null;

	/**
	 * Define the email body content
	 * 	 
	 *
	 * @var string
	 */
	public $body = null;

	/**
	 * Define the email sender email
	 * 	 
	 *
	 * @var string
	 */
	public $fromEmail = null;

	/**
	 * Define the email sender name
	 * 	 
	 *
	 * @var string
	 */
	public $fromName = null;

	/**
	 * Define the email receipient email
	 * 	 
	 *
	 * @var string
	 */
	public $toEmail = null;

	/**
	 * Define the email receipient name
	 * 	 
	 *
	 * @var string
	 */
	public $toName = null;

	/**
	 * Define the email priority
	 * 	 
	 *
	 * @var Kaltura_Client_EmailNotification_Enum_EmailNotificationTemplatePriority
	 */
	public $priority = null;

	/**
	 * Define the content dynamic parameters
	 * 	 
	 *
	 * @var array of KalturaEventNotificationParameter
	 */
	public $contentParameters;


}

