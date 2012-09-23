<?php

/**
 * Subclass for performing query and update operations on the 'moderation_flag' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class moderationFlagPeer extends BasemoderationFlagPeer
{
	public static function doUpdateAll($selectCriteria , $values, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}
		return BasePeer::doUpdate($selectCriteria, $values, $con);		
	}
	
	public static function markAsModeratedByEntryId($partnerId, $entryId)
	{
		$c = new Criteria();
		$c->addAnd(moderationFlagPeer::PARTNER_ID, $partnerId);
		$c->addAnd(moderationFlagPeer::OBJECT_TYPE, moderationObjectType::ENTRY);
		$c->addAnd(moderationFlagPeer::FLAGGED_ENTRY_ID, $entryId);
		$c->addAnd(moderationFlagPeer::STATUS, moderationFlagStatus::PENDING);
		
		$update = new Criteria();
		$update->add(moderationFlagPeer::STATUS, moderationFlagStatus::MODERATED);
		self::doUpdateAll($c, $update);
	}
	
	public static function markAsModeratedByKUserId($partnerId, $kuserId)
	{
		$c = new Criteria();
		$c->addAnd(moderationFlagPeer::PARTNER_ID, $partnerId);
		$c->addAnd(moderationFlagPeer::OBJECT_TYPE, moderationObjectType::ENTRY);
		$c->addAnd(moderationFlagPeer::FLAGGED_KUSER_ID, $kuserId);
		$c->addAnd(moderationFlagPeer::STATUS, moderationFlagStatus::PENDING);
		
		$update = new Criteria();
		$update->add(moderationFlagPeer::STATUS, moderationFlagStatus::MODERATED);
		self::doUpdateAll($c, $update);
	}
}
