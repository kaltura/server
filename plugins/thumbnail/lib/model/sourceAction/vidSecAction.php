<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class vidSecAction extends sourceAction
{
	protected $second;
	protected $entrySource;

	protected $parameterAlias = array(
		"sec" => kThumbnailParameterName::SECOND,
		"s" => kThumbnailParameterName::SECOND,
	);

	protected function extractActionParameters()
	{
		$this->second = $this->getFloatActionParameter(kThumbnailParameterName::SECOND, 0);
		$this->entrySource = $this->getActionParameter(kThumbnailParameterName::SOURCE_ENTRY);
	}

	protected function validateInput()
	{
		if(!is_numeric($this->second) || $this->second < 0)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, "Vid sec second cant be negative");
		}

		$this->validatePermissions();
	}


	protected function validatePermissions()
	{
		$partner = PartnerPeer::retrieveByPK( kCurrentContext::getCurrentPartnerId());
		if ($partner->getEnabledService(PermissionName::FEATURE_BLOCK_THUMBNAIL_CAPTURE))
		{
			throw new KalturaAPIException(KExternalErrors::NOT_ALLOWED_PARAMETER);
		}

		if ($enableCacheValidation)
		{
			$actionList = $secureEntryHelper->getActionList(RuleActionType::LIMIT_THUMBNAIL_CAPTURE);
			if ($actionList)
				KExternalErrors::dieError(KExternalErrors::NOT_ALLOWED_PARAMETER);
		}
	}
	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		// TODO: Implement doAction() method.
	}
}