<?php
class kShortLinkFlowManager implements kObjectDeletedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof kuser)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		$shortLinks = ShortLinkPeer::retrieveByKuserId($object->getId(), $object->getPartnerId());
		foreach($shortLinks as $shortLink)
		{
			$shortLink->setStatus(ShortLinkStatus::DELETED);
			$shortLink->save();
		}
		
		return true;
	}
}