<?php
/**
 * @package plugins.conference
 * @subpackage api.filters
 */
class KalturaConferenceEntryServerNodeFilter extends KalturaConferenceEntryServerNodeBaseFilter
{
	public function __construct()
	{
		$this->serverTypeIn = ConferencePlugin::getApiValue(ConferenceEntryServerNodeType::CONFERENCE_ENTRY_SERVER );
	}
}
