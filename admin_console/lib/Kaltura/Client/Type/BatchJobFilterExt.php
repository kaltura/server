<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_BatchJobFilterExt extends Kaltura_Client_Type_BatchJobFilter
{
	public function getKalturaObjectType()
	{
		return 'KalturaBatchJobFilterExt';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $jobTypeAndSubTypeIn = null;


}

