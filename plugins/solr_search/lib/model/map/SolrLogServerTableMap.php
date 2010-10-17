<?php


/**
 * This class defines the structure of the 'solr_log_server' table.
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
class SolrLogServerTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.SolrLogServerTableMap';

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
		$this->setName('solr_log_server');
		$this->setPhpName('SolrLogServer');
		$this->setClassname('SolrLogServer');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('SERVER', 'Server', 'VARCHAR', false, 63, null);
		$this->addColumn('DC', 'Dc', 'INTEGER', false, null, null);
		$this->addForeignKey('LAST_LOG_ID', 'LastLogId', 'INTEGER', 'solr_log', 'ID', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('SolrLog', 'SolrLog', RelationMap::MANY_TO_ONE, array('last_log_id' => 'id', ), null, null);
	} // buildRelations()

} // SolrLogServerTableMap
