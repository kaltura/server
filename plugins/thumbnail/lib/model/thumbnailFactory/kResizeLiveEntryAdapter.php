<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kResizeLiveEntryAdapter extends kBaseResizeAdapter
{
	/**
	 * @return string
	 * @throws kThumbnailException
	 */
	public function resize()
	{
		$entry = $this->parameters->get(kThumbFactoryFieldName::ENTRY);
		$dc = myEntryUtils::getLiveEntryDcId($entry->getRootEntryId(), EntryServerNodeType::LIVE_PRIMARY);
		if($dc == null)
		{
			KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_LIVE);
		}
		
		if ($dc != kDataCenterMgr::getCurrentDcId ())
		{
			kFileUtils::dumpApiRequest(kDataCenterMgr::getRemoteDcExternalUrlByDcId($dc));
		}

		return parent::resize();
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