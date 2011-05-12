<?php


/**
 * Skeleton subclass for performing query and update operations on the 'dwh_hourly_partner' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.partnerAggregation
 * @subpackage model
 */
class DwhHourlyPartnerPeer extends BaseDwhHourlyPartnerPeer {

	public static function alternativeCon($con)
	{
		return myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_DWH);
	}
	
} // DwhHourlyPartnerPeer
