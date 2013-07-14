<?php
/**
 * @package plugins.captionSphinx
 * @subpackage lib
 */
class kSphinxCaptionAssetFlowManager implements kObjectDeletedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof CaptionAssetItem)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		$sphinxSearchManager = new kSphinxSearchManager();
		$sphinxSearchManager->deleteFromSphinx($object);
		
		return true;
	}
}
