<?php

/**
 * @package Core
 * @subpackage model.data
 */
class kExportToCsvOptions
{
	/**
	 * @var string
	 */
	public $format;
	/**
	 * @var KalturaEntryType
	 */
	public $type;

	/**
	 * @return string
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * @param string $format
	 */
	public function setFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * @return KalturaEntryType
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param KalturaEntryType $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
}
