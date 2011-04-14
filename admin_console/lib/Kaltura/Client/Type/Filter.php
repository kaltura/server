<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_Filter extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaFilter';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $orderBy = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_SearchItem
	 */
	public $advancedSearch;


}

