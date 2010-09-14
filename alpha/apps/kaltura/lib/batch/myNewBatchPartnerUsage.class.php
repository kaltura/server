<?php
require_once('myBatchBase.class.php');
//define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);

class myNewBatchPartnerUsage extends myBatchBase
{
	const SLEEP_TIME = 1;
	
	public function myNewBatchPartnerUsage()
	{
		self::initDb();
	}
	
	private function last_day_for_month( $dateParts )
	{
		$intDate = strtotime($dateParts[0].'-'.$dateParts[1].'-01');
		
		return date('t', $intDate);
	}
	
	/**
	 * $date is the working date, on which a new row will be inserted or existing row will be updated
	 */
	public function doDailyStorageAggregation( $date )
	{
		TRACE("calculating storage agg. for date $date");
		// set start points
		$partners_exists = true;
		$start_pos = 0;
		$bulk_size = 500;
		$errors = array();
		// loop partners
		while($partners_exists)
		{
			// pull bulk of partners
			$c = new Criteria();
			$c->addAnd(PartnerPeer::CREATED_AT, $date, Criteria::LESS_THAN);
			$c->setOffset($start_pos);
			$c->setLimit($bulk_size);
			$partners = PartnerPeer::doSelect($c);
			if (!$partners)
			{
				TRACE( "No more partners. offset: $start_pos , limit: $bulk_size ." );
				// set flag to exit while loop
				$partners_exists = false;
			} 
			else
			{
				TRACE( "Looping ". ($start_pos + $bulk_size -1) ." partners, offset: $start_pos ." );
				// loop bulk of partners
				foreach($partners as $partner)
				{
					$partner_id = $partner->getId();
					$query = 'SELECT sum(amount) as sumamount from partner_activity where partner_id = '.$partner_id;
					$query .= ' and activity = 3 and sub_activity = 301 and activity_date <= "'.$date.'"';
					$connection = Propel::getConnection();
					$statement = $connection->prepareStatement($query);
					try{
						$resultset = $statement->executeQuery();						
					}
					catch(Exception $ex)
					{
						$errors[] = array( $query, $ex);
					}
					
					while ($resultset->next()) { $total_hosting = $resultset->get('sumamount'); }
					if ( !$total_hosting ) $total_hosting = 0;
					TRACE('Partner '.$partner_id.' => has total hosting of '.$total_hosting.' as for ['.$date.']');

					$setQuery = 'INSERT INTO partner_activity(partner_id,activity,sub_activity,activity_date,amount,amount1) ';
					$setQuery .= " VALUES($partner_id , 3 , 301 , '$date' , 0 , $total_hosting) ON duplicate KEY UPDATE ";
					$setQuery .= " amount1 = $total_hosting";
					$statement = $connection->prepareStatement($setQuery);
					try{
						$resultset = $statement->executeQuery();
						//$result = $resultset->next();
						//$result = '';
						//var_dump($resultset);
						TRACE("updated/added row for partner $partner_id, with amount1 $total_hosting ");
					}
					catch(Exception $ex)
					{
						$errors[] = array( $setQuery, $ex);
					}
					//unset($todayActivity);
					//unset($activity);
					//unset($cSumStorage);
					//unset($cTodayStorage);					
				}
			}
			unset($partners);
			$start_pos += $bulk_size;
		}
		if(count($errors))
		{
			TRACE("errors occurred: ".print_r($errors,true));
		}
	}
	
	public function doMonthlyAggregation( $date )
	{
		// set the dates
		$dateParts = explode('-', $date);
		$currentDate = $dateParts;
		$currentDate[2] = $currentDate[2] - 3;
		if ($currentDate[2] <= 0)
		{
			$currentDate[1] = $currentDate[1] - 1;
			if ( $currentDate[1] == 0)
			{
				$currentDate[1] = 12;
				$currentDate[0] = $currentDate[0] - 1;
			}
			// if $currentDate[2] before reduction = 3, $currentDate[2] after reduction = 0
			// if $currentDate[2] = 0 and last_day_for_month return 30, $currentDate[2] = 30
			// if $currentDate[2] before reduction = 2, $currentDate[2] after reduction = -1
			// if $currentDate[2] = -1 and last_day_for_month return 30, $currentDate[2] = 30 + (-1) = 29
			$currentDate[2] = $this->last_day_for_month($currentDate) + $currentDate[2];
		}
		if ($currentDate[1] < 10 && strlen($currentDate[1]) == 1)
		{
			$currentDate[1] = '0'.$currentDate[1];
		}
		if ($currentDate[2] < 10 && strlen($currentDate[2]) == 1)
		{
			$currentDate[2] = '0'.$currentDate[2];
		}
		
		$firstOfMonth = $currentDate[0].'-'.$currentDate[1].'-01';		
		$currentDate = implode('-', $currentDate);
		TRACE("calculating monthly agg. for date $currentDate");
		
		// set start points
		$partners_exists = true;
		$start_pos = 0;
		$bulk_size = 500;
		
		// loop partners
		while($partners_exists)
		{
			// pull bulk of partners
			$c = new Criteria();
			$c->addAnd(PartnerPeer::CREATED_AT, $currentDate, Criteria::LESS_EQUAL);
			$c->setOffset($start_pos);
			$c->setLimit($bulk_size);
			$partners = PartnerPeer::doSelect($c);
			if (!$partners)
			{
				TRACE( "No more partners. offset: $start_pos , limit: $bulk_size ." );
				// set flag to exit while loop
				$partners_exists = false;
			} 
			else
			{		
				TRACE( "Looping ". ($start_pos + $bulk_size -1) ." partners, offset: $start_pos ." );
				// loop bulk of partners
				foreach($partners as $partner)
				{
					/*
					if ($partner->getId() != 593 && $partner->getId() != 395 && $partner->getId() != 387 )
						continue;

					TRACE("testing... not skiping partner ".$partner->getId());
					*/
					
					// get row from partner_activity where date is 1st of current month and type is 6
					$partnerActivityCriteria = new Criteria();
					$partnerActivityCriteria->addAnd ( PartnerActivityPeer::ACTIVITY_DATE, $firstOfMonth );
					$partnerActivityCriteria->addAnd ( PartnerActivityPeer::ACTIVITY , PartnerActivity::PARTNER_ACTIVITY_MONTHLY_AGGREGATION );
					$partnerActivityCriteria->addAnd ( PartnerActivityPeer::PARTNER_ID , $partner->getId() );
					$activityTotal = PartnerActivityPeer::doSelect( $partnerActivityCriteria );
					if (count($activityTotal) > 1)
					{
						TRACE( "loaded more than one monthly aggregation row for partner. something went wrong. partner ".$partner->getID() );
					}
					elseif (count($activityTotal) == 0 || !$activityTotal)
					{
						// no rows for this month, either today is 1st of month or new partner. adding row for partner
						$partnerActivity = new PartnerActivity;
						$partnerActivity->setActivity ( PartnerActivity::PARTNER_ACTIVITY_MONTHLY_AGGREGATION );
						$partnerActivity->setPartnerId ( $partner->getId() );
						$partnerActivity->setActivityDate ( $firstOfMonth );

						$storageTotal = $this->getStorageAggregationFor( $partner->getId() , $currentDate );
						$storageAddition = $storageTotal / date('t', strtotime($currentDate));
						$partnerActivity->setAmount1 ( $storageAddition );

						$partnerActivity->setAmount2 ( $this->getTrafficFor( $partner->getId() , $currentDate ) );

						$total_amount = (($partnerActivity->getAmount1()*1024) + $partnerActivity->getAmount2());
						$partnerActivity->setAmount ( $total_amount );
						$partnerActivity->save();						
					}
					else
					{
						$currentStorage = $activityTotal[0]->getAmount1();
						$storageTotal = $this->getStorageAggregationFor( $partner->getId() , $currentDate );
						$storageAddition = $storageTotal / date('t', strtotime($currentDate));
						$activityTotal[0]->setAmount1( $currentStorage + $storageAddition );
						
						$currentTraffic = $activityTotal[0]->getAmount2();
						$trafficAddition = $this->getTrafficFor( $partner->getId() , $currentDate );
						$activityTotal[0]->setAmount2( $currentTraffic + $trafficAddition );
						
						// storage is saved in MB, traffic is saved in KB, normalizing storage for correct sum result
						$total_amount = (($activityTotal[0]->getAmount1()*1024) + $activityTotal[0]->getAmount2());
						$activityTotal[0]->setAmount( $total_amount );
						$activityTotal[0]->save();
						
					}
					unset($partnerActivityCriteria);
					unset($activityTotal);
				}
			}
			unset($partners);
			$start_pos += $bulk_size;
		}
	}
	
	private function getStorageAggregationFor( $partnerId, $date )
	{
		$partnerActivityCriteria = new Criteria();
		$partnerActivityCriteria->addAnd ( PartnerActivityPeer::ACTIVITY_DATE, $date );
		$partnerActivityCriteria->addAnd ( PartnerActivityPeer::ACTIVITY , PartnerActivity::PARTNER_ACTIVITY_STORAGE );
		$partnerActivityCriteria->addAnd ( PartnerActivityPeer::SUB_ACTIVITY , PartnerActivity::PARTNER_SUB_ACTIVITY_STORAGE_SIZE );
		$partnerActivityCriteria->addAnd ( PartnerActivityPeer::PARTNER_ID , $partnerId );
		$activity = PartnerActivityPeer::doSelect( $partnerActivityCriteria );
		//TRACE("$partnerId and $date resulted in ".count($activity). " rows");
		
		if (count($activity))
		{
			return $activity[0]->getAmount1();
		}
		return 0;
	}
	
	private function getTrafficFor( $partnerId, $date )
	{
		$partnerActivityCriteria = new Criteria();
		$partnerActivityCriteria->addAnd ( PartnerActivityPeer::ACTIVITY_DATE, $date );
		$partnerActivityCriteria->addAnd ( PartnerActivityPeer::ACTIVITY , PartnerActivity::PARTNER_ACTIVITY_TRAFFIC );
		$partnerActivityCriteria->addAnd ( PartnerActivityPeer::SUB_ACTIVITY , 
			array( 	PartnerActivity::PARTNER_SUB_ACTIVITY_WWW,
				PartnerActivity::PARTNER_SUB_ACTIVITY_LIMELIGHT ),
			Criteria::IN );
		$partnerActivityCriteria->addAnd ( PartnerActivityPeer::PARTNER_ID , $partnerId );
		$activity = PartnerActivityPeer::doSelect( $partnerActivityCriteria );

		//TRACE("traffic ! $partnerId and $date resulted in ".count($activity). " rows");
		$_traffic = 0;
		if (count($activity) == 2)
		{
			$_traffic = $activity[0]->getAmount();
			//TRACE("DB value (act[0]) = ".$activity[0]->getAmount().' my value = '.$_traffic);
			$_traffic += $activity[1]->getAmount();
			//TRACE("DB value (act[1]) = ".$activity[1]->getAmount().' my value = '.$_traffic);
		}
		elseif (count($activity) == 1)
		{
			$_traffic = $activity[0]->getAmount();
			//TRACE("DB value (act[0] only) = ".$activity[0]->getAmount().' my value = '.$_traffic);
		}
		
		return $_traffic;
	}	

}

?>