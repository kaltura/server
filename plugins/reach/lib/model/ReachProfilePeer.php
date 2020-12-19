<?php


/**
 * Skeleton subclass for performing query and update operations on the 'reach_profile' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.reach
 * @subpackage model
 */
class ReachProfilePeer extends BaseReachProfilePeer
{
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = KalturaCriteria::create(VendorCatalogItemPeer::OM_CLASS);
		$c->addAnd ( ReachProfilePeer::STATUS, ReachProfileStatus::DELETED, Criteria::NOT_EQUAL);
		
		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function retrieveActiveByPk($pk)
	{
		$c = new Criteria();
		$c->add(ReachProfilePeer::ID, $pk);
		$c->add(ReachProfilePeer::STATUS, ReachProfileStatus::ACTIVE);
		
		return ReachProfilePeer::doSelectOne($c);
	}
	
	public static function retrieveByPartnerId($partnerId)
	{
		$c = new Criteria();
		$c->add(ReachProfilePeer::PARTNER_ID, $partnerId);
		$c->add(ReachProfilePeer::STATUS, ReachProfileStatus::ACTIVE);
		
		return ReachProfilePeer::doSelect($c);
	}
	
	public static function updateUsedCredit($reachProfileId, $value)
	{
		if($value==0)
		{
			return;
		}
		$connection = Propel::getConnection();
		
		$updateSql = "UPDATE ".ReachProfilePeer::TABLE_NAME." SET " .
			ReachProfilePeer::USED_CREDIT . " = " . ReachProfilePeer::USED_CREDIT . " +$value WHERE " .
			ReachProfilePeer::ID . "=" . $reachProfileId . ";";
		
		$stmt = $connection->prepare($updateSql);
		$stmt->execute();
		KalturaLog::debug("Successfully updated vendor credit for profile Id [$reachProfileId]");
		
		$reachProfile = ReachProfilePeer::retrieveByPK($reachProfileId);
		$reachProfile->syncCreditPercentageUsage();
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("reachProfile:id=%s", self::ID));
	}
} // ReachProfilePeer
