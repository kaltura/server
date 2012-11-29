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

class Kaltura_Client_Type_LiveStreamEntry extends Kaltura_Client_Type_MediaEntry
{
	public function getKalturaObjectType()
	{
		return 'KalturaLiveStreamEntry';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->offlineMessage = (string)$xml->offlineMessage;
		$this->streamRemoteId = (string)$xml->streamRemoteId;
		$this->streamRemoteBackupId = (string)$xml->streamRemoteBackupId;
		if(empty($xml->bitrates))
			$this->bitrates = array();
		else
			$this->bitrates = Kaltura_Client_Client::unmarshalItem($xml->bitrates);
		$this->primaryBroadcastingUrl = (string)$xml->primaryBroadcastingUrl;
		$this->secondaryBroadcastingUrl = (string)$xml->secondaryBroadcastingUrl;
		$this->streamName = (string)$xml->streamName;
		$this->streamUrl = (string)$xml->streamUrl;
		$this->hlsStreamUrl = (string)$xml->hlsStreamUrl;
		$this->externalStreamId = (string)$xml->externalStreamId;
		if(count($xml->dvrStatus))
			$this->dvrStatus = (int)$xml->dvrStatus;
		if(count($xml->dvrWindow))
			$this->dvrWindow = (int)$xml->dvrWindow;
		$this->urlManager = (string)$xml->urlManager;
	}
	/**
	 * The message to be presented when the stream is offline
	 * 	 
	 *
	 * @var string
	 */
	public $offlineMessage = null;

	/**
	 * The stream id as provided by the provider
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $streamRemoteId = null;

	/**
	 * The backup stream id as provided by the provider
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $streamRemoteBackupId = null;

	/**
	 * Array of supported bitrates
	 * 	 
	 *
	 * @var array of KalturaLiveStreamBitrate
	 */
	public $bitrates;

	/**
	 * 
	 *
	 * @var string
	 */
	public $primaryBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $secondaryBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $streamName = null;

	/**
	 * The stream url
	 * 	 
	 *
	 * @var string
	 */
	public $streamUrl = null;

	/**
	 * HLS URL - URL for live stream playback on mobile device
	 * 	 
	 *
	 * @var string
	 */
	public $hlsStreamUrl = null;

	/**
	 * Unique idenitifier for the string opposite the provider
	 * 	 
	 *
	 * @var string
	 */
	public $externalStreamId = null;

	/**
	 * DVR Status Enabled/Disabled
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_DVRStatus
	 */
	public $dvrStatus = null;

	/**
	 * Window of time which the DVR allows for backwards scrubbing (in seconds)
	 * 	 
	 *
	 * @var int
	 */
	public $dvrWindow = null;

	/**
	 * URL Manager to handle the live stream URL (for instance, add token)
	 * 	 
	 *
	 * @var string
	 */
	public $urlManager = null;


}

