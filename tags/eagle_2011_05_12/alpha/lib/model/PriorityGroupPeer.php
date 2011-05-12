<?php

/**
 * Subclass for performing query and update operations on the 'priority_group' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class PriorityGroupPeer extends BasePriorityGroupPeer
{
	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL3);
		
		return $con;
	}
}
