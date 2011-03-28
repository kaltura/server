<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

class KalturaDwhHourlyPartnerOrderBy
{
	const AGGREGATED_TIME_ASC = "+aggregatedTime";
	const AGGREGATED_TIME_DESC = "-aggregatedTime";
	const SUM_TIME_VIEWED_ASC = "+sumTimeViewed";
	const SUM_TIME_VIEWED_DESC = "-sumTimeViewed";
	const AVERAGE_TIME_VIEWED_ASC = "+averageTimeViewed";
	const AVERAGE_TIME_VIEWED_DESC = "-averageTimeViewed";
	const COUNT_PLAYS_ASC = "+countPlays";
	const COUNT_PLAYS_DESC = "-countPlays";
	const COUNT_LOADS_ASC = "+countLoads";
	const COUNT_LOADS_DESC = "-countLoads";
	const COUNT_PLAYS25_ASC = "+countPlays25";
	const COUNT_PLAYS25_DESC = "-countPlays25";
	const COUNT_PLAYS50_ASC = "+countPlays50";
	const COUNT_PLAYS50_DESC = "-countPlays50";
	const COUNT_PLAYS75_ASC = "+countPlays75";
	const COUNT_PLAYS75_DESC = "-countPlays75";
	const COUNT_PLAYS100_ASC = "+countPlays100";
	const COUNT_PLAYS100_DESC = "-countPlays100";
	const COUNT_EDIT_ASC = "+countEdit";
	const COUNT_EDIT_DESC = "-countEdit";
	const COUNT_SHARES_ASC = "+countShares";
	const COUNT_SHARES_DESC = "-countShares";
	const COUNT_DOWNLOAD_ASC = "+countDownload";
	const COUNT_DOWNLOAD_DESC = "-countDownload";
	const COUNT_REPORT_ABUSE_ASC = "+countReportAbuse";
	const COUNT_REPORT_ABUSE_DESC = "-countReportAbuse";
	const COUNT_MEDIA_ENTRIES_ASC = "+countMediaEntries";
	const COUNT_MEDIA_ENTRIES_DESC = "-countMediaEntries";
	const COUNT_VIDEO_ENTRIES_ASC = "+countVideoEntries";
	const COUNT_VIDEO_ENTRIES_DESC = "-countVideoEntries";
	const COUNT_IMAGE_ENTRIES_ASC = "+countImageEntries";
	const COUNT_IMAGE_ENTRIES_DESC = "-countImageEntries";
	const COUNT_AUDIO_ENTRIES_ASC = "+countAudioEntries";
	const COUNT_AUDIO_ENTRIES_DESC = "-countAudioEntries";
	const COUNT_MIX_ENTRIES_ASC = "+countMixEntries";
	const COUNT_MIX_ENTRIES_DESC = "-countMixEntries";
	const COUNT_PLAYLISTS_ASC = "+countPlaylists";
	const COUNT_PLAYLISTS_DESC = "-countPlaylists";
	const COUNT_BANDWIDTH_ASC = "+countBandwidth";
	const COUNT_BANDWIDTH_DESC = "-countBandwidth";
	const COUNT_STORAGE_ASC = "+countStorage";
	const COUNT_STORAGE_DESC = "-countStorage";
	const COUNT_USERS_ASC = "+countUsers";
	const COUNT_USERS_DESC = "-countUsers";
	const COUNT_WIDGETS_ASC = "+countWidgets";
	const COUNT_WIDGETS_DESC = "-countWidgets";
	const AGGREGATED_STORAGE_ASC = "+aggregatedStorage";
	const AGGREGATED_STORAGE_DESC = "-aggregatedStorage";
	const AGGREGATED_BANDWIDTH_ASC = "+aggregatedBandwidth";
	const AGGREGATED_BANDWIDTH_DESC = "-aggregatedBandwidth";
	const COUNT_BUFFER_START_ASC = "+countBufferStart";
	const COUNT_BUFFER_START_DESC = "-countBufferStart";
	const COUNT_BUFFER_END_ASC = "+countBufferEnd";
	const COUNT_BUFFER_END_DESC = "-countBufferEnd";
	const COUNT_OPEN_FULL_SCREEN_ASC = "+countOpenFullScreen";
	const COUNT_OPEN_FULL_SCREEN_DESC = "-countOpenFullScreen";
	const COUNT_CLOSE_FULL_SCREEN_ASC = "+countCloseFullScreen";
	const COUNT_CLOSE_FULL_SCREEN_DESC = "-countCloseFullScreen";
	const COUNT_REPLAY_ASC = "+countReplay";
	const COUNT_REPLAY_DESC = "-countReplay";
	const COUNT_SEEK_ASC = "+countSeek";
	const COUNT_SEEK_DESC = "-countSeek";
	const COUNT_OPEN_UPLOAD_ASC = "+countOpenUpload";
	const COUNT_OPEN_UPLOAD_DESC = "-countOpenUpload";
	const COUNT_SAVE_PUBLISH_ASC = "+countSavePublish";
	const COUNT_SAVE_PUBLISH_DESC = "-countSavePublish";
	const COUNT_CLOSE_EDITOR_ASC = "+countCloseEditor";
	const COUNT_CLOSE_EDITOR_DESC = "-countCloseEditor";
	const COUNT_PRE_BUMPER_PLAYED_ASC = "+countPreBumperPlayed";
	const COUNT_PRE_BUMPER_PLAYED_DESC = "-countPreBumperPlayed";
	const COUNT_POST_BUMPER_PLAYED_ASC = "+countPostBumperPlayed";
	const COUNT_POST_BUMPER_PLAYED_DESC = "-countPostBumperPlayed";
	const COUNT_BUMPER_CLICKED_ASC = "+countBumperClicked";
	const COUNT_BUMPER_CLICKED_DESC = "-countBumperClicked";
	const COUNT_PREROLL_STARTED_ASC = "+countPrerollStarted";
	const COUNT_PREROLL_STARTED_DESC = "-countPrerollStarted";
	const COUNT_MIDROLL_STARTED_ASC = "+countMidrollStarted";
	const COUNT_MIDROLL_STARTED_DESC = "-countMidrollStarted";
	const COUNT_POSTROLL_STARTED_ASC = "+countPostrollStarted";
	const COUNT_POSTROLL_STARTED_DESC = "-countPostrollStarted";
	const COUNT_OVERLAY_STARTED_ASC = "+countOverlayStarted";
	const COUNT_OVERLAY_STARTED_DESC = "-countOverlayStarted";
	const COUNT_PREROLL_CLICKED_ASC = "+countPrerollClicked";
	const COUNT_PREROLL_CLICKED_DESC = "-countPrerollClicked";
	const COUNT_MIDROLL_CLICKED_ASC = "+countMidrollClicked";
	const COUNT_MIDROLL_CLICKED_DESC = "-countMidrollClicked";
	const COUNT_POSTROLL_CLICKED_ASC = "+countPostrollClicked";
	const COUNT_POSTROLL_CLICKED_DESC = "-countPostrollClicked";
	const COUNT_OVERLAY_CLICKED_ASC = "+countOverlayClicked";
	const COUNT_OVERLAY_CLICKED_DESC = "-countOverlayClicked";
	const COUNT_PREROLL25_ASC = "+countPreroll25";
	const COUNT_PREROLL25_DESC = "-countPreroll25";
	const COUNT_PREROLL50_ASC = "+countPreroll50";
	const COUNT_PREROLL50_DESC = "-countPreroll50";
	const COUNT_PREROLL75_ASC = "+countPreroll75";
	const COUNT_PREROLL75_DESC = "-countPreroll75";
	const COUNT_MIDROLL25_ASC = "+countMidroll25";
	const COUNT_MIDROLL25_DESC = "-countMidroll25";
	const COUNT_MIDROLL50_ASC = "+countMidroll50";
	const COUNT_MIDROLL50_DESC = "-countMidroll50";
	const COUNT_MIDROLL75_ASC = "+countMidroll75";
	const COUNT_MIDROLL75_DESC = "-countMidroll75";
	const COUNT_POSTROLL25_ASC = "+countPostroll25";
	const COUNT_POSTROLL25_DESC = "-countPostroll25";
	const COUNT_POSTROLL50_ASC = "+countPostroll50";
	const COUNT_POSTROLL50_DESC = "-countPostroll50";
	const COUNT_POSTROLL75_ASC = "+countPostroll75";
	const COUNT_POSTROLL75_DESC = "-countPostroll75";
	const COUNT_LIVE_STREAMING_BANDWIDTH_ASC = "+countLiveStreamingBandwidth";
	const COUNT_LIVE_STREAMING_BANDWIDTH_DESC = "-countLiveStreamingBandwidth";
	const AGGREGATED_LIVE_STREAMING_BANDWIDTH_ASC = "+aggregatedLiveStreamingBandwidth";
	const AGGREGATED_LIVE_STREAMING_BANDWIDTH_DESC = "-aggregatedLiveStreamingBandwidth";
}

abstract class KalturaDwhHourlyPartnerBaseFilter extends KalturaFilter
{
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

class KalturaDwhHourlyPartnerFilter extends KalturaDwhHourlyPartnerBaseFilter
{

}

class KalturaDwhHourlyPartner extends KalturaObjectBase
{
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
	 * @var int
	 * @readonly
	 */
	public $aggregatedTime = null;

	/**
	 * Summary of all entries play time (in seconds)
	 *
	 * @var float
	 * @readonly
	 */
	public $sumTimeViewed = null;

	/**
	 * Average of all entries play time (in seconds)
	 *
	 * @var float
	 * @readonly
	 */
	public $averageTimeViewed = null;

	/**
	 * Number of all played entries
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlays = null;

	/**
	 * Number of all loaded entry players
	 *
	 * @var int
	 * @readonly
	 */
	public $countLoads = null;

	/**
	 * Number of plays that reached 25%
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlays25 = null;

	/**
	 * Number of plays that reached 50%
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlays50 = null;

	/**
	 * Number of plays that reached 75%
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlays75 = null;

	/**
	 * Number of plays that reached 100%
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlays100 = null;

	/**
	 * Number of times that editor opened
	 *
	 * @var int
	 * @readonly
	 */
	public $countEdit = null;

	/**
	 * Number of times that share button clicked
	 *
	 * @var int
	 * @readonly
	 */
	public $countShares = null;

	/**
	 * Number of times that download button clicked
	 *
	 * @var int
	 * @readonly
	 */
	public $countDownload = null;

	/**
	 * Number of times that report abuse button clicked
	 *
	 * @var int
	 * @readonly
	 */
	public $countReportAbuse = null;

	/**
	 * Count of new created media entries
	 *
	 * @var int
	 * @readonly
	 */
	public $countMediaEntries = null;

	/**
	 * Count of new created video entries
	 *
	 * @var int
	 * @readonly
	 */
	public $countVideoEntries = null;

	/**
	 * Count of new created image entries
	 *
	 * @var int
	 * @readonly
	 */
	public $countImageEntries = null;

	/**
	 * Count of new created audio entries
	 *
	 * @var int
	 * @readonly
	 */
	public $countAudioEntries = null;

	/**
	 * Count of new created mix entries
	 *
	 * @var int
	 * @readonly
	 */
	public $countMixEntries = null;

	/**
	 * Count of new created playlists
	 *
	 * @var int
	 * @readonly
	 */
	public $countPlaylists = null;

	/**
	 * Is bigint - in KB, aggregated daily in the first hour of every day
	 *
	 * @var string
	 * @readonly
	 */
	public $countBandwidth = null;

	/**
	 * Is bigint - in MB, aggregated daily in the first hour of every day
	 *
	 * @var string
	 * @readonly
	 */
	public $countStorage = null;

	/**
	 * Count of new created users
	 *
	 * @var int
	 * @readonly
	 */
	public $countUsers = null;

	/**
	 * Count of new created widgets
	 *
	 * @var int
	 * @readonly
	 */
	public $countWidgets = null;

	/**
	 * Is bigint - in MB, aggregated daily in the first hour of every day
	 *
	 * @var string
	 * @readonly
	 */
	public $aggregatedStorage = null;

	/**
	 * Is bigint - in KB, aggregated daily in the first hour of every day
	 *
	 * @var string
	 * @readonly
	 */
	public $aggregatedBandwidth = null;

	/**
	 * Count of times that player entered buffering state
	 *
	 * @var int
	 * @readonly
	 */
	public $countBufferStart = null;

	/**
	 * Count of times that player left buffering state
	 *
	 * @var int
	 * @readonly
	 */
	public $countBufferEnd = null;

	/**
	 * Count of times that player fullscreen state opened
	 *
	 * @var int
	 * @readonly
	 */
	public $countOpenFullScreen = null;

	/**
	 * Count of times that player fullscreen state closed
	 *
	 * @var int
	 * @readonly
	 */
	public $countCloseFullScreen = null;

	/**
	 * Count of times that replay button clicked
	 *
	 * @var int
	 * @readonly
	 */
	public $countReplay = null;

	/**
	 * Count of times that seek option used
	 *
	 * @var int
	 * @readonly
	 */
	public $countSeek = null;

	/**
	 * Count of times that upload dialog opened in the editor
	 *
	 * @var int
	 * @readonly
	 */
	public $countOpenUpload = null;

	/**
	 * Count of times that save and publish button clicked in the editor
	 *
	 * @var int
	 * @readonly
	 */
	public $countSavePublish = null;

	/**
	 * Count of times that the editor closed
	 *
	 * @var int
	 * @readonly
	 */
	public $countCloseEditor = null;

	/**
	 * Count of times that pre-bumper entry played
	 *
	 * @var int
	 * @readonly
	 */
	public $countPreBumperPlayed = null;

	/**
	 * Count of times that post-bumper entry played
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostBumperPlayed = null;

	/**
	 * Count of times that bumper entry clicked
	 *
	 * @var int
	 * @readonly
	 */
	public $countBumperClicked = null;

	/**
	 * Count of times that pre-roll ad started
	 *
	 * @var int
	 * @readonly
	 */
	public $countPrerollStarted = null;

	/**
	 * Count of times that mid-roll ad started
	 *
	 * @var int
	 * @readonly
	 */
	public $countMidrollStarted = null;

	/**
	 * Count of times that post-roll ad started
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostrollStarted = null;

	/**
	 * Count of times that overlay ad started
	 *
	 * @var int
	 * @readonly
	 */
	public $countOverlayStarted = null;

	/**
	 * Count of times that pre-roll ad clicked
	 *
	 * @var int
	 * @readonly
	 */
	public $countPrerollClicked = null;

	/**
	 * Count of times that mid-roll ad clicked
	 *
	 * @var int
	 * @readonly
	 */
	public $countMidrollClicked = null;

	/**
	 * Count of times that post-roll ad clicked
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostrollClicked = null;

	/**
	 * Count of times that overlay ad clicked
	 *
	 * @var int
	 * @readonly
	 */
	public $countOverlayClicked = null;

	/**
	 * Count of pre-roll ad plays that reached 25%
	 *
	 * @var int
	 * @readonly
	 */
	public $countPreroll25 = null;

	/**
	 * Count of pre-roll ad plays that reached 50%
	 *
	 * @var int
	 * @readonly
	 */
	public $countPreroll50 = null;

	/**
	 * Count of pre-roll ad plays that reached 75%
	 *
	 * @var int
	 * @readonly
	 */
	public $countPreroll75 = null;

	/**
	 * Count of mid-roll ad plays that reached 25%
	 *
	 * @var int
	 * @readonly
	 */
	public $countMidroll25 = null;

	/**
	 * Count of mid-roll ad plays that reached 50%
	 *
	 * @var int
	 * @readonly
	 */
	public $countMidroll50 = null;

	/**
	 * Count of mid-roll ad plays that reached 75%
	 *
	 * @var int
	 * @readonly
	 */
	public $countMidroll75 = null;

	/**
	 * Count of post-roll ad plays that reached 25%
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostroll25 = null;

	/**
	 * Count of post-roll ad plays that reached 50%
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostroll50 = null;

	/**
	 * Count of post-roll ad plays that reached 75%
	 *
	 * @var int
	 * @readonly
	 */
	public $countPostroll75 = null;

	/**
	 * Is bigint - in KB, aggregated daily in the first hour of every day
	 *
	 * @var string
	 * @readonly
	 */
	public $countLiveStreamingBandwidth = null;

	/**
	 * Is bigint - in MB, aggregated daily in the first hour of every day
	 *
	 * @var string
	 * @readonly
	 */
	public $aggregatedLiveStreamingBandwidth = null;


}

class KalturaDwhHourlyPartnerListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaDwhHourlyPartner
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}


class KalturaPartnerAggregationService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function listAction(KalturaDwhHourlyPartnerFilter $filter, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("partneraggregation_partneraggregation", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDwhHourlyPartnerListResponse");
		return $resultObject;
	}
}
class KalturaPartnerAggregationClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaPartnerAggregationClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaPartnerAggregationService
	 */
	public $partnerAggregation = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->partnerAggregation = new KalturaPartnerAggregationService($client);
	}

	/**
	 * @return KalturaPartnerAggregationClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaPartnerAggregationClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'partnerAggregation' => $this->partnerAggregation,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'partnerAggregation';
	}
}

