
<?php

/**
 * Catalog Item pricing calac definition
 *
 * @package plugins.reach
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
	 * @return the $outputFormat
	 */
	public function getOutputFormat()
	{
		return $this->outputFormat;
	}
	
	/**
	 * @param int $outputFormat
	 */
	public function setOutputFormat($outputFormat)
	{
		$this->outputFormat = $outputFormat;
	}
}