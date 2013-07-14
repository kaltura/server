<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAkamaiProvisionJobData extends KalturaProvisionJobData
{
	/**
	 * @var string
	 */
	public $wsdlUsername;
	
	/**
	 * @var string
	 */
	public $wsdlPassword;
	
	/**
	 * @var string
	 */
	public $cpcode;
	
	/**
	 * @var string
	 */
	public $emailId;
	
	/**
	 * @var string
	 */
	public $primaryContact;
	
	/**
	 * @var string
	 */
	public $secondaryContact;
	
	
	private static $map_between_objects = array
	(
		"wsdlUsername",
		"wsdlPassword",
		"cpcode",
		"emailId",
		"primaryContact",
		"secondaryContact",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kAkamaiProvisionJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
	

}