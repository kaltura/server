<?php

class WSCoordinate extends WSBaseObject
{	
	function getKalturaObject() {
		return new KalturaCoordinate();
	}
				
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
	
}


