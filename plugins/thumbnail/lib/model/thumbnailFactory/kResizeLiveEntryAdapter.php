<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kResizeLiveEntryAdapter extends kBaseResizeAdapter
{
	public function resizeEntryImage($params)
	{
		$entry = $this->parameters->get(kThumbFactoryFieldName::ENTRY);
		$dc = myEntryUtils::getLiveEntryDcId($entry->getRootEntryId(), EntryServerNodeType::LIVE_PRIMARY);
		if ($dc != kDataCenterMgr::getCurrentDcId ())
		{
			kFileUtils::dumpApiRequest(kDataCenterMgr::getRemoteDcExternalUrlByDcId($dc));
		}

		return parent::resizeEntryImage($params);
	}

	protected function getEntryLengthInMS()
	{
		$entry = $this->parameters->get(kThumbFactoryFieldName::ENTRY);
		return $entry->getRecordedLengthInMsecs();
	}

	protected function calculateThumbNamePostfix()
	{
		if($this->parameters->get(kThumbFactoryFieldName::VID_SLICES) > 0)
		{
			$this->thumbName .= '_duration_' . $this->getEntryLengthInMS();
		}

		parent::calculateThumbNamePostfix();
	}

	protected function initOrigImagePath()
	{
		$this->parameters->set(kThumbFactoryFieldName::ORIG_IMAGE_PATH, null);
	}
}