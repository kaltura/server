<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class kEventNotificationArrayParameter extends kEventNotificationParameter
{
	/**
	 * @var array
	 */
	protected $values;
	
	/**
	 * Used to restrict the values to close list
	 * @var array<kStringValue>
	 */
	protected $allowedValues;

	/* (non-PHPdoc)
	 * @see kEventNotificationParameter::getValue()
	 */
	public function getValue()
	{
		if(!$this->values)
			return null;
			
		$value = new kStringValue();
		$value->setValue(implode(',', $this->values));
		
		return $value;
	}
	
	/**
	 * @return array $values
	 */
	public function getValues()
	{
		return $this->values;
	}

	/**
	 * @return array $allowedValues
	 */
	public function getAllowedValues()
	{
		return $this->allowedValues;
	}

	/**
	 * @param array $values
	 */
	public function setValues($values)
	{
		$this->values = $values;
	}

	/**
	 * @param array $allowedValues
	 */
	public function setAllowedValues($allowedValues)
	{
		$this->allowedValues = $allowedValues;
	}
}