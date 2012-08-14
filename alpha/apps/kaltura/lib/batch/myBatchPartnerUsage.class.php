<?php
/**
 * @package Core
 * @subpackage Server-Batch
 */
class myBatchPartnerUsage extends myBatchBase
{
	const SLEEP_TIME = 1;
	
	public function myBatchPartnerUsage($partnerId = null)
	{
		self::initDb();
		$partners_exists = true;
		$start_pos = 0;
		$bulk_size = 500;
		while($partners_exists)
		{
			$c = new Criteria();
			if(!is_null($partnerId))
			{
				$c->addAnd(PartnerPeer::ID, $partnerId);
			}
			
			$c->addAnd(PartnerPeer::PARTNER_PACKAGE, 1); // get only free partners
			$c->addAnd(PartnerPeer::MONITOR_USAGE, 1);
			$c->addAnd(PartnerPeer::STATUS, Partner::PARTNER_STATUS_DELETED, CRITERIA::NOT_EQUAL);
			$c->setOffset($start_pos);
			$c->setLimit($bulk_size);
			$partners = PartnerPeer::doSelect($c);
			if (!$partners)
			{
				KalturaLog::debug( "No more partners. offset: $start_pos , limit: $bulk_size ." );
				$partners_exists = false;
			} 
			else
			{
				KalturaLog::debug( "Looping ". ($start_pos + $bulk_size -1) ." partners, offset: $start_pos ." );
				foreach($partners as $partner)
				{
					myPartnerUtils::doPartnerUsage($partner, true);
				}
			}
			unset($partners);
			PartnerPeer::clearInstancePool();
			$start_pos += $bulk_size;
		}
	}

}
