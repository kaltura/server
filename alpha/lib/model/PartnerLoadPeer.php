<?php


/**
 * Skeleton subclass for performing query and update operations on the 'partner_load' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class PartnerLoadPeer extends BasePartnerLoadPeer {

	public static function updatePartnerLoad($partnerId, $jobType, $jobSubType = null, PropelPDO $con = null) 
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$priorityFactor = PartnerPeer::getPartnerPriorityFactorByPartner($partner);
		$maxQuota		= $partner->getJobTypeQuota($jobType, $jobSubType);
		if(!$maxQuota)
			$maxQuota = BatchJobLockPeer::getMaxJobsForPartner($jobType);
		
		$dcId = kDataCenterMgr::getCurrentDcId();
		
		// Hack to avoid the not-null constaint on job sub type
		if(is_null($jobSubType))
			$jobSubType = 0;
		
		$c = new Criteria();
		$c->add ( self::PARTNER_ID , $partnerId );
		$c->add ( self::JOB_TYPE , $jobType );
		$c->add ( self::JOB_SUB_TYPE, $jobSubType); 
		$c->add ( self::DC, $dcId);
		
		$oldPartnerLoad = self::doSelectOne( $c );
		
		if($oldPartnerLoad === null) {
			try {
				// Try to insert new entry
				$partnerLoad = new PartnerLoad();
				$partnerLoad->setPartnerId($partnerId);
				$partnerLoad->setJobType($jobType);
				$partnerLoad->setJobSubType($jobSubType);
				$partnerLoad->setPartnerLoad(1);
				$partnerLoad->setDc($dcId);
				$partnerLoad->setWeightedPartnerLoad($priorityFactor);
				$partnerLoad->setQuota($maxQuota - 1);
				
				$res = $partnerLoad->save();
				if($res == 1) {
					return; // if we arrived here, it means the insert was successful
				}
			} catch (Exception $e) {
				// probably a unique constraint - use the updae version below
			}
		}
		
		$table = PartnerLoadPeer::TABLE_NAME;
		$colPartnerLoad = PartnerLoadPeer::PARTNER_LOAD;
		$colWeightedPartnerLoad = PartnerLoadPeer::WEIGHTED_PARTNER_LOAD;
		$colJobType = PartnerLoadPeer::JOB_TYPE;
		$colJobSubType = PartnerLoadPeer::JOB_SUB_TYPE;
		$colPartnerId = PartnerLoadPeer::PARTNER_ID;
		$colDC = PartnerLoadPeer::DC;
		$colQuota = PartnerLoadPeer::QUOTA;
		
		$sql = "UPDATE $table ";
		$sql .= "SET $colPartnerLoad = ($colPartnerLoad + 1)";
		$sql .= ", $colWeightedPartnerLoad = ($colWeightedPartnerLoad + $priorityFactor)";
		$sql .= ", $colQuota = ($colQuota - 1)";
		$sql .= "WHERE $colJobType = $jobType ";
		$sql .= "AND $colJobSubType = $jobSubType ";
		$sql .= "AND $colPartnerId = $partnerId ";
		$sql .= "AND $colDC = $dcId ";
		
		try {
			$affectedRows = $con->exec($sql);
		} catch (Exception $e) {
			KalturaLog::err("Failed to update partner load with error : " . $e->getMessage());
		}
		
	}
	
	/**
	 * This function queies the batch job lock table and calculates for each partnerID and job type
	 * its partner load and weigthed partner load. it returns the information as a Map where : 
	 * key - partnerId#jobType#jobSubType
	 * value - partnerLoad
	 */
	public static function getPartnerLoads()
	{
		$dcId = kDataCenterMgr::getCurrentDcId();
		$c = new Criteria();
		$c->add(BatchJobLockPeer::WORKER_ID, null, Criteria::ISNOTNULL);
		$c->add(BatchJobLockPeer::DC, $dcId);
		$c->addGroupByColumn(BatchJobLockPeer::PARTNER_ID);
		$c->addGroupByColumn(BatchJobLockPeer::JOB_TYPE);
		$c->addGroupByColumn(BatchJobLockPeer::JOB_SUB_TYPE);
		$c->addSelectColumn(BatchJobLockPeer::COUNT);
	
		foreach($c->getGroupByColumns() as $column)
			$c->addSelectColumn($column);
	
		$stmt = BatchJobLockPeer::doSelectStmt($c);
	
		$partnerLoads = array();
		$rows= $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		foreach ($rows as $row) {
	
			$partnerId = $row['PARTNER_ID'];
			$jobType = $row['JOB_TYPE'];
			$jobSubType = $row['JOB_SUB_TYPE'];
			if(is_null($jobSubType)) 
				$jobSubType = 0;
			$jobCount = $row[BatchJobLockPeer::COUNT];
	
			$priorityFactor = PartnerPeer::getPartnerPriorityFactor($partnerId);
			$key = $partnerId . "#" . $jobType . "#" . $jobSubType;
	
			$partnerLoad = new PartnerLoad();
			$partnerLoad->setPartnerId($partnerId);
			$partnerLoad->setJobType($jobType);
			$partnerLoad->setJobSubType($jobSubType);
			$partnerLoad->setDc($dcId);
			$partnerLoad->setPartnerLoad($jobCount);
			$partnerLoad->setWeightedPartnerLoad($jobCount * $priorityFactor);
			$partnerLoads[$key] = $partnerLoad;
		}
	
		return $partnerLoads;
	}
	
	
	public static function updatePartnerLoadTable() {
		$actualPartnerLoads = PartnerLoadPeer::getPartnerLoads();
		$c = new Criteria();
		$c->add(PartnerLoadPeer::DC, kDataCenterMgr::getCurrentDcId());
		$currentPartnerLoads = PartnerLoadPeer::doSelect($c);
			
		
		// This loop updates the partner load table contents according to the 
		// accurate information gathered from the batch job table
		foreach ($currentPartnerLoads as $partnerLoad) {
			
			$maxQuota = self::getMaxQuotaForPartner ($partnerLoad);
			$key = $partnerLoad->getPartnerId() . "#" . $partnerLoad->getJobType() . "#" . $partnerLoad->getJobSubType();
			
			if(array_key_exists($key, $actualPartnerLoads)) {
				$actualLoad = $actualPartnerLoads[$key];
				// Update
				$partnerLoad->setPartnerLoad($actualLoad->getPartnerLoad());
				$partnerLoad->setWeightedPartnerLoad($actualLoad->getWeightedPartnerLoad());
				$partnerLoad->setQuota($maxQuota - $actualLoad->getPartnerLoad());
				$partnerLoad->save();
					
				unset($actualPartnerLoads[$key]);
			} else {
					
				// Delete
				$partnerLoad->delete();
			}
		}
			
		foreach($actualPartnerLoads as $actualPartnerLoad) {
			$maxQuota = self::getMaxQuotaForPartner ($actualPartnerLoad);

			$actualPartnerLoad->setQuota($maxQuota - $actualPartnerLoad->getPartnerLoad());
			$actualPartnerLoad->save();
		}
	}
	
	
	private static function getMaxQuotaForPartner($partnerLoadRecord) {
		// Insert
		$partner = PartnerPeer::retrieveByPK($partnerLoadRecord->getPartnerId());
		if (!$partner)
			return 0;
		$maxQuota = $partner->getJobTypeQuota($partnerLoadRecord->getJobType(), $partnerLoadRecord->getJobSubType());
		if(!$maxQuota)
			$maxQuota = BatchJobLockPeer::getMaxJobsForPartner($partnerLoadRecord->getJobType());
		return $maxQuota;
	}

	
} // PartnerLoadPeer
