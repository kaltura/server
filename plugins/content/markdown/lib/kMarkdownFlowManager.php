<?php

/**
 * @package plugins.attachment
 * @subpackage lib
 */
class kMarkdownFlowManager implements kObjectDeletedEventConsumer, kObjectAddedEventConsumer
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
		if (class_exists('MarkdownAsset') && $object instanceof MarkdownAsset
			&& MarkdownPlugin::isAllowedPartner($object->getPartnerId())
			&& $object->getStatus() == MarkdownAsset::ASSET_STATUS_READY)
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
		if (class_exists('MarkdownAsset') && $object instanceof MarkdownAsset
			&& MarkdownPlugin::isAllowedPartner($object->getPartnerId()))
		{
			return true;
		}

		return false;
	}
}
