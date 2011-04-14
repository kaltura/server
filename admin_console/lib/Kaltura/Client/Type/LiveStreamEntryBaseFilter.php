<?php
/**
 * @package Admin
 * @subpackage Client
 */
abstract class Kaltura_Client_Type_LiveStreamEntryBaseFilter extends Kaltura_Client_Type_MediaEntryFilter
{
	public function getKalturaObjectType()
	{
		return 'KalturaLiveStreamEntryBaseFilter';
	}
	

}

