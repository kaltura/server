<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_BaseEntryFilter extends Kaltura_Client_Type_BaseEntryBaseFilter
{
	public function getKalturaObjectType()
	{
		return 'KalturaBaseEntryFilter';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $freeText = null;


}

