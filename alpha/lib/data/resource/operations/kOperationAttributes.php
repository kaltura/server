<?php
/**
 * Base class to all operation attributes types
 *
 * @package Core
 * @subpackage model.data
 */
abstract class kOperationAttributes 
{
	abstract public function toArray();
	
	abstract public function getApiType();

	/**
	 * Return enum value from EntrySourceType
	 */
	public function getSourceType()
	{
		return null;
	}
}