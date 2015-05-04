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
	 * @var bool
	 */
	private $stopProcessingOnError;

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
	 * @return bool
	 */
	public function getStopProcessingOnError()
	{
		return $this->stopProcessingOnError;
	}

	/**
	 * @param bool $stopProcessingOnError
	 */
	public function setStopProcessingOnError($stopProcessingOnError)
	{
		$this->stopProcessingOnError = $stopProcessingOnError;
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