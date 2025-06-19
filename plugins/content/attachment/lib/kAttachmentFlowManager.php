<?php
/**
 * @package plugins.attachment
 * @subpackage lib
 */
class kAttachmentFlowManager implements kObjectDeletedEventConsumer, kObjectAddedEventConsumer
{

	private function indexEntry(BaseObject $object)
	{
		$entry = $object->getentry();
		if ($entry && $entry->getStatus() != entryStatus::DELETED)
		{
			$entry->setUpdatedAt(time());
			$entry->save();
			$entry->indexToSearchIndex();
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
			&& $object->getStatus() == AttachmentAsset::ASSET_STATUS_READY &&
			$object->getContainerFormat() == AttachmentType::MARKDOWN)
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
			&& AttachmentPlugin::isAllowedPartner($object->getPartnerId()) &&
			$object->getContainerFormat() == AttachmentType::MARKDOWN)
		{
			return true;
		}

		return false;
	}
}
