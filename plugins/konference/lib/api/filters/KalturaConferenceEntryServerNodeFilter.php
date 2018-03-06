<?php
/**
 * @package plugins.konference
 * @subpackage api.filters
 */
class KalturaConferenceEntryServerNodeFilter extends KalturaConferenceEntryServerNodeBaseFilter
{
	public function __construct()
	{
		$this->typeIn = array(KonferencePlugin::getApiValue('ConferenceEntryServerNodeType'));
	}
}
