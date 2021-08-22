
<?php

/**
 * Define vendor alignment dask data object
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kAlignmentVendorTaskData extends kGenericVendorTaskData
{
	/**
	 * @var string
	 */
	public $textTranscriptAssetId;
	
	/**
	 * @var string
	 */
	public $jsonTranscriptAssetId;
	
	/**
	 * @var string
	 */
	public $captionAssetId;
	
	/**
	 * @return the $textTranscriptAssetId
	 */
	public function getTextTranscriptAssetId()
	{
		return $this->textTranscriptAssetId;
	}
	
	/**
	 * @return the $jsonTranscriptAssetId
	 */
	public function getJsonTranscriptAssetId()
	{
		return $this->jsonTranscriptAssetId;
	}
	
	/**
	 * @return the $captionAssetId
	 */
	public function getCaptionAssetId()
	{
		return $this->captionAssetId;
	}

	
	/**
	 * @param int $textTranscriptAssetId
	 */
	public function setTextTranscriptAssetId($textTranscriptAssetId)
	{
		$this->textTranscriptAssetId = $textTranscriptAssetId;
	}
	
	/**
	 * @param int $jsonTranscriptAssetId
	 */
	public function setJsonTranscriptAssetId($jsonTranscriptAssetId)
	{
		$this->jsonTranscriptAssetId = $jsonTranscriptAssetId;
	}
	
	/**
	 * @param int $captionAssetId
	 */
	public function setCaptionAssetId($captionAssetId)
	{
		$this->captionAssetId = $captionAssetId;
	}
}
