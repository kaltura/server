<?php
/**
 * @package Core
 * @subpackage model.data
 */
class previewRestriction extends sessionRestriction
{
	/**
	 * Length in seconds to preview
	 * 
	 * @var int
	 */
	protected $previewLength;
	
	/**
	 * @param int $previewLength
	 */
	public function setPreviewLength($previewLength)
	{
		$this->previewLength = $previewLength;
	}
	
	/**
	 * @return int
	 */
	public function getPreviewLength()
	{
		return $this->previewLength;
	}
}