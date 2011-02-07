<?php


/**
 * This class defines the structure of the 'mail_job' table.
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
class MailJobTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.MailJobTableMap';

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
		$this->setName('mail_job');
		$this->setPhpName('MailJob');
		$this->setClassname('MailJob');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('MAIL_TYPE', 'MailType', 'SMALLINT', false, null, null);
		$this->addColumn('MAIL_PRIORITY', 'MailPriority', 'SMALLINT', false, null, null);
		$this->addColumn('RECIPIENT_NAME', 'RecipientName', 'VARCHAR', false, 64, null);
		$this->addColumn('RECIPIENT_EMAIL', 'RecipientEmail', 'VARCHAR', false, 64, null);
		$this->addForeignKey('RECIPIENT_ID', 'RecipientId', 'INTEGER', 'kuser', 'ID', false, null, null);
		$this->addColumn('FROM_NAME', 'FromName', 'VARCHAR', false, 64, null);
		$this->addColumn('FROM_EMAIL', 'FromEmail', 'VARCHAR', false, 64, null);
		$this->addColumn('BODY_PARAMS', 'BodyParams', 'VARCHAR', false, 2048, null);
		$this->addColumn('SUBJECT_PARAMS', 'SubjectParams', 'VARCHAR', false, 512, null);
		$this->addColumn('TEMPLATE_PATH', 'TemplatePath', 'VARCHAR', false, 512, null);
		$this->addColumn('CULTURE', 'Culture', 'TINYINT', false, null, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CAMPAIGN_ID', 'CampaignId', 'INTEGER', false, null, null);
		$this->addColumn('MIN_SEND_DATE', 'MinSendDate', 'TIMESTAMP', false, null, null);
		$this->addColumn('SCHEDULER_ID', 'SchedulerId', 'INTEGER', false, null, null);
		$this->addColumn('WORKER_ID', 'WorkerId', 'INTEGER', false, null, null);
		$this->addColumn('BATCH_INDEX', 'BatchIndex', 'INTEGER', false, null, null);
		$this->addColumn('PROCESSOR_EXPIRATION', 'ProcessorExpiration', 'TIMESTAMP', false, null, null);
		$this->addColumn('EXECUTION_ATTEMPTS', 'ExecutionAttempts', 'TINYINT', false, null, null);
		$this->addColumn('LOCK_VERSION', 'LockVersion', 'INTEGER', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, 0);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('DC', 'Dc', 'VARCHAR', false, 2, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('kuser', 'kuser', RelationMap::MANY_TO_ONE, array('recipient_id' => 'id', ), null, null);
	} // buildRelations()

} // MailJobTableMap
