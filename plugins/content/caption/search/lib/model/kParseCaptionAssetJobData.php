<?php
/**
 * @package plugins.virusScan
 * @subpackage model.data
 */
class kParseCaptionAssetJobData extends kJobData
{
	/**
	 * @var string
	 */
	private $captionAssetId;
	
	/**
	 * @return string $captionAssetId
	 */
	public function getCaptionAssetId()
	{
		return $this->captionAssetId;
	}

	/**
	 * @param string $captionAssetId
	 */
	public function setCaptionAssetId($captionAssetId)
	{
		$this->captionAssetId = $captionAssetId;
	}
}
