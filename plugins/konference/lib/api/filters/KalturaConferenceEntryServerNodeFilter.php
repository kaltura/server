<?php
/**
 * @package plugins.konference
 * @subpackage api.filters
 */
class KalturaConferenceEntryServerNodeFilter extends KalturaConferenceEntryServerNodeBaseFilter
{
	public function __construct()
	{
		$this->serverTypeIn = array(KonferencePlugin::getCoreValue('ConferenceEntryServerNodeType'));
	}
}
