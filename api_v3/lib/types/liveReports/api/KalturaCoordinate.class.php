<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaCoordinate extends KalturaObject
{	
	/**
	 * @var float
	 **/
	public $latitude;
	
	/**
	 * @var float
	 **/
	public $longitude;
	
	/**
	 * @var string
	 **/
	public $name;
	
	public function getWSObject() {
		$obj = new WSCoordinate();
		$obj->fromKalturaObject($this);
		return $obj;
	}
}


