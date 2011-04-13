<?php
require_once('myBatchBase.class.php');
define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);

class myBatchPartnerUsage extends myBatchBase
{
	const SLEEP_TIME = 1;
	
	public function myBatchPartnerUsage()
	{
		self::initDb();
		$partners_exists = true;
		$start_pos = 0;
		$bulk_size = 500;
		while($partners_exists)
		{
			$c = new Criteria();
			// get only free partners
			$c->addAnd(PartnerPeer::PARTNER_PACKAGE, 1);
			$c->addAnd(PartnerPeer::MONITOR_USAGE, 1);
			$c->setOffset($start_pos);
			$c->setLimit($bulk_size);
			$partners = PartnerPeer::doSelect($c);
			if (!$partners)
			{
				TRACE( "No more partners. offset: $start_pos , limit: $bulk_size ." );
				$partners_exists = false;
			} 
			else
			{
				TRACE( "Looping ". ($start_pos + $bulk_size -1) ." partners, offset: $start_pos ." );
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

?>