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
		if(empty($xml->to))
			$this->to = array();
		else
			$this->to = Kaltura_Client_Client::unmarshalItem($xml->to);
		if(empty($xml->cc))
			$this->cc = array();
		else
			$this->cc = Kaltura_Client_Client::unmarshalItem($xml->cc);
		if(empty($xml->bcc))
			$this->bcc = array();
		else
			$this->bcc = Kaltura_Client_Client::unmarshalItem($xml->bcc);
		if(empty($xml->replyTo))
			$this->replyTo = array();
		else
			$this->replyTo = Kaltura_Client_Client::unmarshalItem($xml->replyTo);
		if(count($xml->priority))
			$this->priority = (int)$xml->priority;
		$this->confirmReadingTo = (string)$xml->confirmReadingTo;
		$this->hostname = (string)$xml->hostname;
		$this->messageID = (string)$xml->messageID;
		if(empty($xml->customHeaders))
			$this->customHeaders = array();
		else
			$this->customHeaders = Kaltura_Client_Client::unmarshalItem($xml->customHeaders);
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
	 * Email recipient emails and names
	 * 	 
	 *
	 * @var array of KalturaEmailNotificationRecipient
	 */
	public $to;

	/**
	 * Email cc emails and names
	 * 	 
	 *
	 * @var array of KalturaEmailNotificationRecipient
	 */
	public $cc;

	/**
	 * Email bcc emails and names
	 * 	 
	 *
	 * @var array of KalturaEmailNotificationRecipient
	 */
	public $bcc;

	/**
	 * Email addresses that a reading confirmation will be sent to
	 * 	 
	 *
	 * @var array of KalturaEmailNotificationRecipient
	 */
	public $replyTo;

	/**
	 * Define the email priority
	 * 	 
	 *
	 * @var Kaltura_Client_EmailNotification_Enum_EmailNotificationTemplatePriority
	 */
	public $priority = null;

	/**
	 * Email address that a reading confirmation will be sent
	 * 	 
	 *
	 * @var string
	 */
	public $confirmReadingTo = null;

	/**
	 * Hostname to use in Message-Id and Received headers and as default HELO string. 
	 * 	 If empty, the value returned by SERVER_NAME is used or 'localhost.localdomain'.
	 * 	 
	 *
	 * @var string
	 */
	public $hostname = null;

	/**
	 * Sets the message ID to be used in the Message-Id header.
	 * 	 If empty, a unique id will be generated.
	 * 	 
	 *
	 * @var string
	 */
	public $messageID = null;

	/**
	 * Adds a e-mail custom header
	 * 	 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $customHeaders;

	/**
	 * Define the content dynamic parameters
	 * 	 
	 *
	 * @var array of KalturaEmailNotificationParameter
	 */
	public $contentParameters;


}

