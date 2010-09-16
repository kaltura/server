<?php

/**
 * Subclass for performing query and update operations on the 'email_ingestion_profile' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EmailIngestionProfilePeer extends BaseEmailIngestionProfilePeer
{
	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL3);
		
		return $con;
	}
	
	public static function retrieveByEmailAddressNoFilter($emailAddress)
	{
		$c = new Criteria();
		$c->addAnd(self::EMAIL_ADDRESS, $emailAddress);
		
		// there should always be one because the field in the DB is unique
		return self::doSelectOne($c);
	}
}
