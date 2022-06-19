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
	protected $format;
	
	/**
	 * @var entryType
	 */
	protected $typeEqual;
	
	/**
	 * @var boolean
	 */
	protected $defaultHeader;
	
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
	 * @return entryType
	 */
	public function getTypeEqual()
	{
		return $this->typeEqual;
	}
	
	/**
	 * @param entryType $typeEqual
	 */
	public function setTypeEqual($typeEqual)
	{
		$this->typeEqual = $typeEqual;
	}
	
	/**
	 * @return boolean
	 */
	public function getDefaultHeader()
	{
		return $this->defaultHeader;
	}
	
	/**
	 * @param boolean $defaultHeader
	 */
	public function setDefaultHeader($defaultHeader)
	{
		$this->defaultHeader = $defaultHeader;
	}
}