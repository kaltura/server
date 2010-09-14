<?php
class directoryRestriction extends baseRestriction
{
	const DIRECTORY_RESTRICTION_TYPE_DONT_DISPLAY = 0;
	const DIRECTORY_RESTRICTION_TYPE_DISPLAY_WITH_LINK = 1;
	
	/**
	 * @var string
	 */
	protected $type;
	
	/**
	 * @param string $type
	 */
	function setType($type)
	{
		$this->type = $type;
	}
	
	/**
	 * @return string
	 */
	function getType()
	{
		return $this->type;	
	}
	
	/**
	 * @return bool
	 */
	function isValid()
	{
		// not implemented yet
		return true;
	}
}