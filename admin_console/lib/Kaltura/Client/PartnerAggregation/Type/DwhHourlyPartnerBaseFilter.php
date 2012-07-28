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
abstract class Kaltura_Client_PartnerAggregation_Type_DwhHourlyPartnerBaseFilter extends Kaltura_Client_Type_Filter
{
	public function getKalturaObjectType()
	{
		return 'KalturaDwhHourlyPartnerBaseFilter';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->partnerIdEqual))
			$this->partnerIdEqual = (int)$xml->partnerIdEqual;
		if(count($xml->aggregatedTimeLessThanOrEqual))
			$this->aggregatedTimeLessThanOrEqual = (int)$xml->aggregatedTimeLessThanOrEqual;
		if(count($xml->aggregatedTimeGreaterThanOrEqual))
			$this->aggregatedTimeGreaterThanOrEqual = (int)$xml->aggregatedTimeGreaterThanOrEqual;
		if(count($xml->sumTimeViewedLessThanOrEqual))
			$this->sumTimeViewedLessThanOrEqual = (float)$xml->sumTimeViewedLessThanOrEqual;
		if(count($xml->sumTimeViewedGreaterThanOrEqual))
			$this->sumTimeViewedGreaterThanOrEqual = (float)$xml->sumTimeViewedGreaterThanOrEqual;
		if(count($xml->averageTimeViewedLessThanOrEqual))
			$this->averageTimeViewedLessThanOrEqual = (float)$xml->averageTimeViewedLessThanOrEqual;
		if(count($xml->averageTimeViewedGreaterThanOrEqual))
			$this->averageTimeViewedGreaterThanOrEqual = (float)$xml->averageTimeViewedGreaterThanOrEqual;
		if(count($xml->countPlaysLessThanOrEqual))
			$this->countPlaysLessThanOrEqual = (int)$xml->countPlaysLessThanOrEqual;
		if(count($xml->countPlaysGreaterThanOrEqual))
			$this->countPlaysGreaterThanOrEqual = (int)$xml->countPlaysGreaterThanOrEqual;
		if(count($xml->countLoadsLessThanOrEqual))
			$this->countLoadsLessThanOrEqual = (int)$xml->countLoadsLessThanOrEqual;
		if(count($xml->countLoadsGreaterThanOrEqual))
			$this->countLoadsGreaterThanOrEqual = (int)$xml->countLoadsGreaterThanOrEqual;
		if(count($xml->countPlays25LessThanOrEqual))
			$this->countPlays25LessThanOrEqual = (int)$xml->countPlays25LessThanOrEqual;
		if(count($xml->countPlays25GreaterThanOrEqual))
			$this->countPlays25GreaterThanOrEqual = (int)$xml->countPlays25GreaterThanOrEqual;
		if(count($xml->countPlays50LessThanOrEqual))
			$this->countPlays50LessThanOrEqual = (int)$xml->countPlays50LessThanOrEqual;
		if(count($xml->countPlays50GreaterThanOrEqual))
			$this->countPlays50GreaterThanOrEqual = (int)$xml->countPlays50GreaterThanOrEqual;
		if(count($xml->countPlays75LessThanOrEqual))
			$this->countPlays75LessThanOrEqual = (int)$xml->countPlays75LessThanOrEqual;
		if(count($xml->countPlays75GreaterThanOrEqual))
			$this->countPlays75GreaterThanOrEqual = (int)$xml->countPlays75GreaterThanOrEqual;
		if(count($xml->countPlays100LessThanOrEqual))
			$this->countPlays100LessThanOrEqual = (int)$xml->countPlays100LessThanOrEqual;
		if(count($xml->countPlays100GreaterThanOrEqual))
			$this->countPlays100GreaterThanOrEqual = (int)$xml->countPlays100GreaterThanOrEqual;
		if(count($xml->countEditLessThanOrEqual))
			$this->countEditLessThanOrEqual = (int)$xml->countEditLessThanOrEqual;
		if(count($xml->countEditGreaterThanOrEqual))
			$this->countEditGreaterThanOrEqual = (int)$xml->countEditGreaterThanOrEqual;
		if(count($xml->countSharesLessThanOrEqual))
			$this->countSharesLessThanOrEqual = (int)$xml->countSharesLessThanOrEqual;
		if(count($xml->countSharesGreaterThanOrEqual))
			$this->countSharesGreaterThanOrEqual = (int)$xml->countSharesGreaterThanOrEqual;
		if(count($xml->countDownloadLessThanOrEqual))
			$this->countDownloadLessThanOrEqual = (int)$xml->countDownloadLessThanOrEqual;
		if(count($xml->countDownloadGreaterThanOrEqual))
			$this->countDownloadGreaterThanOrEqual = (int)$xml->countDownloadGreaterThanOrEqual;
		if(count($xml->countReportAbuseLessThanOrEqual))
			$this->countReportAbuseLessThanOrEqual = (int)$xml->countReportAbuseLessThanOrEqual;
		if(count($xml->countReportAbuseGreaterThanOrEqual))
			$this->countReportAbuseGreaterThanOrEqual = (int)$xml->countReportAbuseGreaterThanOrEqual;
		if(count($xml->countMediaEntriesLessThanOrEqual))
			$this->countMediaEntriesLessThanOrEqual = (int)$xml->countMediaEntriesLessThanOrEqual;
		if(count($xml->countMediaEntriesGreaterThanOrEqual))
			$this->countMediaEntriesGreaterThanOrEqual = (int)$xml->countMediaEntriesGreaterThanOrEqual;
		if(count($xml->countVideoEntriesLessThanOrEqual))
			$this->countVideoEntriesLessThanOrEqual = (int)$xml->countVideoEntriesLessThanOrEqual;
		if(count($xml->countVideoEntriesGreaterThanOrEqual))
			$this->countVideoEntriesGreaterThanOrEqual = (int)$xml->countVideoEntriesGreaterThanOrEqual;
		if(count($xml->countImageEntriesLessThanOrEqual))
			$this->countImageEntriesLessThanOrEqual = (int)$xml->countImageEntriesLessThanOrEqual;
		if(count($xml->countImageEntriesGreaterThanOrEqual))
			$this->countImageEntriesGreaterThanOrEqual = (int)$xml->countImageEntriesGreaterThanOrEqual;
		if(count($xml->countAudioEntriesLessThanOrEqual))
			$this->countAudioEntriesLessThanOrEqual = (int)$xml->countAudioEntriesLessThanOrEqual;
		if(count($xml->countAudioEntriesGreaterThanOrEqual))
			$this->countAudioEntriesGreaterThanOrEqual = (int)$xml->countAudioEntriesGreaterThanOrEqual;
		if(count($xml->countMixEntriesLessThanOrEqual))
			$this->countMixEntriesLessThanOrEqual = (int)$xml->countMixEntriesLessThanOrEqual;
		if(count($xml->countMixEntriesGreaterThanOrEqual))
			$this->countMixEntriesGreaterThanOrEqual = (int)$xml->countMixEntriesGreaterThanOrEqual;
		if(count($xml->countPlaylistsLessThanOrEqual))
			$this->countPlaylistsLessThanOrEqual = (int)$xml->countPlaylistsLessThanOrEqual;
		if(count($xml->countPlaylistsGreaterThanOrEqual))
			$this->countPlaylistsGreaterThanOrEqual = (int)$xml->countPlaylistsGreaterThanOrEqual;
		$this->countBandwidthLessThanOrEqual = (string)$xml->countBandwidthLessThanOrEqual;
		$this->countBandwidthGreaterThanOrEqual = (string)$xml->countBandwidthGreaterThanOrEqual;
		$this->countStorageLessThanOrEqual = (string)$xml->countStorageLessThanOrEqual;
		$this->countStorageGreaterThanOrEqual = (string)$xml->countStorageGreaterThanOrEqual;
		if(count($xml->countUsersLessThanOrEqual))
			$this->countUsersLessThanOrEqual = (int)$xml->countUsersLessThanOrEqual;
		if(count($xml->countUsersGreaterThanOrEqual))
			$this->countUsersGreaterThanOrEqual = (int)$xml->countUsersGreaterThanOrEqual;
		if(count($xml->countWidgetsLessThanOrEqual))
			$this->countWidgetsLessThanOrEqual = (int)$xml->countWidgetsLessThanOrEqual;
		if(count($xml->countWidgetsGreaterThanOrEqual))
			$this->countWidgetsGreaterThanOrEqual = (int)$xml->countWidgetsGreaterThanOrEqual;
		$this->aggregatedStorageLessThanOrEqual = (string)$xml->aggregatedStorageLessThanOrEqual;
		$this->aggregatedStorageGreaterThanOrEqual = (string)$xml->aggregatedStorageGreaterThanOrEqual;
		$this->aggregatedBandwidthLessThanOrEqual = (string)$xml->aggregatedBandwidthLessThanOrEqual;
		$this->aggregatedBandwidthGreaterThanOrEqual = (string)$xml->aggregatedBandwidthGreaterThanOrEqual;
		if(count($xml->countBufferStartLessThanOrEqual))
			$this->countBufferStartLessThanOrEqual = (int)$xml->countBufferStartLessThanOrEqual;
		if(count($xml->countBufferStartGreaterThanOrEqual))
			$this->countBufferStartGreaterThanOrEqual = (int)$xml->countBufferStartGreaterThanOrEqual;
		if(count($xml->countBufferEndLessThanOrEqual))
			$this->countBufferEndLessThanOrEqual = (int)$xml->countBufferEndLessThanOrEqual;
		if(count($xml->countBufferEndGreaterThanOrEqual))
			$this->countBufferEndGreaterThanOrEqual = (int)$xml->countBufferEndGreaterThanOrEqual;
		if(count($xml->countOpenFullScreenLessThanOrEqual))
			$this->countOpenFullScreenLessThanOrEqual = (int)$xml->countOpenFullScreenLessThanOrEqual;
		if(count($xml->countOpenFullScreenGreaterThanOrEqual))
			$this->countOpenFullScreenGreaterThanOrEqual = (int)$xml->countOpenFullScreenGreaterThanOrEqual;
		if(count($xml->countCloseFullScreenLessThanOrEqual))
			$this->countCloseFullScreenLessThanOrEqual = (int)$xml->countCloseFullScreenLessThanOrEqual;
		if(count($xml->countCloseFullScreenGreaterThanOrEqual))
			$this->countCloseFullScreenGreaterThanOrEqual = (int)$xml->countCloseFullScreenGreaterThanOrEqual;
		if(count($xml->countReplayLessThanOrEqual))
			$this->countReplayLessThanOrEqual = (int)$xml->countReplayLessThanOrEqual;
		if(count($xml->countReplayGreaterThanOrEqual))
			$this->countReplayGreaterThanOrEqual = (int)$xml->countReplayGreaterThanOrEqual;
		if(count($xml->countSeekLessThanOrEqual))
			$this->countSeekLessThanOrEqual = (int)$xml->countSeekLessThanOrEqual;
		if(count($xml->countSeekGreaterThanOrEqual))
			$this->countSeekGreaterThanOrEqual = (int)$xml->countSeekGreaterThanOrEqual;
		if(count($xml->countOpenUploadLessThanOrEqual))
			$this->countOpenUploadLessThanOrEqual = (int)$xml->countOpenUploadLessThanOrEqual;
		if(count($xml->countOpenUploadGreaterThanOrEqual))
			$this->countOpenUploadGreaterThanOrEqual = (int)$xml->countOpenUploadGreaterThanOrEqual;
		if(count($xml->countSavePublishLessThanOrEqual))
			$this->countSavePublishLessThanOrEqual = (int)$xml->countSavePublishLessThanOrEqual;
		if(count($xml->countSavePublishGreaterThanOrEqual))
			$this->countSavePublishGreaterThanOrEqual = (int)$xml->countSavePublishGreaterThanOrEqual;
		if(count($xml->countCloseEditorLessThanOrEqual))
			$this->countCloseEditorLessThanOrEqual = (int)$xml->countCloseEditorLessThanOrEqual;
		if(count($xml->countCloseEditorGreaterThanOrEqual))
			$this->countCloseEditorGreaterThanOrEqual = (int)$xml->countCloseEditorGreaterThanOrEqual;
		if(count($xml->countPreBumperPlayedLessThanOrEqual))
			$this->countPreBumperPlayedLessThanOrEqual = (int)$xml->countPreBumperPlayedLessThanOrEqual;
		if(count($xml->countPreBumperPlayedGreaterThanOrEqual))
			$this->countPreBumperPlayedGreaterThanOrEqual = (int)$xml->countPreBumperPlayedGreaterThanOrEqual;
		if(count($xml->countPostBumperPlayedLessThanOrEqual))
			$this->countPostBumperPlayedLessThanOrEqual = (int)$xml->countPostBumperPlayedLessThanOrEqual;
		if(count($xml->countPostBumperPlayedGreaterThanOrEqual))
			$this->countPostBumperPlayedGreaterThanOrEqual = (int)$xml->countPostBumperPlayedGreaterThanOrEqual;
		if(count($xml->countBumperClickedLessThanOrEqual))
			$this->countBumperClickedLessThanOrEqual = (int)$xml->countBumperClickedLessThanOrEqual;
		if(count($xml->countBumperClickedGreaterThanOrEqual))
			$this->countBumperClickedGreaterThanOrEqual = (int)$xml->countBumperClickedGreaterThanOrEqual;
		if(count($xml->countPrerollStartedLessThanOrEqual))
			$this->countPrerollStartedLessThanOrEqual = (int)$xml->countPrerollStartedLessThanOrEqual;
		if(count($xml->countPrerollStartedGreaterThanOrEqual))
			$this->countPrerollStartedGreaterThanOrEqual = (int)$xml->countPrerollStartedGreaterThanOrEqual;
		if(count($xml->countMidrollStartedLessThanOrEqual))
			$this->countMidrollStartedLessThanOrEqual = (int)$xml->countMidrollStartedLessThanOrEqual;
		if(count($xml->countMidrollStartedGreaterThanOrEqual))
			$this->countMidrollStartedGreaterThanOrEqual = (int)$xml->countMidrollStartedGreaterThanOrEqual;
		if(count($xml->countPostrollStartedLessThanOrEqual))
			$this->countPostrollStartedLessThanOrEqual = (int)$xml->countPostrollStartedLessThanOrEqual;
		if(count($xml->countPostrollStartedGreaterThanOrEqual))
			$this->countPostrollStartedGreaterThanOrEqual = (int)$xml->countPostrollStartedGreaterThanOrEqual;
		if(count($xml->countOverlayStartedLessThanOrEqual))
			$this->countOverlayStartedLessThanOrEqual = (int)$xml->countOverlayStartedLessThanOrEqual;
		if(count($xml->countOverlayStartedGreaterThanOrEqual))
			$this->countOverlayStartedGreaterThanOrEqual = (int)$xml->countOverlayStartedGreaterThanOrEqual;
		if(count($xml->countPrerollClickedLessThanOrEqual))
			$this->countPrerollClickedLessThanOrEqual = (int)$xml->countPrerollClickedLessThanOrEqual;
		if(count($xml->countPrerollClickedGreaterThanOrEqual))
			$this->countPrerollClickedGreaterThanOrEqual = (int)$xml->countPrerollClickedGreaterThanOrEqual;
		if(count($xml->countMidrollClickedLessThanOrEqual))
			$this->countMidrollClickedLessThanOrEqual = (int)$xml->countMidrollClickedLessThanOrEqual;
		if(count($xml->countMidrollClickedGreaterThanOrEqual))
			$this->countMidrollClickedGreaterThanOrEqual = (int)$xml->countMidrollClickedGreaterThanOrEqual;
		if(count($xml->countPostrollClickedLessThanOrEqual))
			$this->countPostrollClickedLessThanOrEqual = (int)$xml->countPostrollClickedLessThanOrEqual;
		if(count($xml->countPostrollClickedGreaterThanOrEqual))
			$this->countPostrollClickedGreaterThanOrEqual = (int)$xml->countPostrollClickedGreaterThanOrEqual;
		if(count($xml->countOverlayClickedLessThanOrEqual))
			$this->countOverlayClickedLessThanOrEqual = (int)$xml->countOverlayClickedLessThanOrEqual;
		if(count($xml->countOverlayClickedGreaterThanOrEqual))
			$this->countOverlayClickedGreaterThanOrEqual = (int)$xml->countOverlayClickedGreaterThanOrEqual;
		if(count($xml->countPreroll25LessThanOrEqual))
			$this->countPreroll25LessThanOrEqual = (int)$xml->countPreroll25LessThanOrEqual;
		if(count($xml->countPreroll25GreaterThanOrEqual))
			$this->countPreroll25GreaterThanOrEqual = (int)$xml->countPreroll25GreaterThanOrEqual;
		if(count($xml->countPreroll50LessThanOrEqual))
			$this->countPreroll50LessThanOrEqual = (int)$xml->countPreroll50LessThanOrEqual;
		if(count($xml->countPreroll50GreaterThanOrEqual))
			$this->countPreroll50GreaterThanOrEqual = (int)$xml->countPreroll50GreaterThanOrEqual;
		if(count($xml->countPreroll75LessThanOrEqual))
			$this->countPreroll75LessThanOrEqual = (int)$xml->countPreroll75LessThanOrEqual;
		if(count($xml->countPreroll75GreaterThanOrEqual))
			$this->countPreroll75GreaterThanOrEqual = (int)$xml->countPreroll75GreaterThanOrEqual;
		if(count($xml->countMidroll25LessThanOrEqual))
			$this->countMidroll25LessThanOrEqual = (int)$xml->countMidroll25LessThanOrEqual;
		if(count($xml->countMidroll25GreaterThanOrEqual))
			$this->countMidroll25GreaterThanOrEqual = (int)$xml->countMidroll25GreaterThanOrEqual;
		if(count($xml->countMidroll50LessThanOrEqual))
			$this->countMidroll50LessThanOrEqual = (int)$xml->countMidroll50LessThanOrEqual;
		if(count($xml->countMidroll50GreaterThanOrEqual))
			$this->countMidroll50GreaterThanOrEqual = (int)$xml->countMidroll50GreaterThanOrEqual;
		if(count($xml->countMidroll75LessThanOrEqual))
			$this->countMidroll75LessThanOrEqual = (int)$xml->countMidroll75LessThanOrEqual;
		if(count($xml->countMidroll75GreaterThanOrEqual))
			$this->countMidroll75GreaterThanOrEqual = (int)$xml->countMidroll75GreaterThanOrEqual;
		if(count($xml->countPostroll25LessThanOrEqual))
			$this->countPostroll25LessThanOrEqual = (int)$xml->countPostroll25LessThanOrEqual;
		if(count($xml->countPostroll25GreaterThanOrEqual))
			$this->countPostroll25GreaterThanOrEqual = (int)$xml->countPostroll25GreaterThanOrEqual;
		if(count($xml->countPostroll50LessThanOrEqual))
			$this->countPostroll50LessThanOrEqual = (int)$xml->countPostroll50LessThanOrEqual;
		if(count($xml->countPostroll50GreaterThanOrEqual))
			$this->countPostroll50GreaterThanOrEqual = (int)$xml->countPostroll50GreaterThanOrEqual;
		if(count($xml->countPostroll75LessThanOrEqual))
			$this->countPostroll75LessThanOrEqual = (int)$xml->countPostroll75LessThanOrEqual;
		if(count($xml->countPostroll75GreaterThanOrEqual))
			$this->countPostroll75GreaterThanOrEqual = (int)$xml->countPostroll75GreaterThanOrEqual;
		$this->countLiveStreamingBandwidthLessThanOrEqual = (string)$xml->countLiveStreamingBandwidthLessThanOrEqual;
		$this->countLiveStreamingBandwidthGreaterThanOrEqual = (string)$xml->countLiveStreamingBandwidthGreaterThanOrEqual;
		$this->aggregatedLiveStreamingBandwidthLessThanOrEqual = (string)$xml->aggregatedLiveStreamingBandwidthLessThanOrEqual;
		$this->aggregatedLiveStreamingBandwidthGreaterThanOrEqual = (string)$xml->aggregatedLiveStreamingBandwidthGreaterThanOrEqual;
	}
	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $aggregatedTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $aggregatedTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $sumTimeViewedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $sumTimeViewedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $averageTimeViewedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $averageTimeViewedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlaysLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlaysGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countLoadsLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countLoadsGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlays25LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlays25GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlays50LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlays50GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlays75LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlays75GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlays100LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlays100GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countEditLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countEditGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countSharesLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countSharesGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countDownloadLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countDownloadGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countReportAbuseLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countReportAbuseGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMediaEntriesLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMediaEntriesGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countVideoEntriesLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countVideoEntriesGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countImageEntriesLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countImageEntriesGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countAudioEntriesLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countAudioEntriesGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMixEntriesLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMixEntriesGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlaylistsLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPlaylistsGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $countBandwidthLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $countBandwidthGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $countStorageLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $countStorageGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countUsersLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countUsersGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countWidgetsLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countWidgetsGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $aggregatedStorageLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $aggregatedStorageGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $aggregatedBandwidthLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $aggregatedBandwidthGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countBufferStartLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countBufferStartGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countBufferEndLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countBufferEndGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countOpenFullScreenLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countOpenFullScreenGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countCloseFullScreenLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countCloseFullScreenGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countReplayLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countReplayGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countSeekLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countSeekGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countOpenUploadLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countOpenUploadGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countSavePublishLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countSavePublishGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countCloseEditorLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countCloseEditorGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPreBumperPlayedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPreBumperPlayedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostBumperPlayedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostBumperPlayedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countBumperClickedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countBumperClickedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPrerollStartedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPrerollStartedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMidrollStartedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMidrollStartedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostrollStartedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostrollStartedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countOverlayStartedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countOverlayStartedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPrerollClickedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPrerollClickedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMidrollClickedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMidrollClickedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostrollClickedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostrollClickedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countOverlayClickedLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countOverlayClickedGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPreroll25LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPreroll25GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPreroll50LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPreroll50GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPreroll75LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPreroll75GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMidroll25LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMidroll25GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMidroll50LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMidroll50GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMidroll75LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countMidroll75GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostroll25LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostroll25GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostroll50LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostroll50GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostroll75LessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $countPostroll75GreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $countLiveStreamingBandwidthLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $countLiveStreamingBandwidthGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $aggregatedLiveStreamingBandwidthLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $aggregatedLiveStreamingBandwidthGreaterThanOrEqual = null;


}

