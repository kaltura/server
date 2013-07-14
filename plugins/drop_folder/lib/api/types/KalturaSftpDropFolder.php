<?php

/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaSftpDropFolder extends KalturaSshDropFolder
{
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new SftpDropFolder();
			
		parent::toObject($dbObject, $skip);
					
		return $dbObject;
	}
}