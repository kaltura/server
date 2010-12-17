<?php


/**
 * Skeleton subclass for representing a row from the 'permission_item' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class PermissionItem extends BasePermissionItem
{
	
	const ALL_VALUES_IDENTIFIER = '*'; // means that a certain parameter is not limited to a specific value - can be used in different places
	
	public function __construct()
	{
		$this->setType(get_class($this));
	}	
	
	public function getFromValue($name)
	{
		$curValue = $this->getValue();
		if (!isset($curValue[$name])) {
			return null;
		}
		return $curValue[$name];
	}
	
	public function setInValue($key, $value)
	{
		$curValue = $this->getValue();
		$curValue[$key] = $value;
		$this->setValue($curValue);
	}
	
	
	public function getValue()
	{
		$value = parent::getValue();
		if (!$value) {
			return array();
		}
		else {
			return unserialize($value);
		}
	}
	
	public function setValue($value_object)
	{
		if (!$value_object) {
			parent::setValue(null);
		}
		else {
			parent::setValue(serialize($value_object));
		}
	}
	
} // PermissionItem
