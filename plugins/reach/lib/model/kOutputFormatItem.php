
<?php

/**
 * Catalog Item pricing calac definition
 *
 * @package Core
 * @subpackage model
 *
 */
class kOutputFormatItem
{
	/**
	 * @var int
	 */
	protected $outputFormat;
	
	/**
	 * @return the $language
	 */
	public function getOutputFormat()
	{
		return $this->outputFormat;
	}
	
	/**
	 * @param string $language
	 */
	public function setOutputFormat($outputFormat)
	{
		$this->outputFormat = $outputFormat;
	}
}