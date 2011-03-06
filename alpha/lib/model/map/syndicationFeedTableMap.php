<?php


/**
 * This class defines the structure of the 'syndication_feed' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package Core
 * @subpackage model.map
 */
class syndicationFeedTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.syndicationFeedTableMap';

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
		$this->setName('syndication_feed');
		$this->setPhpName('syndicationFeed');
		$this->setClassname('syndicationFeed');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'VARCHAR', true, 20, null);
		$this->addColumn('INT_ID', 'IntId', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('PLAYLIST_ID', 'PlaylistId', 'VARCHAR', false, 20, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', true, 128, '');
		$this->addColumn('STATUS', 'Status', 'TINYINT', false, null, null);
		$this->addColumn('TYPE', 'Type', 'TINYINT', false, null, null);
		$this->addColumn('LANDING_PAGE', 'LandingPage', 'VARCHAR', true, 512, '');
		$this->addColumn('FLAVOR_PARAM_ID', 'FlavorParamId', 'INTEGER', false, null, null);
		$this->addColumn('PLAYER_UICONF_ID', 'PlayerUiconfId', 'INTEGER', false, null, null);
		$this->addColumn('ALLOW_EMBED', 'AllowEmbed', 'BOOLEAN', false, null, true);
		$this->addColumn('ADULT_CONTENT', 'AdultContent', 'VARCHAR', false, 10, null);
		$this->addColumn('TRANSCODE_EXISTING_CONTENT', 'TranscodeExistingContent', 'BOOLEAN', false, null, false);
		$this->addColumn('ADD_TO_DEFAULT_CONVERSION_PROFILE', 'AddToDefaultConversionProfile', 'BOOLEAN', false, null, false);
		$this->addColumn('CATEGORIES', 'Categories', 'VARCHAR', false, 1024, null);
		$this->addColumn('FEED_DESCRIPTION', 'FeedDescription', 'VARCHAR', false, 1024, null);
		$this->addColumn('LANGUAGE', 'Language', 'VARCHAR', false, 5, null);
		$this->addColumn('FEED_LANDING_PAGE', 'FeedLandingPage', 'VARCHAR', false, 512, null);
		$this->addColumn('OWNER_NAME', 'OwnerName', 'VARCHAR', false, 50, null);
		$this->addColumn('OWNER_EMAIL', 'OwnerEmail', 'VARCHAR', false, 128, null);
		$this->addColumn('FEED_IMAGE_URL', 'FeedImageUrl', 'VARCHAR', false, 512, null);
		$this->addColumn('FEED_AUTHOR', 'FeedAuthor', 'VARCHAR', false, 50, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('DISPLAY_IN_SEARCH', 'DisplayInSearch', 'TINYINT', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // syndicationFeedTableMap
