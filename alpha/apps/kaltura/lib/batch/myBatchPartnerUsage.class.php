<?php
/**
 * @package Core
 * @subpackage Server-Batch
 */
class myBatchPartnerUsage extends myBatchBase
{
	const SLEEP_TIME = 1;
	
	public function __construct($partnerId = null, $partnerPackage = 1)
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
			
			$c->addAnd(PartnerPeer::PARTNER_PACKAGE, $partnerPackage); 
			$c->addAnd(PartnerPeer::MONITOR_USAGE, 1);
			$c->addAnd(PartnerPeer::STATUS, Partner::PARTNER_STATUS_DELETED, Criteria::NOT_EQUAL);
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
					if($partnerPackage == 1) //free
					{
						myPartnerUtils::doPartnerUsage($partner, true);
					}
					else if($partnerPackage == 100) //monthly developer
					{
						myPartnerUtils::doMonthlyPartnerUsage($partner);
					}
				}
			}
			unset($partners);
			PartnerPeer::clearInstancePool();
			$start_pos += $bulk_size;
		}
	}

}
