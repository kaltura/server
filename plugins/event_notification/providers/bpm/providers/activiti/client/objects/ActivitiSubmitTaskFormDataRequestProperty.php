<?php

require_once(__DIR__ . '/../ActivitiRequestObject.php');

	

class ActivitiSubmitTaskFormDataRequestProperty extends ActivitiRequestObject
{
	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $value;

	/**
	 * @param $id string
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @param $value string
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

}

