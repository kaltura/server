<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_MediaEntryFilterForPlaylist extends Kaltura_Client_Type_MediaEntryFilter
{
	public function getKalturaObjectType()
	{
		return 'KalturaMediaEntryFilterForPlaylist';
	}
	
	/**
	 * 
	 *
	 * @var int
	 */
	public $limit = null;


}

