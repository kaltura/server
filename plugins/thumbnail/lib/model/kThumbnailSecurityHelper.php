<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kThumbnailSecurityHelper
{
	/**
	 * @param entry $entry
	 * @throws KalturaAPIException
	 */
	public static function verifyEntryAccess($entry)
	{
		$enableCacheValidation = true;
		$accessControl = $entry->getAccessControl();
		if ($accessControl)
		{
			/* @var accessControl $accessControl */
			$enableCacheValidation = $accessControl->hasRules(ContextType::THUMBNAIL, array(RuleActionType::BLOCK, RuleActionType::LIMIT_THUMBNAIL_CAPTURE));
		}

		if ($enableCacheValidation)
		{
			$secureEntryHelper = new KSecureEntryHelper($entry, kCurrentContext::$ks, self::getReferrer(), ContextType::THUMBNAIL);
			$secureEntryHelper->validateForPlay();
		}

		// not allow capturing frames if the partner has FEATURE_DISALLOW_FRAME_CAPTURE permission
		$partner = $entry->getPartner();
		if ($partner->getEnabledService(PermissionName::FEATURE_BLOCK_THUMBNAIL_CAPTURE))
		{
			throw new kThumbnailException(kThumbnailException::NOT_ALLOWED_PARAMETER, kThumbnailException::NOT_ALLOWED_PARAMETER);
		}

		if ($enableCacheValidation)
		{
			$actionList = $secureEntryHelper->getActionList(RuleActionType::LIMIT_THUMBNAIL_CAPTURE);
			if ($actionList)
			{
				throw new kThumbnailException(kThumbnailException::NOT_ALLOWED_PARAMETER, kThumbnailException::NOT_ALLOWED_PARAMETER);
			}
		}
	}

	//todo check if we need to add get referrer from url too
	public static function getReferrer()
	{
		return kApiCache::getHttpReferrer();
	}
}