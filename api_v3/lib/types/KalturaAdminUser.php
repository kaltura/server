<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAdminUser extends KalturaObject 
{
	/**
	 * @var string
	 * @readonly
	 */
	public $password;

	/**
	 * @var string
	 * @readonly
	 */
	public $email;
	
	/**
	 * @var string
	 */
	public $screenName;
	
	private static $map_between_objects = array
	(
		"email" , "screenName" 
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function fromAdminUser ( kuser $entry )
	{
		parent::fromObject( $partner );
	}
	
	public function toAdminUser () 
	{
		$user = new kuser;
		return parent::toObject( $user );
	}

}
?>