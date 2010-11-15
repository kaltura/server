<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaJobData extends KalturaObject
{
	/**
	 * Extended by job data objects to reflect plugin enum values 
	 * 
	 * @param string $subType
	 * @return int
	 */
	public function toSubType($subType)
	{
		return $subType;
	}
	
	/**
	 * Extended by job data objects to reflect plugin enum values
	 * 
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		return $subType;
	}
}
