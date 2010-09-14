<?php
class myModerationMgr
{
	public static function addToModerationList ( $object , $moderator_kuser_id = null , $status = null )
	{
		if ( $object instanceof entry)
		{
			$entry = $object;
			$moderation = new moderation();
			$moderation->setPartnerId( $entry->getPartnerId() );
			$moderation->setObjectId( $entry->getId() );
			$moderation->setObjectType( moderation::MODERATION_OBJECT_TYPE_ENTRY );
			if ( $status == null )
				$moderation->setStatus(moderation::MODERATION_STATUS_PENDING);
			else
				$moderation->setStatus( $status );
			$moderation->save();
			 
		}
		elseif ( $object instanceof kshow)
		{
			throw new exception ( "TO BE IMPLEMENTED addToModerationList - for type kshow");
		}
		else
		{
			// ERROR !
			throw new exception ( "TO BE IMPLEMENTED addToModerationList - for type unknown type");
		}
	}
	
	/**
	 * update all moderations for the object defined by object that has the status PENDING OR REVIEW
	 *
	 * @param Criteria $object
	 * @param Criteria $status
	 * @return void
	 */
	public static function updateModerationsForObject ( $object , $status  )
	{
		if ( $object instanceof entry)
		{
			$entry = $object;
			$c = new Criteria();
			$c->add ( moderationPeer::OBJECT_ID , $entry->getId() );
			$c->add ( moderationPeer::OBJECT_TYPE , moderation::MODERATION_OBJECT_TYPE_ENTRY );
			$crit = $c->getNewCriterion( moderationPeer::STATUS , moderation::MODERATION_STATUS_PENDING  ) ;
			$crit->addOr ( $c->getNewCriterion( moderationPeer::STATUS , moderation::MODERATION_STATUS_REVIEW  ) ) ;
			$c->add ( $crit );
//			$c->add ( moderationPeer::PARTNER_ID , $entry->getPartnerId() ); // 

			$new_status = new Criteria();
			$new_status->add ( moderationPeer::STATUS , $status );
			
			// this will update the moderation objects WITHOUT going through save() 
			// this will prevent a silly loop of updating the moderation -> entry -> moderation ...

			return moderationPeer::doUpdateAllModerations($c, $new_status );
					 
		}
		elseif ( $object instanceof kshow)
		{
			throw new exception ( "TO BE IMPLEMENTED addToModerationList - for type kshow");
		}
		else
		{
			// ERROR !
			throw new exception ( "TO BE IMPLEMENTED addToModerationList - for type unknown type");
		}		 
	}
}
?>