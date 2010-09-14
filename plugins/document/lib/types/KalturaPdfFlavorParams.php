<?php
class KalturaPdfFlavorParams extends KalturaFlavorParams 
{
	
	private static $map_between_objects = array
	(
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}