<?php

/**
 * Subclass for representing a row from the 'moderation_flag' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class moderationFlag extends BasemoderationFlag
{
	public function getPuserId()
	{
		$kuser = $this->getkuserRelatedByKuserId();
		if ($kuser)
			return $kuser->getPuserId();
		else
			return null;
	}
	
	public function getFlaggedPuserId()
	{
		$flaggedKuser = $this->getkuserRelatedByFlaggedKuserId();
		if ($flaggedKuser)
			return $flaggedKuser->getPuserId();
		else
			return null;
	}
}
