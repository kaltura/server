<?php

/**
 * Base static class for performing query and update operations on the 'dwh_hourly_partner' table.
 *
 * 
 *
 * @package plugins.partnerAggregation
 * @subpackage model.om
 */
abstract class BaseDwhHourlyPartnerPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'dwh_hourly_partner';

	/** the related Propel class for this table */
	const OM_CLASS = 'DwhHourlyPartner';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'plugins.partnerAggregation.DwhHourlyPartner';

	/** the related TableMap class for this table */
	const TM_CLASS = 'DwhHourlyPartnerTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 61;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'dwh_hourly_partner.PARTNER_ID';

	/** the column name for the DATE_ID field */
	const DATE_ID = 'dwh_hourly_partner.DATE_ID';

	/** the column name for the HOUR_ID field */
	const HOUR_ID = 'dwh_hourly_partner.HOUR_ID';

	/** the column name for the SUM_TIME_VIEWED field */
	const SUM_TIME_VIEWED = 'dwh_hourly_partner.SUM_TIME_VIEWED';

	/** the column name for the COUNT_TIME_VIEWED field */
	const COUNT_TIME_VIEWED = 'dwh_hourly_partner.COUNT_TIME_VIEWED';

	/** the column name for the COUNT_PLAYS field */
	const COUNT_PLAYS = 'dwh_hourly_partner.COUNT_PLAYS';

	/** the column name for the COUNT_LOADS field */
	const COUNT_LOADS = 'dwh_hourly_partner.COUNT_LOADS';

	/** the column name for the COUNT_PLAYS_25 field */
	const COUNT_PLAYS_25 = 'dwh_hourly_partner.COUNT_PLAYS_25';

	/** the column name for the COUNT_PLAYS_50 field */
	const COUNT_PLAYS_50 = 'dwh_hourly_partner.COUNT_PLAYS_50';

	/** the column name for the COUNT_PLAYS_75 field */
	const COUNT_PLAYS_75 = 'dwh_hourly_partner.COUNT_PLAYS_75';

	/** the column name for the COUNT_PLAYS_100 field */
	const COUNT_PLAYS_100 = 'dwh_hourly_partner.COUNT_PLAYS_100';

	/** the column name for the COUNT_EDIT field */
	const COUNT_EDIT = 'dwh_hourly_partner.COUNT_EDIT';

	/** the column name for the COUNT_VIRAL field */
	const COUNT_VIRAL = 'dwh_hourly_partner.COUNT_VIRAL';

	/** the column name for the COUNT_DOWNLOAD field */
	const COUNT_DOWNLOAD = 'dwh_hourly_partner.COUNT_DOWNLOAD';

	/** the column name for the COUNT_REPORT field */
	const COUNT_REPORT = 'dwh_hourly_partner.COUNT_REPORT';

	/** the column name for the COUNT_MEDIA field */
	const COUNT_MEDIA = 'dwh_hourly_partner.COUNT_MEDIA';

	/** the column name for the COUNT_VIDEO field */
	const COUNT_VIDEO = 'dwh_hourly_partner.COUNT_VIDEO';

	/** the column name for the COUNT_IMAGE field */
	const COUNT_IMAGE = 'dwh_hourly_partner.COUNT_IMAGE';

	/** the column name for the COUNT_AUDIO field */
	const COUNT_AUDIO = 'dwh_hourly_partner.COUNT_AUDIO';

	/** the column name for the COUNT_MIX field */
	const COUNT_MIX = 'dwh_hourly_partner.COUNT_MIX';

	/** the column name for the COUNT_MIX_NON_EMPTY field */
	const COUNT_MIX_NON_EMPTY = 'dwh_hourly_partner.COUNT_MIX_NON_EMPTY';

	/** the column name for the COUNT_PLAYLIST field */
	const COUNT_PLAYLIST = 'dwh_hourly_partner.COUNT_PLAYLIST';

	/** the column name for the COUNT_BANDWIDTH field */
	const COUNT_BANDWIDTH = 'dwh_hourly_partner.COUNT_BANDWIDTH';

	/** the column name for the COUNT_STORAGE field */
	const COUNT_STORAGE = 'dwh_hourly_partner.COUNT_STORAGE';

	/** the column name for the COUNT_USERS field */
	const COUNT_USERS = 'dwh_hourly_partner.COUNT_USERS';

	/** the column name for the COUNT_WIDGETS field */
	const COUNT_WIDGETS = 'dwh_hourly_partner.COUNT_WIDGETS';

	/** the column name for the FLAG_ACTIVE_SITE field */
	const FLAG_ACTIVE_SITE = 'dwh_hourly_partner.FLAG_ACTIVE_SITE';

	/** the column name for the FLAG_ACTIVE_PUBLISHER field */
	const FLAG_ACTIVE_PUBLISHER = 'dwh_hourly_partner.FLAG_ACTIVE_PUBLISHER';

	/** the column name for the AGGR_STORAGE field */
	const AGGR_STORAGE = 'dwh_hourly_partner.AGGR_STORAGE';

	/** the column name for the AGGR_BANDWIDTH field */
	const AGGR_BANDWIDTH = 'dwh_hourly_partner.AGGR_BANDWIDTH';

	/** the column name for the COUNT_BUF_START field */
	const COUNT_BUF_START = 'dwh_hourly_partner.COUNT_BUF_START';

	/** the column name for the COUNT_BUF_END field */
	const COUNT_BUF_END = 'dwh_hourly_partner.COUNT_BUF_END';

	/** the column name for the COUNT_OPEN_FULL_SCREEN field */
	const COUNT_OPEN_FULL_SCREEN = 'dwh_hourly_partner.COUNT_OPEN_FULL_SCREEN';

	/** the column name for the COUNT_CLOSE_FULL_SCREEN field */
	const COUNT_CLOSE_FULL_SCREEN = 'dwh_hourly_partner.COUNT_CLOSE_FULL_SCREEN';

	/** the column name for the COUNT_REPLAY field */
	const COUNT_REPLAY = 'dwh_hourly_partner.COUNT_REPLAY';

	/** the column name for the COUNT_SEEK field */
	const COUNT_SEEK = 'dwh_hourly_partner.COUNT_SEEK';

	/** the column name for the COUNT_OPEN_UPLOAD field */
	const COUNT_OPEN_UPLOAD = 'dwh_hourly_partner.COUNT_OPEN_UPLOAD';

	/** the column name for the COUNT_SAVE_PUBLISH field */
	const COUNT_SAVE_PUBLISH = 'dwh_hourly_partner.COUNT_SAVE_PUBLISH';

	/** the column name for the COUNT_CLOSE_EDITOR field */
	const COUNT_CLOSE_EDITOR = 'dwh_hourly_partner.COUNT_CLOSE_EDITOR';

	/** the column name for the COUNT_PRE_BUMPER_PLAYED field */
	const COUNT_PRE_BUMPER_PLAYED = 'dwh_hourly_partner.COUNT_PRE_BUMPER_PLAYED';

	/** the column name for the COUNT_POST_BUMPER_PLAYED field */
	const COUNT_POST_BUMPER_PLAYED = 'dwh_hourly_partner.COUNT_POST_BUMPER_PLAYED';

	/** the column name for the COUNT_BUMPER_CLICKED field */
	const COUNT_BUMPER_CLICKED = 'dwh_hourly_partner.COUNT_BUMPER_CLICKED';

	/** the column name for the COUNT_PREROLL_STARTED field */
	const COUNT_PREROLL_STARTED = 'dwh_hourly_partner.COUNT_PREROLL_STARTED';

	/** the column name for the COUNT_MIDROLL_STARTED field */
	const COUNT_MIDROLL_STARTED = 'dwh_hourly_partner.COUNT_MIDROLL_STARTED';

	/** the column name for the COUNT_POSTROLL_STARTED field */
	const COUNT_POSTROLL_STARTED = 'dwh_hourly_partner.COUNT_POSTROLL_STARTED';

	/** the column name for the COUNT_OVERLAY_STARTED field */
	const COUNT_OVERLAY_STARTED = 'dwh_hourly_partner.COUNT_OVERLAY_STARTED';

	/** the column name for the COUNT_PREROLL_CLICKED field */
	const COUNT_PREROLL_CLICKED = 'dwh_hourly_partner.COUNT_PREROLL_CLICKED';

	/** the column name for the COUNT_MIDROLL_CLICKED field */
	const COUNT_MIDROLL_CLICKED = 'dwh_hourly_partner.COUNT_MIDROLL_CLICKED';

	/** the column name for the COUNT_POSTROLL_CLICKED field */
	const COUNT_POSTROLL_CLICKED = 'dwh_hourly_partner.COUNT_POSTROLL_CLICKED';

	/** the column name for the COUNT_OVERLAY_CLICKED field */
	const COUNT_OVERLAY_CLICKED = 'dwh_hourly_partner.COUNT_OVERLAY_CLICKED';

	/** the column name for the COUNT_PREROLL_25 field */
	const COUNT_PREROLL_25 = 'dwh_hourly_partner.COUNT_PREROLL_25';

	/** the column name for the COUNT_PREROLL_50 field */
	const COUNT_PREROLL_50 = 'dwh_hourly_partner.COUNT_PREROLL_50';

	/** the column name for the COUNT_PREROLL_75 field */
	const COUNT_PREROLL_75 = 'dwh_hourly_partner.COUNT_PREROLL_75';

	/** the column name for the COUNT_MIDROLL_25 field */
	const COUNT_MIDROLL_25 = 'dwh_hourly_partner.COUNT_MIDROLL_25';

	/** the column name for the COUNT_MIDROLL_50 field */
	const COUNT_MIDROLL_50 = 'dwh_hourly_partner.COUNT_MIDROLL_50';

	/** the column name for the COUNT_MIDROLL_75 field */
	const COUNT_MIDROLL_75 = 'dwh_hourly_partner.COUNT_MIDROLL_75';

	/** the column name for the COUNT_POSTROLL_25 field */
	const COUNT_POSTROLL_25 = 'dwh_hourly_partner.COUNT_POSTROLL_25';

	/** the column name for the COUNT_POSTROLL_50 field */
	const COUNT_POSTROLL_50 = 'dwh_hourly_partner.COUNT_POSTROLL_50';

	/** the column name for the COUNT_POSTROLL_75 field */
	const COUNT_POSTROLL_75 = 'dwh_hourly_partner.COUNT_POSTROLL_75';

	/** the column name for the COUNT_STREAMING field */
	const COUNT_STREAMING = 'dwh_hourly_partner.COUNT_STREAMING';

	/** the column name for the AGGR_STREAMING field */
	const AGGR_STREAMING = 'dwh_hourly_partner.AGGR_STREAMING';

	/**
	 * An identiy map to hold any loaded instances of DwhHourlyPartner objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array DwhHourlyPartner[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('PartnerId', 'DateId', 'HourId', 'SumTimeViewed', 'CountTimeViewed', 'CountPlays', 'CountLoads', 'CountPlays25', 'CountPlays50', 'CountPlays75', 'CountPlays100', 'CountEdit', 'CountViral', 'CountDownload', 'CountReport', 'CountMedia', 'CountVideo', 'CountImage', 'CountAudio', 'CountMix', 'CountMixNonEmpty', 'CountPlaylist', 'CountBandwidth', 'CountStorage', 'CountUsers', 'CountWidgets', 'FlagActiveSite', 'FlagActivePublisher', 'AggrStorage', 'AggrBandwidth', 'CountBufStart', 'CountBufEnd', 'CountOpenFullScreen', 'CountCloseFullScreen', 'CountReplay', 'CountSeek', 'CountOpenUpload', 'CountSavePublish', 'CountCloseEditor', 'CountPreBumperPlayed', 'CountPostBumperPlayed', 'CountBumperClicked', 'CountPrerollStarted', 'CountMidrollStarted', 'CountPostrollStarted', 'CountOverlayStarted', 'CountPrerollClicked', 'CountMidrollClicked', 'CountPostrollClicked', 'CountOverlayClicked', 'CountPreroll25', 'CountPreroll50', 'CountPreroll75', 'CountMidroll25', 'CountMidroll50', 'CountMidroll75', 'CountPostroll25', 'CountPostroll50', 'CountPostroll75', 'CountStreaming', 'AggrStreaming', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('partnerId', 'dateId', 'hourId', 'sumTimeViewed', 'countTimeViewed', 'countPlays', 'countLoads', 'countPlays25', 'countPlays50', 'countPlays75', 'countPlays100', 'countEdit', 'countViral', 'countDownload', 'countReport', 'countMedia', 'countVideo', 'countImage', 'countAudio', 'countMix', 'countMixNonEmpty', 'countPlaylist', 'countBandwidth', 'countStorage', 'countUsers', 'countWidgets', 'flagActiveSite', 'flagActivePublisher', 'aggrStorage', 'aggrBandwidth', 'countBufStart', 'countBufEnd', 'countOpenFullScreen', 'countCloseFullScreen', 'countReplay', 'countSeek', 'countOpenUpload', 'countSavePublish', 'countCloseEditor', 'countPreBumperPlayed', 'countPostBumperPlayed', 'countBumperClicked', 'countPrerollStarted', 'countMidrollStarted', 'countPostrollStarted', 'countOverlayStarted', 'countPrerollClicked', 'countMidrollClicked', 'countPostrollClicked', 'countOverlayClicked', 'countPreroll25', 'countPreroll50', 'countPreroll75', 'countMidroll25', 'countMidroll50', 'countMidroll75', 'countPostroll25', 'countPostroll50', 'countPostroll75', 'countStreaming', 'aggrStreaming', ),
		BasePeer::TYPE_COLNAME => array (self::PARTNER_ID, self::DATE_ID, self::HOUR_ID, self::SUM_TIME_VIEWED, self::COUNT_TIME_VIEWED, self::COUNT_PLAYS, self::COUNT_LOADS, self::COUNT_PLAYS_25, self::COUNT_PLAYS_50, self::COUNT_PLAYS_75, self::COUNT_PLAYS_100, self::COUNT_EDIT, self::COUNT_VIRAL, self::COUNT_DOWNLOAD, self::COUNT_REPORT, self::COUNT_MEDIA, self::COUNT_VIDEO, self::COUNT_IMAGE, self::COUNT_AUDIO, self::COUNT_MIX, self::COUNT_MIX_NON_EMPTY, self::COUNT_PLAYLIST, self::COUNT_BANDWIDTH, self::COUNT_STORAGE, self::COUNT_USERS, self::COUNT_WIDGETS, self::FLAG_ACTIVE_SITE, self::FLAG_ACTIVE_PUBLISHER, self::AGGR_STORAGE, self::AGGR_BANDWIDTH, self::COUNT_BUF_START, self::COUNT_BUF_END, self::COUNT_OPEN_FULL_SCREEN, self::COUNT_CLOSE_FULL_SCREEN, self::COUNT_REPLAY, self::COUNT_SEEK, self::COUNT_OPEN_UPLOAD, self::COUNT_SAVE_PUBLISH, self::COUNT_CLOSE_EDITOR, self::COUNT_PRE_BUMPER_PLAYED, self::COUNT_POST_BUMPER_PLAYED, self::COUNT_BUMPER_CLICKED, self::COUNT_PREROLL_STARTED, self::COUNT_MIDROLL_STARTED, self::COUNT_POSTROLL_STARTED, self::COUNT_OVERLAY_STARTED, self::COUNT_PREROLL_CLICKED, self::COUNT_MIDROLL_CLICKED, self::COUNT_POSTROLL_CLICKED, self::COUNT_OVERLAY_CLICKED, self::COUNT_PREROLL_25, self::COUNT_PREROLL_50, self::COUNT_PREROLL_75, self::COUNT_MIDROLL_25, self::COUNT_MIDROLL_50, self::COUNT_MIDROLL_75, self::COUNT_POSTROLL_25, self::COUNT_POSTROLL_50, self::COUNT_POSTROLL_75, self::COUNT_STREAMING, self::AGGR_STREAMING, ),
		BasePeer::TYPE_FIELDNAME => array ('partner_id', 'date_id', 'hour_id', 'sum_time_viewed', 'count_time_viewed', 'count_plays', 'count_loads', 'count_plays_25', 'count_plays_50', 'count_plays_75', 'count_plays_100', 'count_edit', 'count_viral', 'count_download', 'count_report', 'count_media', 'count_video', 'count_image', 'count_audio', 'count_mix', 'count_mix_non_empty', 'count_playlist', 'count_bandwidth', 'count_storage', 'count_users', 'count_widgets', 'flag_active_site', 'flag_active_publisher', 'aggr_storage', 'aggr_bandwidth', 'count_buf_start', 'count_buf_end', 'count_open_full_screen', 'count_close_full_screen', 'count_replay', 'count_seek', 'count_open_upload', 'count_save_publish', 'count_close_editor', 'count_pre_bumper_played', 'count_post_bumper_played', 'count_bumper_clicked', 'count_preroll_started', 'count_midroll_started', 'count_postroll_started', 'count_overlay_started', 'count_preroll_clicked', 'count_midroll_clicked', 'count_postroll_clicked', 'count_overlay_clicked', 'count_preroll_25', 'count_preroll_50', 'count_preroll_75', 'count_midroll_25', 'count_midroll_50', 'count_midroll_75', 'count_postroll_25', 'count_postroll_50', 'count_postroll_75', 'count_streaming', 'aggr_streaming', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('PartnerId' => 0, 'DateId' => 1, 'HourId' => 2, 'SumTimeViewed' => 3, 'CountTimeViewed' => 4, 'CountPlays' => 5, 'CountLoads' => 6, 'CountPlays25' => 7, 'CountPlays50' => 8, 'CountPlays75' => 9, 'CountPlays100' => 10, 'CountEdit' => 11, 'CountViral' => 12, 'CountDownload' => 13, 'CountReport' => 14, 'CountMedia' => 15, 'CountVideo' => 16, 'CountImage' => 17, 'CountAudio' => 18, 'CountMix' => 19, 'CountMixNonEmpty' => 20, 'CountPlaylist' => 21, 'CountBandwidth' => 22, 'CountStorage' => 23, 'CountUsers' => 24, 'CountWidgets' => 25, 'FlagActiveSite' => 26, 'FlagActivePublisher' => 27, 'AggrStorage' => 28, 'AggrBandwidth' => 29, 'CountBufStart' => 30, 'CountBufEnd' => 31, 'CountOpenFullScreen' => 32, 'CountCloseFullScreen' => 33, 'CountReplay' => 34, 'CountSeek' => 35, 'CountOpenUpload' => 36, 'CountSavePublish' => 37, 'CountCloseEditor' => 38, 'CountPreBumperPlayed' => 39, 'CountPostBumperPlayed' => 40, 'CountBumperClicked' => 41, 'CountPrerollStarted' => 42, 'CountMidrollStarted' => 43, 'CountPostrollStarted' => 44, 'CountOverlayStarted' => 45, 'CountPrerollClicked' => 46, 'CountMidrollClicked' => 47, 'CountPostrollClicked' => 48, 'CountOverlayClicked' => 49, 'CountPreroll25' => 50, 'CountPreroll50' => 51, 'CountPreroll75' => 52, 'CountMidroll25' => 53, 'CountMidroll50' => 54, 'CountMidroll75' => 55, 'CountPostroll25' => 56, 'CountPostroll50' => 57, 'CountPostroll75' => 58, 'CountStreaming' => 59, 'AggrStreaming' => 60, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('partnerId' => 0, 'dateId' => 1, 'hourId' => 2, 'sumTimeViewed' => 3, 'countTimeViewed' => 4, 'countPlays' => 5, 'countLoads' => 6, 'countPlays25' => 7, 'countPlays50' => 8, 'countPlays75' => 9, 'countPlays100' => 10, 'countEdit' => 11, 'countViral' => 12, 'countDownload' => 13, 'countReport' => 14, 'countMedia' => 15, 'countVideo' => 16, 'countImage' => 17, 'countAudio' => 18, 'countMix' => 19, 'countMixNonEmpty' => 20, 'countPlaylist' => 21, 'countBandwidth' => 22, 'countStorage' => 23, 'countUsers' => 24, 'countWidgets' => 25, 'flagActiveSite' => 26, 'flagActivePublisher' => 27, 'aggrStorage' => 28, 'aggrBandwidth' => 29, 'countBufStart' => 30, 'countBufEnd' => 31, 'countOpenFullScreen' => 32, 'countCloseFullScreen' => 33, 'countReplay' => 34, 'countSeek' => 35, 'countOpenUpload' => 36, 'countSavePublish' => 37, 'countCloseEditor' => 38, 'countPreBumperPlayed' => 39, 'countPostBumperPlayed' => 40, 'countBumperClicked' => 41, 'countPrerollStarted' => 42, 'countMidrollStarted' => 43, 'countPostrollStarted' => 44, 'countOverlayStarted' => 45, 'countPrerollClicked' => 46, 'countMidrollClicked' => 47, 'countPostrollClicked' => 48, 'countOverlayClicked' => 49, 'countPreroll25' => 50, 'countPreroll50' => 51, 'countPreroll75' => 52, 'countMidroll25' => 53, 'countMidroll50' => 54, 'countMidroll75' => 55, 'countPostroll25' => 56, 'countPostroll50' => 57, 'countPostroll75' => 58, 'countStreaming' => 59, 'aggrStreaming' => 60, ),
		BasePeer::TYPE_COLNAME => array (self::PARTNER_ID => 0, self::DATE_ID => 1, self::HOUR_ID => 2, self::SUM_TIME_VIEWED => 3, self::COUNT_TIME_VIEWED => 4, self::COUNT_PLAYS => 5, self::COUNT_LOADS => 6, self::COUNT_PLAYS_25 => 7, self::COUNT_PLAYS_50 => 8, self::COUNT_PLAYS_75 => 9, self::COUNT_PLAYS_100 => 10, self::COUNT_EDIT => 11, self::COUNT_VIRAL => 12, self::COUNT_DOWNLOAD => 13, self::COUNT_REPORT => 14, self::COUNT_MEDIA => 15, self::COUNT_VIDEO => 16, self::COUNT_IMAGE => 17, self::COUNT_AUDIO => 18, self::COUNT_MIX => 19, self::COUNT_MIX_NON_EMPTY => 20, self::COUNT_PLAYLIST => 21, self::COUNT_BANDWIDTH => 22, self::COUNT_STORAGE => 23, self::COUNT_USERS => 24, self::COUNT_WIDGETS => 25, self::FLAG_ACTIVE_SITE => 26, self::FLAG_ACTIVE_PUBLISHER => 27, self::AGGR_STORAGE => 28, self::AGGR_BANDWIDTH => 29, self::COUNT_BUF_START => 30, self::COUNT_BUF_END => 31, self::COUNT_OPEN_FULL_SCREEN => 32, self::COUNT_CLOSE_FULL_SCREEN => 33, self::COUNT_REPLAY => 34, self::COUNT_SEEK => 35, self::COUNT_OPEN_UPLOAD => 36, self::COUNT_SAVE_PUBLISH => 37, self::COUNT_CLOSE_EDITOR => 38, self::COUNT_PRE_BUMPER_PLAYED => 39, self::COUNT_POST_BUMPER_PLAYED => 40, self::COUNT_BUMPER_CLICKED => 41, self::COUNT_PREROLL_STARTED => 42, self::COUNT_MIDROLL_STARTED => 43, self::COUNT_POSTROLL_STARTED => 44, self::COUNT_OVERLAY_STARTED => 45, self::COUNT_PREROLL_CLICKED => 46, self::COUNT_MIDROLL_CLICKED => 47, self::COUNT_POSTROLL_CLICKED => 48, self::COUNT_OVERLAY_CLICKED => 49, self::COUNT_PREROLL_25 => 50, self::COUNT_PREROLL_50 => 51, self::COUNT_PREROLL_75 => 52, self::COUNT_MIDROLL_25 => 53, self::COUNT_MIDROLL_50 => 54, self::COUNT_MIDROLL_75 => 55, self::COUNT_POSTROLL_25 => 56, self::COUNT_POSTROLL_50 => 57, self::COUNT_POSTROLL_75 => 58, self::COUNT_STREAMING => 59, self::AGGR_STREAMING => 60, ),
		BasePeer::TYPE_FIELDNAME => array ('partner_id' => 0, 'date_id' => 1, 'hour_id' => 2, 'sum_time_viewed' => 3, 'count_time_viewed' => 4, 'count_plays' => 5, 'count_loads' => 6, 'count_plays_25' => 7, 'count_plays_50' => 8, 'count_plays_75' => 9, 'count_plays_100' => 10, 'count_edit' => 11, 'count_viral' => 12, 'count_download' => 13, 'count_report' => 14, 'count_media' => 15, 'count_video' => 16, 'count_image' => 17, 'count_audio' => 18, 'count_mix' => 19, 'count_mix_non_empty' => 20, 'count_playlist' => 21, 'count_bandwidth' => 22, 'count_storage' => 23, 'count_users' => 24, 'count_widgets' => 25, 'flag_active_site' => 26, 'flag_active_publisher' => 27, 'aggr_storage' => 28, 'aggr_bandwidth' => 29, 'count_buf_start' => 30, 'count_buf_end' => 31, 'count_open_full_screen' => 32, 'count_close_full_screen' => 33, 'count_replay' => 34, 'count_seek' => 35, 'count_open_upload' => 36, 'count_save_publish' => 37, 'count_close_editor' => 38, 'count_pre_bumper_played' => 39, 'count_post_bumper_played' => 40, 'count_bumper_clicked' => 41, 'count_preroll_started' => 42, 'count_midroll_started' => 43, 'count_postroll_started' => 44, 'count_overlay_started' => 45, 'count_preroll_clicked' => 46, 'count_midroll_clicked' => 47, 'count_postroll_clicked' => 48, 'count_overlay_clicked' => 49, 'count_preroll_25' => 50, 'count_preroll_50' => 51, 'count_preroll_75' => 52, 'count_midroll_25' => 53, 'count_midroll_50' => 54, 'count_midroll_75' => 55, 'count_postroll_25' => 56, 'count_postroll_50' => 57, 'count_postroll_75' => 58, 'count_streaming' => 59, 'aggr_streaming' => 60, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, )
	);

	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 * @throws     PropelException - if the specified name could not be found in the fieldname mappings.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	/**
	 * Convenience method which changes table.column to alias.column.
	 *
	 * Using this method you can maintain SQL abstraction while using column aliases.
	 * <code>
	 *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
	 *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
	 * </code>
	 * @param      string $alias The alias for the current table.
	 * @param      string $column The column name for current table. (i.e. DwhHourlyPartnerPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(DwhHourlyPartnerPeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      criteria object containing the columns to add.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria)
	{
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::PARTNER_ID);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::DATE_ID);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::HOUR_ID);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::SUM_TIME_VIEWED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_TIME_VIEWED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PLAYS);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_LOADS);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PLAYS_25);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PLAYS_50);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PLAYS_75);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PLAYS_100);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_EDIT);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_VIRAL);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_DOWNLOAD);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_REPORT);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_MEDIA);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_VIDEO);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_IMAGE);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_AUDIO);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_MIX);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_MIX_NON_EMPTY);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PLAYLIST);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_BANDWIDTH);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_STORAGE);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_USERS);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_WIDGETS);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::FLAG_ACTIVE_SITE);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::FLAG_ACTIVE_PUBLISHER);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::AGGR_STORAGE);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::AGGR_BANDWIDTH);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_BUF_START);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_BUF_END);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_OPEN_FULL_SCREEN);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_CLOSE_FULL_SCREEN);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_REPLAY);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_SEEK);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_OPEN_UPLOAD);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_SAVE_PUBLISH);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_CLOSE_EDITOR);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PRE_BUMPER_PLAYED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_POST_BUMPER_PLAYED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_BUMPER_CLICKED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PREROLL_STARTED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_MIDROLL_STARTED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_POSTROLL_STARTED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_OVERLAY_STARTED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PREROLL_CLICKED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_MIDROLL_CLICKED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_POSTROLL_CLICKED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_OVERLAY_CLICKED);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PREROLL_25);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PREROLL_50);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_PREROLL_75);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_MIDROLL_25);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_MIDROLL_50);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_MIDROLL_75);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_POSTROLL_25);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_POSTROLL_50);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_POSTROLL_75);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::COUNT_STREAMING);
		$criteria->addSelectColumn(DwhHourlyPartnerPeer::AGGR_STREAMING);
	}

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		// we may modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(DwhHourlyPartnerPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			DwhHourlyPartnerPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = DwhHourlyPartnerPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     DwhHourlyPartner
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = DwhHourlyPartnerPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Method to do selects.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		return DwhHourlyPartnerPeer::populateObjects(DwhHourlyPartnerPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(DwhHourlyPartnerPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = DwhHourlyPartnerPeer::getCriteriaFilter();
		
		if ( $use )  $criteria_filter->enable(); 
		else $criteria_filter->disable();
	}
	
	/**
	 * Returns the default criteria filter
	 *
	 * @return     criteriaFilter The default criteria filter.
	 */
	public static function &getCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			DwhHourlyPartnerPeer::setDefaultCriteriaFilter();
		
		return self::$s_criteria_filter;
	}
	
	 
	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new myCriteria(); 
		self::$s_criteria_filter->setFilter($c);
	}
	
	
	/**
	 * the filterCriteria will filter out all the doSelect methods - ONLY if the filter is turned on.
	 * IMPORTANT - the filter is turend on by default and when switched off - should be turned on again manually .
	 * 
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 */
	protected static function attachCriteriaFilter(Criteria $criteria)
	{
		DwhHourlyPartnerPeer::getCriteriaFilter()->applyFilter($criteria);
	}
	
	public static function addPartnerToCriteria($partnerId, $privatePartnerData = false, $partnerGroup = null, $kalturaNetwork = null)
	{
		$criteriaFilter = self::getCriteriaFilter();
		$criteria = $criteriaFilter->getFilter();
		
		if(!$privatePartnerData)
		{
			// the private partner data is not allowed - 
			if($kalturaNetwork)
			{
				// allow only the kaltura netword stuff
				if($partnerId)
				{
					$orderBy = "(" . self::PARTNER_ID . "<>{$partnerId})";  // first take the pattner_id and then the rest
					myCriteria::addComment($criteria , "Only Kaltura Network");
					$criteria->addAscendingOrderByColumn($orderBy);//, Criteria::CUSTOM );
				}
			}
			else
			{
				// no private data and no kaltura_network - 
				// add a criteria that will return nothing
				$criteria->addAnd(self::PARTNER_ID, Partner::PARTNER_THAT_DOWS_NOT_EXIST);
			}
		}
		else
		{
			// private data is allowed
			if(empty($partnerGroup) && empty($kalturaNetwork))
			{
				// the default case
				$criteria->addAnd(self::PARTNER_ID, $partnerId);
			}
			elseif ($partnerGroup == myPartnerUtils::ALL_PARTNERS_WILD_CHAR)
			{
				// all is allowed - don't add anything to the criteria
			}
			else 
			{
				$criterion = null;
				if($partnerGroup)
				{
					// $partnerGroup hold a list of partners separated by ',' or $kalturaNetwork is not empty (should be mySearchUtils::KALTURA_NETWORK = 'kn')
					$partners = explode(',', trim($partnerGroup));
					foreach($partners as &$p)
						trim($p); // make sure there are not leading or trailing spaces
	
					// add the partner_id to the partner_group
					$partners[] = $partnerId;
					
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partners, Criteria::IN);
				}
				else 
				{
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partnerId);
				}	
				
				$criteria->addAnd($criterion);
			}
		}
			
		$criteriaFilter->enable();
	}
	
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
	 * @see        BasePeer::doCount()
	 */
	public static function doCountStmt(Criteria $criteria, PropelPDO $con = null)
	{
		// attach default criteria
		DwhHourlyPartnerPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = DwhHourlyPartnerPeer::alternativeCon ( $con );
		
		// BasePeer returns a PDOStatement
		return BasePeer::doCount($criteria, $con);
	}
	
	
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
	 * @see        BasePeer::doSelect()
	 */
	public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
	{
		$con = DwhHourlyPartnerPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				DwhHourlyPartnerPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			DwhHourlyPartnerPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		DwhHourlyPartnerPeer::attachCriteriaFilter($criteria);
		
		// BasePeer returns a PDOStatement
		return BasePeer::doSelect($criteria, $con);
	}
	/**
	 * Adds an object to the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doSelect*()
	 * methods in your stub classes -- you may need to explicitly add objects
	 * to the cache in order to ensure that the same objects are always returned by doSelect*()
	 * and retrieveByPK*() calls.
	 *
	 * @param      DwhHourlyPartner $value A DwhHourlyPartner object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(DwhHourlyPartner $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = serialize(array((string) $obj->getPartnerId(), (string) $obj->getDateId(), (string) $obj->getHourId()));
			} // if key === null
			self::$instances[$key] = $obj;
		}
	}

	/**
	 * Removes an object from the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doDelete
	 * methods in your stub classes -- you may need to explicitly remove objects
	 * from the cache in order to prevent returning objects that no longer exist.
	 *
	 * @param      mixed $value A DwhHourlyPartner object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof DwhHourlyPartner) {
				$key = serialize(array((string) $value->getPartnerId(), (string) $value->getDateId(), (string) $value->getHourId()));
			} elseif (is_array($value) && count($value) === 3) {
				// assume we've been passed a primary key
				$key = serialize(array((string) $value[0], (string) $value[1], (string) $value[2]));
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or DwhHourlyPartner object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
				throw $e;
			}

			unset(self::$instances[$key]);
		}
	} // removeInstanceFromPool()

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
	 * @return     DwhHourlyPartner Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
	 * @see        getPrimaryKeyHash()
	 */
	public static function getInstanceFromPool($key)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if (isset(self::$instances[$key])) {
				return self::$instances[$key];
			}
		}
		return null; // just to be explicit
	}
	
	/**
	 * Clear the instance pool.
	 *
	 * @return     void
	 */
	public static function clearInstancePool()
	{
		foreach (self::$instances as $instance)
		{
			$instance->clearAllReferences(false);
		}
		self::$instances = array();
	}
	
	/**
	 * Method to invalidate the instance pool of all tables related to dwh_hourly_partner
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
	}

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     string A string version of PK or NULL if the components of primary key in result array are all null.
	 */
	public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
	{
		// If the PK cannot be derived from the row, return NULL.
		if ($row[$startcol] === null && $row[$startcol + 1] === null && $row[$startcol + 2] === null) {
			return null;
		}
		return serialize(array((string) $row[$startcol], (string) $row[$startcol + 1], (string) $row[$startcol + 2]));
	}

	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(PDOStatement $stmt)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = DwhHourlyPartnerPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = DwhHourlyPartnerPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = DwhHourlyPartnerPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				DwhHourlyPartnerPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}
	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * Add a TableMap instance to the database for this peer class.
	 */
	public static function buildTableMap()
	{
	  $dbMap = Propel::getDatabaseMap(BaseDwhHourlyPartnerPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseDwhHourlyPartnerPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new DwhHourlyPartnerTableMap());
	  }
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * If $withPrefix is true, the returned path
	 * uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @param      boolean  Whether or not to return the path wit hthe class name 
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass($withPrefix = true)
	{
		return $withPrefix ? DwhHourlyPartnerPeer::CLASS_DEFAULT : DwhHourlyPartnerPeer::OM_CLASS;
	}

	/**
	 * Retrieve object using using composite pkey values.
	 * @param      int $partner_id
	 * @param      int $date_id
	 * @param      int $hour_id
	 * @param      PropelPDO $con
	 * @return     DwhHourlyPartner
	 */
	public static function retrieveByPK($partner_id, $date_id, $hour_id, PropelPDO $con = null) {
		$key = serialize(array((string) $partner_id, (string) $date_id, (string) $hour_id));
 		if (null !== ($obj = DwhHourlyPartnerPeer::getInstanceFromPool($key))) {
 			return $obj;
		}

		$criteria = new Criteria(DwhHourlyPartnerPeer::DATABASE_NAME);
		$criteria->add(DwhHourlyPartnerPeer::PARTNER_ID, $partner_id);
		$criteria->add(DwhHourlyPartnerPeer::DATE_ID, $date_id);
		$criteria->add(DwhHourlyPartnerPeer::HOUR_ID, $hour_id);
		$v = DwhHourlyPartnerPeer::doSelect($criteria, $con);

		return !empty($v) ? $v[0] : null;
	}
} // BaseDwhHourlyPartnerPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseDwhHourlyPartnerPeer::buildTableMap();

