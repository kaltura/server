<?php
/**
 * @package plugins.sip
 * @subpackage api.filters
 */
class KalturaSipEntryServerNodeFilter extends KalturaSipEntryServerNodeBaseFilter
{
	public function __construct()
	{
		$this->serverTypeIn = SipPlugin::getApiValue(SipEntryServerNodeType::SIP_ENTRY_SERVER );
	}
}
