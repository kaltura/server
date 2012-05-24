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
class Kaltura_Client_PartnerAggregation_Type_DwhHourlyPartner extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaDwhHourlyPartner';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->partnerId))
			$this->partnerId = (int)$xml->partnerId;
		if(count($xml->aggregatedTime))
			$this->aggregatedTime = (int)$xml->aggregatedTime;
		if(count($xml->sumTimeViewed))
			$this->sumTimeViewed = (float)$xml->sumTimeViewed;
		if(count($xml->averageTimeViewed))
			$this->averageTimeViewed = (float)$xml->averageTimeViewed;
		if(count($xml->countPlays))
			$this->countPlays = (int)$xml->countPlays;
		if(count($xml->countLoads))
			$this->countLoads = (int)$xml->countLoads;
		if(count($xml->countPlays25))
			$this->countPlays25 = (int)$xml->countPlays25;
		if(count($xml->countPlays50))
			$this->countPlays50 = (int)$xml->countPlays50;
		if(count($xml->countPlays75))
			$this->countPlays75 = (int)$xml->countPlays75;
		if(count($xml->countPlays100))
			$this->countPlays100 = (int)$xml->countPlays100;
		if(count($xml->countEdit))
			$this->countEdit = (int)$xml->countEdit;
		if(count($xml->countShares))
			$this->countShares = (int)$xml->countShares;
		if(count($xml->countDownload))
			$this->countDownload = (int)$xml->countDownload;
		if(count($xml->countReportAbuse))
			$this->countReportAbuse = (int)$xml->countReportAbuse;
		if(count($xml->countMediaEntries))
			$this->countMediaEntries = (int)$xml->countMediaEntries;
		if(count($xml->countVideoEntries))
			$this->countVideoEntries = (int)$xml->countVideoEntries;
		if(count($xml->countImageEntries))
			$this->countImageEntries = (int)$xml->countImageEntries;
		if(count($xml->countAudioEntries))
			$this->countAudioEntries = (int)$xml->countAudioEntries;
		if(count($xml->countMixEntries))
			$this->countMixEntries = (int)$xml->countMixEntries;
		if(count($xml->countPlaylists))
			$this->countPlaylists = (int)$xml->countPlaylists;
		$this->countBandwidth = (string)$xml->countBandwidth;
		$this->countStorage = (string)$xml->countStorage;
		if(count($xml->countUsers))
			$this->countUsers = (int)$xml->countUsers;
		if(count($xml->countWidgets))
			$this->countWidgets = (int)$xml->countWidgets;
		$this->aggregatedStorage = (string)$xml->aggregatedStorage;
		$this->aggregatedBandwidth = (string)$xml->aggregatedBandwidth;
		if(count($xml->countBufferStart))
			$this->countBufferStart = (int)$xml->countBufferStart;
		if(count($xml->countBufferEnd))
			$this->countBufferEnd = (int)$xml->countBufferEnd;
		if(count($xml->countOpenFullScreen))
			$this->countOpenFullScreen = (int)$xml->countOpenFullScreen;
		if(count($xml->countCloseFullScreen))
			$this->countCloseFullScreen = (int)$xml->countCloseFullScreen;
		if(count($xml->countReplay))
			$this->countReplay = (int)$xml->countReplay;
		if(count($xml->countSeek))
			$this->countSeek = (int)$xml->countSeek;
		if(count($xml->countOpenUpload))
			$this->countOpenUpload = (int)$xml->countOpenUpload;
		if(count($xml->countSavePublish))
			$this->countSavePublish = (int)$xml->countSavePublish;
		if(count($xml->countCloseEditor))
			$this->countCloseEditor = (int)$xml->countCloseEditor;
		if(count($xml->countPreBumperPlayed))
			$this->countPreBumperPlayed = (int)$xml->countPreBumperPlayed;
		if(count($xml->countPostBumperPlayed))
			$this->countPostBumperPlayed = (int)$xml->countPostBumperPlayed;
		if(count($xml->countBumperClicked))
			$this->countBumperClicked = (int)$xml->countBumperClicked;
		if(count($xml->countPrerollStarted))
			$this->countPrerollStarted = (int)$xml->countPrerollStarted;
		if(count($xml->countMidrollStarted))
			$this->countMidrollStarted = (int)$xml->countMidrollStarted;
		if(count($xml->countPostrollStarted))
			$this->countPostrollStarted = (int)$xml->countPostrollStarted;
		if(count($xml->countOverlayStarted))
			$this->countOverlayStarted = (int)$xml->countOverlayStarted;
		if(count($xml->countPrerollClicked))
			$this->countPrerollClicked = (int)$xml->countPrerollClicked;
		if(count($xml->countMidrollClicked))
			$this->countMidrollClicked = (int)$xml->countMidrollClicked;
		if(count($xml->countPostrollClicked))
			$this->countPostrollClicked = (int)$xml->countPostrollClicked;
		if(count($xml->countOverlayClicked))
			$this->countOverlayClicked = (int)$xml->countOverlayClicked;
		if(count($xml->countPreroll25))
			$this->countPreroll25 = (int)$xml->countPreroll25;
		if(count($xml->countPreroll50))
			$this->countPreroll50 = (int)$xml->countPreroll50;
		if(count($xml->countPreroll75))
			$this->countPreroll75 = (int)$xml->countPreroll75;
		if(count($xml->countMidroll25))
			$this->countMidroll25 = (int)$xml->countMidroll25;
		if(count($xml->countMidroll50))
			$this->countMidroll50 = (int)$xml->countMidroll50;
		if(count($xml->countMidroll75))
			$this->countMidroll75 = (int)$xml->countMidroll75;
		if(count($xml->countPostroll25))
			$this->countPostroll25 = (int)$xml->countPostroll25;
		if(count($xml->countPostroll50))
			$this->countPostroll50 = (int)$xml->countPostroll50;
		if(count($xml->countPostroll75))
			$this->countPostroll75 = (int)$xml->countPostroll75;
		$this->countLiveStreamingBandwidth = (string)$xml->countLiveStreamingBandwidth;
		$this->aggregatedLiveStreamingBandwidth = (string)$xml->aggregatedLiveStreamingBandwidth;
	}
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * Events aggregation time as Unix timestamp (In seconds) represent one hour
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $aggregatedTime = null;

	/**
	 * Summary of all entries play time (in seconds)
	 * 	 
	 *
	 * @var float
	 * @readonly
	 */
	public $sumTimeViewed = null;

	/**
	 * Average of all entries play time (in seconds)
	 * 	 
	 *
	 * @var float
	 * @readonly
	 */
	public $averageTimeViewed = null;

	/**
	 * Number of all played entries
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlays = null;

	/**
	 * Number of all loaded entry players
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countLoads = null;

	/**
	 * Number of plays that reached 25%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlays25 = null;

	/**
	 * Number of plays that reached 50%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlays50 = null;

	/**
	 * Number of plays that reached 75%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlays75 = null;

	/**
	 * Number of plays that reached 100%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlays100 = null;

	/**
	 * Number of times that editor opened
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countEdit = null;

	/**
	 * Number of times that share button clicked
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countShares = null;

	/**
	 * Number of times that download button clicked
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countDownload = null;

	/**
	 * Number of times that report abuse button clicked
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countReportAbuse = null;

	/**
	 * Count of new created media entries
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countMediaEntries = null;

	/**
	 * Count of new created video entries
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countVideoEntries = null;

	/**
	 * Count of new created image entries
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countImageEntries = null;

	/**
	 * Count of new created audio entries
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countAudioEntries = null;

	/**
	 * Count of new created mix entries
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countMixEntries = null;

	/**
	 * Count of new created playlists
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlaylists = null;

	/**
	 * Is bigint - in KB, aggregated daily in the first hour of every day
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $countBandwidth = null;

	/**
	 * Is bigint - in MB, aggregated daily in the first hour of every day
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $countStorage = null;

	/**
	 * Count of new created users
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countUsers = null;

	/**
	 * Count of new created widgets
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countWidgets = null;

	/**
	 * Is bigint - in MB, aggregated daily in the first hour of every day
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $aggregatedStorage = null;

	/**
	 * Is bigint - in KB, aggregated daily in the first hour of every day
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $aggregatedBandwidth = null;

	/**
	 * Count of times that player entered buffering state
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countBufferStart = null;

	/**
	 * Count of times that player left buffering state
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countBufferEnd = null;

	/**
	 * Count of times that player fullscreen state opened
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countOpenFullScreen = null;

	/**
	 * Count of times that player fullscreen state closed
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countCloseFullScreen = null;

	/**
	 * Count of times that replay button clicked
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countReplay = null;

	/**
	 * Count of times that seek option used
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countSeek = null;

	/**
	 * Count of times that upload dialog opened in the editor
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countOpenUpload = null;

	/**
	 * Count of times that save and publish button clicked in the editor
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countSavePublish = null;

	/**
	 * Count of times that the editor closed
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countCloseEditor = null;

	/**
	 * Count of times that pre-bumper entry played
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPreBumperPlayed = null;

	/**
	 * Count of times that post-bumper entry played
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostBumperPlayed = null;

	/**
	 * Count of times that bumper entry clicked
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countBumperClicked = null;

	/**
	 * Count of times that pre-roll ad started
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPrerollStarted = null;

	/**
	 * Count of times that mid-roll ad started
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countMidrollStarted = null;

	/**
	 * Count of times that post-roll ad started
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostrollStarted = null;

	/**
	 * Count of times that overlay ad started
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countOverlayStarted = null;

	/**
	 * Count of times that pre-roll ad clicked
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPrerollClicked = null;

	/**
	 * Count of times that mid-roll ad clicked
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countMidrollClicked = null;

	/**
	 * Count of times that post-roll ad clicked
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostrollClicked = null;

	/**
	 * Count of times that overlay ad clicked
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countOverlayClicked = null;

	/**
	 * Count of pre-roll ad plays that reached 25%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPreroll25 = null;

	/**
	 * Count of pre-roll ad plays that reached 50%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPreroll50 = null;

	/**
	 * Count of pre-roll ad plays that reached 75%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPreroll75 = null;

	/**
	 * Count of mid-roll ad plays that reached 25%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countMidroll25 = null;

	/**
	 * Count of mid-roll ad plays that reached 50%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countMidroll50 = null;

	/**
	 * Count of mid-roll ad plays that reached 75%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countMidroll75 = null;

	/**
	 * Count of post-roll ad plays that reached 25%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostroll25 = null;

	/**
	 * Count of post-roll ad plays that reached 50%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostroll50 = null;

	/**
	 * Count of post-roll ad plays that reached 75%
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostroll75 = null;

	/**
	 * Is bigint - in KB, aggregated daily in the first hour of every day
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $countLiveStreamingBandwidth = null;

	/**
	 * Is bigint - in MB, aggregated daily in the first hour of every day
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $aggregatedLiveStreamingBandwidth = null;


}

