<?php
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

