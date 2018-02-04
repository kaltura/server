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
		$this->setStatus(entryStatus::NO_CONTENT);
	}
}
