<?php


/**
 * Skeleton subclass for performing query and update operations on the 'resource_user' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.schedule
 * @subpackage model
 */
class ResourceUserPeer extends BaseResourceUserPeer {

	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c =  KalturaCriteria::create(self::OM_CLASS);
		$c->addAnd ( self::STATUS, array(ResourceUserStatus::DELETED), Criteria::NOT_IN);
		$partnerId = kCurrentContext::getCurrentPartnerId();
		if($partnerId)
			$c->add(self::PARTNER_ID, $partnerId);

		self::$s_criteria_filter->setFilter($c);
	}

	public function retrieveByResourceTagAndKuserId($resourceTag, $kuserId)
	{
		$criteria = new Criteria();
		$criteria->addAnd(self::RESOURCE_TAG, $resourceTag);
		$criteria->addAnd(self::KUSER_ID, $kuserId);

		return self::doSelectOne($criteria);

	}

} // ResourceUserPeer
