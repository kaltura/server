<?php

/**
 * Subclass for representing a row from the 'partner_activity' table.
 *
 * 
 *
 * @package lib.model
 */ 
class PartnerActivity extends BasePartnerActivity
{
	const PARTNER_ACTIVITY_TRAFFIC = 1;		// KB
	const PARTNER_ACTIVITY_KDP = 2;
	const PARTNER_ACTIVITY_STORAGE = 3;		// MB
	const PARTNER_ACTIVITY_MEDIA = 4;
	const PARTNER_ACTIVITY_USER = 5;
	const PARTNER_ACTIVITY_MONTHLY_AGGREGATION = 6;
	const PARTNER_ACTIVITY_FMS_BW = 7; 		// KB to have the same units as traffic
	
	const PARTNER_SUB_ACTIVITY_WWW = 1;
	const PARTNER_SUB_ACTIVITY_LIMELIGHT = 2;
	const PARTNER_SUB_ACTIVITY_LEVEL3 = 3;
	const PARTNER_SUB_ACTIVITY_AKAMAI = 4;
	
	const PARTNER_SUB_ACTIVITY_KDP_PLAYS = 201;
	const PARTNER_SUB_ACTIVITY_KDP_VIEWS = 202;
	
	const PARTNER_SUB_ACTIVITY_STORAGE_SIZE = 301;
	const PARTNER_SUB_ACTIVITY_STORAGE_COUNT = 302;
	
	const PARTNER_SUB_ACTIVITY_MEDIA = 401;
	const PARTNER_SUB_ACTIVITY_USER = 501;
	
	const PARTNER_FIELD_MEDIA_VIDEO = 1;
	const PARTNER_FIELD_MEDIA_AUDIO = 2;
	const PARTNER_FIELD_MEDIA_IMAGE = 3;
	const PARTNER_FIELD_MEDIA_RC = 4;
	const PARTNER_FIELD_MEDIA_WIDGET = 5;
	
	const PARTNER_FIELD_USER_CONTRIB = 1;
	const PARTNER_FIELD_USER_TOTAL_CONTRIB = 2;
	
	static public function incrementActivity($partner_id, $activity, $sub_activity, $amount = 1, $day = null, $field = null)
	{
		if (!$day)
			$day = date("Y-m-d");
			
		$field_name = $field ? PartnerActivityPeer::AMOUNT.$field : PartnerActivityPeer::AMOUNT;
		
/*		
		$query = "INSERT INTO ".PartnerActivityPeer::TABLE_NAME." (".
			PartnerActivityPeer::PARTNER_ID.",".
			PartnerActivityPeer::ACTIVITY_DATE.",".
			PartnerActivityPeer::ACTIVITY.",".
			PartnerActivityPeer::SUB_ACTIVITY.",".
			$field_name.") VALUES ".
			"($partner_id,'$day',$activity,$sub_activity,$amount) ON DUPLICATE KEY UPDATE ".
			"$field_name=$field_name+$amount";

		$connection = Propel::getConnection();

		$statement = $connection->prepareStatement($query);
	    $resultset = $statement->executeQuery();
*/
		
		$connection = Propel::getConnection();
		
		$crit = new Criteria();
		$crit->addAnd(PartnerActivityPeer::PARTNER_ID, $partner_id);
		$crit->addAnd(PartnerActivityPeer::ACTIVITY_DATE, $day);
		$crit->addAnd(PartnerActivityPeer::ACTIVITY, $activity);
		$crit->addAnd(PartnerActivityPeer::SUB_ACTIVITY, $sub_activity);
		$crit->addSelectColumn(PartnerActivityPeer::ID);
		$resultset = PartnerActivityPeer::doSelectOne($crit, $connection);
/*
		$query = "SELECT ID FROM ".PartnerActivityPeer::TABLE_NAME." WHERE ".
			PartnerActivityPeer::PARTNER_ID."=$partner_id AND ".
			PartnerActivityPeer::ACTIVITY_DATE."='$day' AND ".
			PartnerActivityPeer::ACTIVITY."=$activity AND ".
			PartnerActivityPeer::SUB_ACTIVITY."=$sub_activity";
		
		$statement = $connection->prepare($query);
	    $resultset = $statement->execute();
*/	    
	    // if the key exists do an update command
	    if ($resultset)
	    {
	    	$id = $resultset->getInt('ID');
			$updateCommand = "UPDATE ".PartnerActivityPeer::TABLE_NAME." SET $field_name=$field_name+$amount WHERE ".PartnerActivityPeer::ID."=$id";
	    }
	    else
	    {
		    $updateCommand = "INSERT INTO ".PartnerActivityPeer::TABLE_NAME." (".
				PartnerActivityPeer::PARTNER_ID.",".
				PartnerActivityPeer::ACTIVITY_DATE.",".
				PartnerActivityPeer::ACTIVITY.",".
				PartnerActivityPeer::SUB_ACTIVITY.",".
				$field_name.") VALUES ".
				"($partner_id,'$day',$activity,$sub_activity,$amount)";
	    }
	    
		$statement = $connection->prepare($updateCommand);
    	$resultset = $statement->execute();
	}
}
