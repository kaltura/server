<?php
/**
 * @package Core
 * @subpackage Server-Batch
 */
class myBatchPartnerUsage extends myBatchBase
{
	const SLEEP_TIME = 1;
	
	public function __construct($partnerId = null, $partnerPackage = PartnerPackages::PARTNER_PACKAGE_FREE)
	{
		self::initDb();
		$partners_exists = true;
		$bulk_size = 500;
		$highest_partner_id = 100;
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

			$c->addAnd(PartnerPeer::ID, $highest_partner_id, Criteria::GREATER_THAN);
			$c->addAscendingOrderByColumn(PartnerPeer::ID);
			$c->setLimit($bulk_size);
			$partners = PartnerPeer::doSelect($c);
			$freeTrialTypes = array(PartnerPackages::PARTNER_PACKAGE_FREE, PartnerPackages::PARTNER_PACKAGE_DEVELOPER_TRIAL);

			if (!$partners)
			{
				KalturaLog::debug( "No more partners." );
				$partners_exists = false;
			} 
			else
			{
				KalturaLog::debug( "Looping ". count($partners) ." partners" );
				foreach($partners as $partner)
				{
					if(in_array($partnerPackage, $freeTrialTypes) && myPartnerUtils::isPartnerCreatedAsMonitoredFreeTrial($partner))
					{
						myPartnerUtils::doPartnerUsage($partner);
						myPartnerUtils::handleDayInFreeTrial($partner);
					}
					else if ($partnerPackage == PartnerPackages::PARTNER_PACKAGE_FREE)
						myPartnerUtils::doPartnerUsage($partner);
					//else if ($partnerPackage == PartnerPackages::PARTNER_PACKAGE_DEVELOPER_TRIAL)
					//	myPartnerUtils::doMonthlyPartnerUsage($partner);
				}
			}
			$partner = end($partners);
			if($partner)
				$highest_partner_id = $partner->getId();
			unset($partners);
			PartnerPeer::clearInstancePool();
		}
	}

}
