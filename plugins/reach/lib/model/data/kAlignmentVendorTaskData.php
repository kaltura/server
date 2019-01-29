
<?php

/**
 * Define vendor alignment dask data object
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kAlignmentVendorTaskData
{
	/**
	 * @var string
	 */
	public $transcriptAssetId;
	
	/**
	 * @return the $transcriptAssetId
	 */
	public function getTranscriptAssetId()
	{
		return $this->transcriptAssetId;
	}

	/**
	 * @param int $transcriptAssetId
	 */
	public function setTranscriptAssetId($transcriptAssetId)
	{
		$this->transcriptAssetId = $transcriptAssetId;
	}
}
