<?php
/**
 * Define vendor translation task data object
 *
 * @package plugins.reach
 * @subpackage model
 *
 */

class kTranslationVendorTaskData extends kVendorTaskData
{
	/**
	 * @var string
	 */
	public $captionAssetId;

	/**
	 * @return string
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