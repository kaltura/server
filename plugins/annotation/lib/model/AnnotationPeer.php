<?php


/**
 * Skeleton subclass for performing query and update operations on the 'annotation' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.annotation
 * @subpackage model
 */
class AnnotationPeer extends BaseAnnotationPeer {
	const MAX_ANNOTATION_TEXT = 32700;
	const MAX_ANNOTATION_TAGS = 255;
	
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->addAnd(AnnotationPeer::STATUS, AnnotationStatus::ANNOTATION_STATUS_READY);
		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function setDefaultCriteriaFilterByKuser()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$puserId = kCurrentContext::$ks_uid;
		$partnerId = kCurrentContext::$partner_id;
		if ($puserId && $partnerId)
		{
			$kuserId = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
			$c->addAnd(AnnotationPeer::KUSER_ID, $kuserId->getId());
		}
		self::$s_criteria_filter->setFilter($c);
	}
		
} // AnnotationPeer
