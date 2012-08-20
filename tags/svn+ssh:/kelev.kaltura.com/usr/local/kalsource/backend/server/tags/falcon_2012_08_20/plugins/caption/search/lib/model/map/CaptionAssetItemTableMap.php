<?php


/**
 * This class defines the structure of the 'caption_asset_item' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.captionSearch
 * @subpackage model.map
 */
class CaptionAssetItemTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.captionSearch.CaptionAssetItemTableMap';

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
		$this->setName('caption_asset_item');
		$this->setPhpName('CaptionAssetItem');
		$this->setClassname('CaptionAssetItem');
		$this->setPackage('plugins.captionSearch');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', false, 20, null);
		$this->addColumn('CAPTION_ASSET_ID', 'CaptionAssetId', 'VARCHAR', false, 20, null);
		$this->addColumn('CONTENT', 'Content', 'VARCHAR', false, 255, null);
		$this->addColumn('START_TIME', 'StartTime', 'INTEGER', false, null, null);
		$this->addColumn('END_TIME', 'EndTime', 'INTEGER', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // CaptionAssetItemTableMap
