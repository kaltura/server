<?php
class kShortLinkFlowManager implements kObjectDeletedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object)
	{
		if($object instanceof kuser)
		{
			$shortLinks = ShortLinkPeer::retrieveByKuserId($object->getId());
			foreach($shortLinks as $shortLink)
			{
				$shortLink->setStatus(ShortLinkStatus::DELETED);
				$shortLink->save();
			}
		}
	}
}