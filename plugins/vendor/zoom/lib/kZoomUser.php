<?php
/**
 * @package plugins.vendor
 * @subpackage model.zoom
 */

class kZoomUser
{
	protected $originalName;
	protected $processedName;

	/**
	 * @param string $originalName
	 */
	public function setOriginalName($originalName)
	{
		$this->originalName = $originalName;
	}

	/**
	 * @param string $processedName
	 */
	public function setProcessedName($processedName)
	{
		$this->processedName = $processedName;
	}

	/**
	 * @return string
	 */
	public function getOriginalName()
	{
		return $this->originalName;
	}

	/**
	 * @return string
	 */
	public function getProcessedName()
	{
		return $this->processedName;
	}
}