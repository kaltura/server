<?php

/**
 * @package plugins.scheduledTask
 * @subpackage model.data
 */
class kObjectTask
{
	/**
	 * The type of the object task
	 *
	 * @var int
	 */
	private $type;

	/**
	 * Key value array of the api object
	 * @var array
	 */
	private $data;

	public function __construct()
	{
		$this->data = array();
	}

	/**
	 * @param int $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function setDataValue($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getDataValue($key)
	{
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}
}