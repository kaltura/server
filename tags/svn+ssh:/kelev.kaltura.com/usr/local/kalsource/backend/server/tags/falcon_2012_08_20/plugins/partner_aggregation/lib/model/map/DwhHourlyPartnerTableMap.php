<?php


/**
 * This class defines the structure of the 'dwh_hourly_partner' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.partnerAggregation
 * @subpackage model.map
 */
class DwhHourlyPartnerTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.partnerAggregation.DwhHourlyPartnerTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('dwh_hourly_partner');
		$this->setPhpName('DwhHourlyPartner');
		$this->setClassname('DwhHourlyPartner');
		$this->setPackage('plugins.partnerAggregation');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, 0);
		$this->addPrimaryKey('DATE_ID', 'DateId', 'INTEGER', true, null, 0);
		$this->addPrimaryKey('HOUR_ID', 'HourId', 'INTEGER', true, null, 0);
		$this->addColumn('SUM_TIME_VIEWED', 'SumTimeViewed', 'DECIMAL', false, 20, null);
		$this->addColumn('COUNT_TIME_VIEWED', 'CountTimeViewed', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PLAYS', 'CountPlays', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_LOADS', 'CountLoads', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PLAYS_25', 'CountPlays25', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PLAYS_50', 'CountPlays50', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PLAYS_75', 'CountPlays75', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PLAYS_100', 'CountPlays100', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_EDIT', 'CountEdit', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_VIRAL', 'CountViral', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_DOWNLOAD', 'CountDownload', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_REPORT', 'CountReport', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_MEDIA', 'CountMedia', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_VIDEO', 'CountVideo', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_IMAGE', 'CountImage', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_AUDIO', 'CountAudio', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_MIX', 'CountMix', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_MIX_NON_EMPTY', 'CountMixNonEmpty', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PLAYLIST', 'CountPlaylist', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_BANDWIDTH', 'CountBandwidth', 'BIGINT', false, null, null);
		$this->addColumn('COUNT_STORAGE', 'CountStorage', 'BIGINT', false, null, null);
		$this->addColumn('COUNT_USERS', 'CountUsers', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_WIDGETS', 'CountWidgets', 'INTEGER', false, null, null);
		$this->addColumn('FLAG_ACTIVE_SITE', 'FlagActiveSite', 'TINYINT', false, null, 0);
		$this->addColumn('FLAG_ACTIVE_PUBLISHER', 'FlagActivePublisher', 'TINYINT', false, null, 0);
		$this->addColumn('AGGR_STORAGE', 'AggrStorage', 'BIGINT', false, null, null);
		$this->addColumn('AGGR_BANDWIDTH', 'AggrBandwidth', 'BIGINT', false, null, null);
		$this->addColumn('COUNT_BUF_START', 'CountBufStart', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_BUF_END', 'CountBufEnd', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_OPEN_FULL_SCREEN', 'CountOpenFullScreen', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_CLOSE_FULL_SCREEN', 'CountCloseFullScreen', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_REPLAY', 'CountReplay', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_SEEK', 'CountSeek', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_OPEN_UPLOAD', 'CountOpenUpload', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_SAVE_PUBLISH', 'CountSavePublish', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_CLOSE_EDITOR', 'CountCloseEditor', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PRE_BUMPER_PLAYED', 'CountPreBumperPlayed', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_POST_BUMPER_PLAYED', 'CountPostBumperPlayed', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_BUMPER_CLICKED', 'CountBumperClicked', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PREROLL_STARTED', 'CountPrerollStarted', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_MIDROLL_STARTED', 'CountMidrollStarted', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_POSTROLL_STARTED', 'CountPostrollStarted', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_OVERLAY_STARTED', 'CountOverlayStarted', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PREROLL_CLICKED', 'CountPrerollClicked', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_MIDROLL_CLICKED', 'CountMidrollClicked', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_POSTROLL_CLICKED', 'CountPostrollClicked', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_OVERLAY_CLICKED', 'CountOverlayClicked', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PREROLL_25', 'CountPreroll25', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PREROLL_50', 'CountPreroll50', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_PREROLL_75', 'CountPreroll75', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_MIDROLL_25', 'CountMidroll25', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_MIDROLL_50', 'CountMidroll50', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_MIDROLL_75', 'CountMidroll75', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_POSTROLL_25', 'CountPostroll25', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_POSTROLL_50', 'CountPostroll50', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_POSTROLL_75', 'CountPostroll75', 'INTEGER', false, null, null);
		$this->addColumn('COUNT_STREAMING', 'CountStreaming', 'BIGINT', false, null, 0);
		$this->addColumn('AGGR_STREAMING', 'AggrStreaming', 'BIGINT', false, null, 0);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // DwhHourlyPartnerTableMap
