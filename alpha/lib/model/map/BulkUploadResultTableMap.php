<?php


/**
 * This class defines the structure of the 'bulk_upload_result' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.model.map
 */
class BulkUploadResultTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.BulkUploadResultTableMap';

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
		$this->setName('bulk_upload_result');
		$this->setPhpName('BulkUploadResult');
		$this->setClassname('BulkUploadResult');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('BULK_UPLOAD_JOB_ID', 'BulkUploadJobId', 'INTEGER', false, null, null);
		$this->addColumn('LINE_INDEX', 'LineIndex', 'INTEGER', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', false, 20, null);
		$this->addColumn('ENTRY_STATUS', 'EntryStatus', 'INTEGER', false, null, null);
		$this->addColumn('ROW_DATA', 'RowData', 'VARCHAR', false, 1023, null);
		$this->addColumn('TITLE', 'Title', 'VARCHAR', false, 127, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 255, null);
		$this->addColumn('TAGS', 'Tags', 'VARCHAR', false, 255, null);
		$this->addColumn('URL', 'Url', 'VARCHAR', false, 255, null);
		$this->addColumn('CONTENT_TYPE', 'ContentType', 'VARCHAR', false, 31, null);
		$this->addColumn('CONVERSION_PROFILE_ID', 'ConversionProfileId', 'INTEGER', false, null, null);
		$this->addColumn('ACCESS_CONTROL_PROFILE_ID', 'AccessControlProfileId', 'INTEGER', false, null, null);
		$this->addColumn('CATEGORY', 'Category', 'VARCHAR', false, 128, null);
		$this->addColumn('SCHEDULE_START_DATE', 'ScheduleStartDate', 'TIMESTAMP', false, null, null);
		$this->addColumn('SCHEDULE_END_DATE', 'ScheduleEndDate', 'TIMESTAMP', false, null, null);
		$this->addColumn('THUMBNAIL_URL', 'ThumbnailUrl', 'VARCHAR', false, 255, null);
		$this->addColumn('THUMBNAIL_SAVED', 'ThumbnailSaved', 'BOOLEAN', false, null, null);
		$this->addColumn('PARTNER_DATA', 'PartnerData', 'VARCHAR', false, 4096, null);
		$this->addColumn('ERROR_DESCRIPTION', 'ErrorDescription', 'VARCHAR', false, 255, null);
		$this->addColumn('PLUGINS_DATA', 'PluginsData', 'VARCHAR', false, 9182, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // BulkUploadResultTableMap
