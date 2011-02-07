<?php


/**
 * This class defines the structure of the 'ui_conf' table.
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
class uiConfTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.uiConfTableMap';

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
		$this->setName('ui_conf');
		$this->setPhpName('uiConf');
		$this->setClassname('uiConf');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('OBJ_TYPE', 'ObjType', 'SMALLINT', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('SUBP_ID', 'SubpId', 'INTEGER', false, null, null);
		$this->addColumn('CONF_FILE_PATH', 'ConfFilePath', 'VARCHAR', false, 128, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 128, null);
		$this->addColumn('WIDTH', 'Width', 'VARCHAR', false, 10, null);
		$this->addColumn('HEIGHT', 'Height', 'VARCHAR', false, 10, null);
		$this->addColumn('HTML_PARAMS', 'HtmlParams', 'VARCHAR', false, 256, null);
		$this->addColumn('SWF_URL', 'SwfUrl', 'VARCHAR', false, 256, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CONF_VARS', 'ConfVars', 'VARCHAR', false, 4096, null);
		$this->addColumn('USE_CDN', 'UseCdn', 'TINYINT', false, null, null);
		$this->addColumn('TAGS', 'Tags', 'LONGVARCHAR', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 4096, null);
		$this->addColumn('DISPLAY_IN_SEARCH', 'DisplayInSearch', 'TINYINT', false, null, null);
		$this->addColumn('CREATION_MODE', 'CreationMode', 'TINYINT', false, null, null);
		$this->addColumn('VERSION', 'Version', 'VARCHAR', false, 10, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('widget', 'widget', RelationMap::ONE_TO_MANY, array('id' => 'ui_conf_id', ), null, null);
	} // buildRelations()

} // uiConfTableMap
