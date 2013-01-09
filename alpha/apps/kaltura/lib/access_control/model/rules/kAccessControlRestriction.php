<?php
/**
 * @package Core
 * @subpackage model.data
 * @abstract
 * @deprecated
 * 
 * Old restriction for backward compatibility
 */
abstract class kAccessControlRestriction extends kRule
{
	const RESTRICTION_TYPE_RESTRICT_LIST = 0;
	const RESTRICTION_TYPE_ALLOW_LIST = 1;

	/**
	 * @param accessControl $accessControl
	 */
	public function __construct(accessControl $accessControl = null)
	{
		parent::__construct($accessControl);
		$contexts = array(
			accessControlContextType::PLAY, 
			accessControlContextType::DOWNLOAD, 
		);
		$partnerId = $accessControl ? $accessControl->getPartnerId() : kCurrentContext::$ks_partner_id;
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if($partner) {
			if($partner->getRestrictThumbnailByKs())
				$contexts[] = accessControlContextType::THUMBNAIL;
			if($partner->getShouldApplyAccessControlOnEntryMetadata())
				$contexts[] = accessControlContextType::METADATA;
		}
			
		$this->setContexts($contexts);
	}

	/* (non-PHPdoc)
	 * @see kRule::applyContext()
	 */
	public function applyContext(kEntryContextDataResult $context)
	{
		$fulfilled = parent::applyContext($context);

		if($fulfilled)
			foreach($this->actions as $action)
				if($action instanceof kAccessControlPreviewAction)
					$context->setPreviewLength($action->getLimit());
			
		return $fulfilled;
	}
}

