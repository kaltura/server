<?php
/**
 * @package plugins.partnerAggregation
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaDwhHourlyPartnerBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"partnerIdEqual" => "_eq_partner_id",
		"aggregatedTimeLessThanOrEqual" => "_lte_aggregated_time",
		"aggregatedTimeGreaterThanOrEqual" => "_gte_aggregated_time",
		"sumTimeViewedLessThanOrEqual" => "_lte_sum_time_viewed",
		"sumTimeViewedGreaterThanOrEqual" => "_gte_sum_time_viewed",
		"averageTimeViewedLessThanOrEqual" => "_lte_average_time_viewed",
		"averageTimeViewedGreaterThanOrEqual" => "_gte_average_time_viewed",
		"countPlaysLessThanOrEqual" => "_lte_count_plays",
		"countPlaysGreaterThanOrEqual" => "_gte_count_plays",
		"countLoadsLessThanOrEqual" => "_lte_count_loads",
		"countLoadsGreaterThanOrEqual" => "_gte_count_loads",
		"countPlays25LessThanOrEqual" => "_lte_count_plays25",
		"countPlays25GreaterThanOrEqual" => "_gte_count_plays25",
		"countPlays50LessThanOrEqual" => "_lte_count_plays50",
		"countPlays50GreaterThanOrEqual" => "_gte_count_plays50",
		"countPlays75LessThanOrEqual" => "_lte_count_plays75",
		"countPlays75GreaterThanOrEqual" => "_gte_count_plays75",
		"countPlays100LessThanOrEqual" => "_lte_count_plays100",
		"countPlays100GreaterThanOrEqual" => "_gte_count_plays100",
		"countEditLessThanOrEqual" => "_lte_count_edit",
		"countEditGreaterThanOrEqual" => "_gte_count_edit",
		"countSharesLessThanOrEqual" => "_lte_count_shares",
		"countSharesGreaterThanOrEqual" => "_gte_count_shares",
		"countDownloadLessThanOrEqual" => "_lte_count_download",
		"countDownloadGreaterThanOrEqual" => "_gte_count_download",
		"countReportAbuseLessThanOrEqual" => "_lte_count_report_abuse",
		"countReportAbuseGreaterThanOrEqual" => "_gte_count_report_abuse",
		"countMediaEntriesLessThanOrEqual" => "_lte_count_media_entries",
		"countMediaEntriesGreaterThanOrEqual" => "_gte_count_media_entries",
		"countVideoEntriesLessThanOrEqual" => "_lte_count_video_entries",
		"countVideoEntriesGreaterThanOrEqual" => "_gte_count_video_entries",
		"countImageEntriesLessThanOrEqual" => "_lte_count_image_entries",
		"countImageEntriesGreaterThanOrEqual" => "_gte_count_image_entries",
		"countAudioEntriesLessThanOrEqual" => "_lte_count_audio_entries",
		"countAudioEntriesGreaterThanOrEqual" => "_gte_count_audio_entries",
		"countMixEntriesLessThanOrEqual" => "_lte_count_mix_entries",
		"countMixEntriesGreaterThanOrEqual" => "_gte_count_mix_entries",
		"countPlaylistsLessThanOrEqual" => "_lte_count_playlists",
		"countPlaylistsGreaterThanOrEqual" => "_gte_count_playlists",
		"countBandwidthLessThanOrEqual" => "_lte_count_bandwidth",
		"countBandwidthGreaterThanOrEqual" => "_gte_count_bandwidth",
		"countStorageLessThanOrEqual" => "_lte_count_storage",
		"countStorageGreaterThanOrEqual" => "_gte_count_storage",
		"countUsersLessThanOrEqual" => "_lte_count_users",
		"countUsersGreaterThanOrEqual" => "_gte_count_users",
		"countWidgetsLessThanOrEqual" => "_lte_count_widgets",
		"countWidgetsGreaterThanOrEqual" => "_gte_count_widgets",
		"aggregatedStorageLessThanOrEqual" => "_lte_aggregated_storage",
		"aggregatedStorageGreaterThanOrEqual" => "_gte_aggregated_storage",
		"aggregatedBandwidthLessThanOrEqual" => "_lte_aggregated_bandwidth",
		"aggregatedBandwidthGreaterThanOrEqual" => "_gte_aggregated_bandwidth",
		"countBufferStartLessThanOrEqual" => "_lte_count_buffer_start",
		"countBufferStartGreaterThanOrEqual" => "_gte_count_buffer_start",
		"countBufferEndLessThanOrEqual" => "_lte_count_buffer_end",
		"countBufferEndGreaterThanOrEqual" => "_gte_count_buffer_end",
		"countOpenFullScreenLessThanOrEqual" => "_lte_count_open_full_screen",
		"countOpenFullScreenGreaterThanOrEqual" => "_gte_count_open_full_screen",
		"countCloseFullScreenLessThanOrEqual" => "_lte_count_close_full_screen",
		"countCloseFullScreenGreaterThanOrEqual" => "_gte_count_close_full_screen",
		"countReplayLessThanOrEqual" => "_lte_count_replay",
		"countReplayGreaterThanOrEqual" => "_gte_count_replay",
		"countSeekLessThanOrEqual" => "_lte_count_seek",
		"countSeekGreaterThanOrEqual" => "_gte_count_seek",
		"countOpenUploadLessThanOrEqual" => "_lte_count_open_upload",
		"countOpenUploadGreaterThanOrEqual" => "_gte_count_open_upload",
		"countSavePublishLessThanOrEqual" => "_lte_count_save_publish",
		"countSavePublishGreaterThanOrEqual" => "_gte_count_save_publish",
		"countCloseEditorLessThanOrEqual" => "_lte_count_close_editor",
		"countCloseEditorGreaterThanOrEqual" => "_gte_count_close_editor",
		"countPreBumperPlayedLessThanOrEqual" => "_lte_count_pre_bumper_played",
		"countPreBumperPlayedGreaterThanOrEqual" => "_gte_count_pre_bumper_played",
		"countPostBumperPlayedLessThanOrEqual" => "_lte_count_post_bumper_played",
		"countPostBumperPlayedGreaterThanOrEqual" => "_gte_count_post_bumper_played",
		"countBumperClickedLessThanOrEqual" => "_lte_count_bumper_clicked",
		"countBumperClickedGreaterThanOrEqual" => "_gte_count_bumper_clicked",
		"countPrerollStartedLessThanOrEqual" => "_lte_count_preroll_started",
		"countPrerollStartedGreaterThanOrEqual" => "_gte_count_preroll_started",
		"countMidrollStartedLessThanOrEqual" => "_lte_count_midroll_started",
		"countMidrollStartedGreaterThanOrEqual" => "_gte_count_midroll_started",
		"countPostrollStartedLessThanOrEqual" => "_lte_count_postroll_started",
		"countPostrollStartedGreaterThanOrEqual" => "_gte_count_postroll_started",
		"countOverlayStartedLessThanOrEqual" => "_lte_count_overlay_started",
		"countOverlayStartedGreaterThanOrEqual" => "_gte_count_overlay_started",
		"countPrerollClickedLessThanOrEqual" => "_lte_count_preroll_clicked",
		"countPrerollClickedGreaterThanOrEqual" => "_gte_count_preroll_clicked",
		"countMidrollClickedLessThanOrEqual" => "_lte_count_midroll_clicked",
		"countMidrollClickedGreaterThanOrEqual" => "_gte_count_midroll_clicked",
		"countPostrollClickedLessThanOrEqual" => "_lte_count_postroll_clicked",
		"countPostrollClickedGreaterThanOrEqual" => "_gte_count_postroll_clicked",
		"countOverlayClickedLessThanOrEqual" => "_lte_count_overlay_clicked",
		"countOverlayClickedGreaterThanOrEqual" => "_gte_count_overlay_clicked",
		"countPreroll25LessThanOrEqual" => "_lte_count_preroll25",
		"countPreroll25GreaterThanOrEqual" => "_gte_count_preroll25",
		"countPreroll50LessThanOrEqual" => "_lte_count_preroll50",
		"countPreroll50GreaterThanOrEqual" => "_gte_count_preroll50",
		"countPreroll75LessThanOrEqual" => "_lte_count_preroll75",
		"countPreroll75GreaterThanOrEqual" => "_gte_count_preroll75",
		"countMidroll25LessThanOrEqual" => "_lte_count_midroll25",
		"countMidroll25GreaterThanOrEqual" => "_gte_count_midroll25",
		"countMidroll50LessThanOrEqual" => "_lte_count_midroll50",
		"countMidroll50GreaterThanOrEqual" => "_gte_count_midroll50",
		"countMidroll75LessThanOrEqual" => "_lte_count_midroll75",
		"countMidroll75GreaterThanOrEqual" => "_gte_count_midroll75",
		"countPostroll25LessThanOrEqual" => "_lte_count_postroll25",
		"countPostroll25GreaterThanOrEqual" => "_gte_count_postroll25",
		"countPostroll50LessThanOrEqual" => "_lte_count_postroll50",
		"countPostroll50GreaterThanOrEqual" => "_gte_count_postroll50",
		"countPostroll75LessThanOrEqual" => "_lte_count_postroll75",
		"countPostroll75GreaterThanOrEqual" => "_gte_count_postroll75",
		"countLiveStreamingBandwidthLessThanOrEqual" => "_lte_count_live_streaming_bandwidth",
		"countLiveStreamingBandwidthGreaterThanOrEqual" => "_gte_count_live_streaming_bandwidth",
		"aggregatedLiveStreamingBandwidthLessThanOrEqual" => "_lte_aggregated_live_streaming_bandwidth",
		"aggregatedLiveStreamingBandwidthGreaterThanOrEqual" => "_gte_aggregated_live_streaming_bandwidth",
	);

	private $order_by_map = array
	(
		"+aggregatedTime" => "+aggregated_time",
		"-aggregatedTime" => "-aggregated_time",
		"+sumTimeViewed" => "+sum_time_viewed",
		"-sumTimeViewed" => "-sum_time_viewed",
		"+averageTimeViewed" => "+average_time_viewed",
		"-averageTimeViewed" => "-average_time_viewed",
		"+countPlays" => "+count_plays",
		"-countPlays" => "-count_plays",
		"+countLoads" => "+count_loads",
		"-countLoads" => "-count_loads",
		"+countPlays25" => "+count_plays25",
		"-countPlays25" => "-count_plays25",
		"+countPlays50" => "+count_plays50",
		"-countPlays50" => "-count_plays50",
		"+countPlays75" => "+count_plays75",
		"-countPlays75" => "-count_plays75",
		"+countPlays100" => "+count_plays100",
		"-countPlays100" => "-count_plays100",
		"+countEdit" => "+count_edit",
		"-countEdit" => "-count_edit",
		"+countShares" => "+count_shares",
		"-countShares" => "-count_shares",
		"+countDownload" => "+count_download",
		"-countDownload" => "-count_download",
		"+countReportAbuse" => "+count_report_abuse",
		"-countReportAbuse" => "-count_report_abuse",
		"+countMediaEntries" => "+count_media_entries",
		"-countMediaEntries" => "-count_media_entries",
		"+countVideoEntries" => "+count_video_entries",
		"-countVideoEntries" => "-count_video_entries",
		"+countImageEntries" => "+count_image_entries",
		"-countImageEntries" => "-count_image_entries",
		"+countAudioEntries" => "+count_audio_entries",
		"-countAudioEntries" => "-count_audio_entries",
		"+countMixEntries" => "+count_mix_entries",
		"-countMixEntries" => "-count_mix_entries",
		"+countPlaylists" => "+count_playlists",
		"-countPlaylists" => "-count_playlists",
		"+countBandwidth" => "+count_bandwidth",
		"-countBandwidth" => "-count_bandwidth",
		"+countStorage" => "+count_storage",
		"-countStorage" => "-count_storage",
		"+countUsers" => "+count_users",
		"-countUsers" => "-count_users",
		"+countWidgets" => "+count_widgets",
		"-countWidgets" => "-count_widgets",
		"+aggregatedStorage" => "+aggregated_storage",
		"-aggregatedStorage" => "-aggregated_storage",
		"+aggregatedBandwidth" => "+aggregated_bandwidth",
		"-aggregatedBandwidth" => "-aggregated_bandwidth",
		"+countBufferStart" => "+count_buffer_start",
		"-countBufferStart" => "-count_buffer_start",
		"+countBufferEnd" => "+count_buffer_end",
		"-countBufferEnd" => "-count_buffer_end",
		"+countOpenFullScreen" => "+count_open_full_screen",
		"-countOpenFullScreen" => "-count_open_full_screen",
		"+countCloseFullScreen" => "+count_close_full_screen",
		"-countCloseFullScreen" => "-count_close_full_screen",
		"+countReplay" => "+count_replay",
		"-countReplay" => "-count_replay",
		"+countSeek" => "+count_seek",
		"-countSeek" => "-count_seek",
		"+countOpenUpload" => "+count_open_upload",
		"-countOpenUpload" => "-count_open_upload",
		"+countSavePublish" => "+count_save_publish",
		"-countSavePublish" => "-count_save_publish",
		"+countCloseEditor" => "+count_close_editor",
		"-countCloseEditor" => "-count_close_editor",
		"+countPreBumperPlayed" => "+count_pre_bumper_played",
		"-countPreBumperPlayed" => "-count_pre_bumper_played",
		"+countPostBumperPlayed" => "+count_post_bumper_played",
		"-countPostBumperPlayed" => "-count_post_bumper_played",
		"+countBumperClicked" => "+count_bumper_clicked",
		"-countBumperClicked" => "-count_bumper_clicked",
		"+countPrerollStarted" => "+count_preroll_started",
		"-countPrerollStarted" => "-count_preroll_started",
		"+countMidrollStarted" => "+count_midroll_started",
		"-countMidrollStarted" => "-count_midroll_started",
		"+countPostrollStarted" => "+count_postroll_started",
		"-countPostrollStarted" => "-count_postroll_started",
		"+countOverlayStarted" => "+count_overlay_started",
		"-countOverlayStarted" => "-count_overlay_started",
		"+countPrerollClicked" => "+count_preroll_clicked",
		"-countPrerollClicked" => "-count_preroll_clicked",
		"+countMidrollClicked" => "+count_midroll_clicked",
		"-countMidrollClicked" => "-count_midroll_clicked",
		"+countPostrollClicked" => "+count_postroll_clicked",
		"-countPostrollClicked" => "-count_postroll_clicked",
		"+countOverlayClicked" => "+count_overlay_clicked",
		"-countOverlayClicked" => "-count_overlay_clicked",
		"+countPreroll25" => "+count_preroll25",
		"-countPreroll25" => "-count_preroll25",
		"+countPreroll50" => "+count_preroll50",
		"-countPreroll50" => "-count_preroll50",
		"+countPreroll75" => "+count_preroll75",
		"-countPreroll75" => "-count_preroll75",
		"+countMidroll25" => "+count_midroll25",
		"-countMidroll25" => "-count_midroll25",
		"+countMidroll50" => "+count_midroll50",
		"-countMidroll50" => "-count_midroll50",
		"+countMidroll75" => "+count_midroll75",
		"-countMidroll75" => "-count_midroll75",
		"+countPostroll25" => "+count_postroll25",
		"-countPostroll25" => "-count_postroll25",
		"+countPostroll50" => "+count_postroll50",
		"-countPostroll50" => "-count_postroll50",
		"+countPostroll75" => "+count_postroll75",
		"-countPostroll75" => "-count_postroll75",
		"+countLiveStreamingBandwidth" => "+count_live_streaming_bandwidth",
		"-countLiveStreamingBandwidth" => "-count_live_streaming_bandwidth",
		"+aggregatedLiveStreamingBandwidth" => "+aggregated_live_streaming_bandwidth",
		"-aggregatedLiveStreamingBandwidth" => "-aggregated_live_streaming_bandwidth",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}

	/**
	 * 
	 * 
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $aggregatedTimeLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $aggregatedTimeGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var float
	 */
	public $sumTimeViewedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var float
	 */
	public $sumTimeViewedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var float
	 */
	public $averageTimeViewedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var float
	 */
	public $averageTimeViewedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlaysLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlaysGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countLoadsLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countLoadsGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlays25LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlays25GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlays50LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlays50GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlays75LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlays75GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlays100LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlays100GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countEditLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countEditGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countSharesLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countSharesGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countDownloadLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countDownloadGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countReportAbuseLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countReportAbuseGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMediaEntriesLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMediaEntriesGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countVideoEntriesLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countVideoEntriesGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countImageEntriesLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countImageEntriesGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countAudioEntriesLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countAudioEntriesGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMixEntriesLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMixEntriesGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlaylistsLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPlaylistsGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $countBandwidthLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $countBandwidthGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $countStorageLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $countStorageGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countUsersLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countUsersGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countWidgetsLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countWidgetsGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $aggregatedStorageLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $aggregatedStorageGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $aggregatedBandwidthLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $aggregatedBandwidthGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countBufferStartLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countBufferStartGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countBufferEndLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countBufferEndGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countOpenFullScreenLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countOpenFullScreenGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countCloseFullScreenLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countCloseFullScreenGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countReplayLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countReplayGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countSeekLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countSeekGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countOpenUploadLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countOpenUploadGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countSavePublishLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countSavePublishGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countCloseEditorLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countCloseEditorGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPreBumperPlayedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPreBumperPlayedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostBumperPlayedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostBumperPlayedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countBumperClickedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countBumperClickedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPrerollStartedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPrerollStartedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMidrollStartedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMidrollStartedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostrollStartedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostrollStartedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countOverlayStartedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countOverlayStartedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPrerollClickedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPrerollClickedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMidrollClickedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMidrollClickedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostrollClickedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostrollClickedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countOverlayClickedLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countOverlayClickedGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPreroll25LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPreroll25GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPreroll50LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPreroll50GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPreroll75LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPreroll75GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMidroll25LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMidroll25GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMidroll50LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMidroll50GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMidroll75LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countMidroll75GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostroll25LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostroll25GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostroll50LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostroll50GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostroll75LessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $countPostroll75GreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $countLiveStreamingBandwidthLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $countLiveStreamingBandwidthGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $aggregatedLiveStreamingBandwidthLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $aggregatedLiveStreamingBandwidthGreaterThanOrEqual;
}
