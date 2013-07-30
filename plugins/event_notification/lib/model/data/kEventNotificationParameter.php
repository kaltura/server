<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class kEventNotificationParameter
{
	/**
	 * The key to be replaced in the content
	 * @var string
	 */
	protected $key;

	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * The value that replace the key 
	 * @var kStringValue
	 */
	protected $value;
	
	/**
	 * @return the $key
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @return kStringValue $value
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string $description
	 */
	public function getDescription() 
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) 
	{
		$this->description = $description;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	/**
	 * @param kStringValue $value
	 */
	public function setValue(kStringValue $value)
	{
		$this->value = $value;
	}
}