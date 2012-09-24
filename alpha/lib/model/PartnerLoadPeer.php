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

	public static function updatePartnerLoad($jobType, $partnerId, $urgency, PropelPDO $con = null) 
	{
		$priorityFactor = PartnerPeer::getPartnerPriorityFactor($partnerId, $urgency);
		
		$c = new Criteria();
		$c->add ( self::PARTNER_ID , $partnerId );
		$c->add ( self::JOB_TYPE , $jobType );
		
		$oldPartnerLoad = self::doSelectOne( $c );
		
		if($oldPartnerLoad === null) {
			try {
				// Try to insert new entry
				$partnerLoad = new PartnerLoad();
				$partnerLoad->setPartnerId($partnerId);
				$partnerLoad->setJobType($jobType);
				$partnerLoad->setPartnerLoad(1);
				$partnerLoad->setWeightedPartnerLoad($priorityFactor);
				
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
		$colPartnerId = PartnerLoadPeer::PARTNER_ID;
		
		$sql = "UPDATE $table ";
		$sql .= "SET $colPartnerLoad = ($colPartnerLoad + 1)";
		$sql .= ", $colWeightedPartnerLoad = ($colWeightedPartnerLoad + $priorityFactor)";
		$sql .= "WHERE $colJobType = $jobType ";
		$sql .= "AND $colPartnerId = $partnerId ";
		
		$sql .= "LIMIT 1";
			
		try {
			$affectedRows = $con->exec($sql);
		} catch (Exception $e) {
			KalturaLog::error("Failed to update partner load with error : " . $e->getMessage());
		}
		
	}
	
	
} // PartnerLoadPeer
