<?php
class kAssetPropertiesManager implements kObjectChangedEventConsumer
{

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof asset)
		{
			if (in_array(assetPeer::CUSTOM_DATA, $modifiedColumns) &&
				($object->isCustomDataModified('language') || $object->isCustomDataModified('label')))
				return true;
		}
		return false;
	}


	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		/* @var $object Asset */
		$entry = entryPeer::retrieveByPK($object->getEntryId());
		if ($entry)
		{
			$entry->setCacheFlavorVersion($entry->getCacheFlavorVersion() + 1);
			$entry->save();
		}
		return true;
	}

}