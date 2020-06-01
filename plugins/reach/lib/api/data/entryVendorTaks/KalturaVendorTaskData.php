<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
abstract class KalturaVendorTaskData extends KalturaObject implements IApiObjectFactory
{
	/* (non-PHPdoc)
 	 * @see IApiObjectFactory::getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile)
 	 */
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$taskDataType = get_class($sourceObject);
		$taskData = null;
		switch ($taskDataType)
		{
			case 'kAlignmentVendorTaskData':
				$taskData = new KalturaAlignmentVendorTaskData();
				break;

			case 'kTranslationVendorTaskData':
				$taskData = new KalturaTranslationVendorTaskData();
				break;
		}
		
		if ($taskData)
			/* @var $object KalturaVendorTaskData */
			$taskData->fromObject($sourceObject, $responseProfile);
		
		return $taskData;
	}

	protected function validateCaptionAsset($captionAssetId)
	{
		$captionAssetDb = assetPeer::retrieveById($captionAssetId);
		if (!$captionAssetDb || !($captionAssetDb instanceof CaptionAsset))
		{
			throw new KalturaAPIException(KalturaCaptionErrors::CAPTION_ASSET_ID_NOT_FOUND, $captionAssetId);
		}
	}
}
