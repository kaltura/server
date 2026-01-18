<?php

/**
 * @package plugins.attachment
 * @subpackage lib
 */
class kAttachmentFlowManager implements kObjectDeletedEventConsumer, kObjectAddedEventConsumer, kObjectChangedEventConsumer
{

	private function indexEntry(BaseObject $object)
	{
		$entry = $object->getentry();
		if ($entry && $entry->getStatus() != entryStatus::DELETED)
		{
			$entry->setUpdatedAt(time());
			$entry->save();
			$entry->indexToElastic();
		}

		return true;
	}


	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		return $this->indexEntry($object);
	}

	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if (class_exists('AttachmentAsset') && $object instanceof AttachmentAsset
			&& AttachmentPlugin::isAllowedPartner($object->getPartnerId())
			&& $object->getStatus() == AttachmentAsset::ASSET_STATUS_READY)
		{
			return true;
		}

		return false;
	}

	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		return $this->indexEntry($object);
	}

	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if (class_exists('AttachmentAsset') && $object instanceof AttachmentAsset
			&& AttachmentPlugin::isAllowedPartner($object->getPartnerId()))
		{
			return true;
		}

		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		return $this->indexEntry($object);
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	*/
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if (class_exists('AttachmentAsset') && $object instanceof AttachmentAsset
			&& AttachmentPlugin::isAllowedPartner($object->getPartnerId())
			&& in_array(assetPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == AttachmentAsset::ASSET_STATUS_READY)
		{
			return true;
		}

		return false;
	}

}
