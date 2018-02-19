<?php
/**
 * @package plugins.konference
 * @subpackage model
 */
class LiveConferenceEntry extends LiveEntry
{
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ConferenceEntryType::CONFERENCE);
	}

	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
		$this->setStatus(entryStatus::READY);
		$this->save();
	}


}
