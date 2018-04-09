<?php
/**
 * @package plugins.conference
 * @subpackage api.filters
 */
class KalturaConferenceEntryServerNodeFilter extends KalturaConferenceEntryServerNodeBaseFilter
{
	public function __construct()
	{
		$this->serverTypeIn = array(ConferencePlugin::getCoreValue('EntryServerNodeType', ConferenceEntryServerNodeType::CONFERENCE_ENTRY_SERVER ));
	}
}
