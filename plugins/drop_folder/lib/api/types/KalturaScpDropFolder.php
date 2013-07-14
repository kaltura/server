<?php

/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaScpDropFolder extends KalturaSshDropFolder
{
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new ScpDropFolder();
			
		parent::toObject($dbObject, $skip);
					
		return $dbObject;
	}
}