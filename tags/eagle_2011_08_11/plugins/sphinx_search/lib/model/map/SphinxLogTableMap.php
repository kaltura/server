<?php


/**
 * This class defines the structure of the 'sphinx_log' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.sphinxSearch
 * @subpackage model.map
 */
class SphinxLogTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.sphinxSearch.SphinxLogTableMap';

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
		$this->setName('sphinx_log');
		$this->setPhpName('SphinxLog');
		$this->setClassname('SphinxLog');
		$this->setPackage('plugins.sphinxSearch');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', false, 20, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, 0);
		$this->addColumn('DC', 'Dc', 'INTEGER', false, null, null);
		$this->addColumn('SQL', 'Sql', 'CLOB', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('SphinxLogServer', 'SphinxLogServer', RelationMap::ONE_TO_MANY, array('id' => 'last_log_id', ), null, null);
	} // buildRelations()

} // SphinxLogTableMap
