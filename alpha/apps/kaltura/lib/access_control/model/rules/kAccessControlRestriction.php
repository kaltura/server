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
	 * 
	 * @var accessControl
	 */
	protected $accessControl;

	/**
	 * @param accessControl $accessControl
	 */
	public function __construct(accessControl $accessControl = null)
	{
		$scope = null;
		if($accessControl)
		{
			$this->accessControl = $accessControl;
			$scope = $accessControl->getScope();
		}
		parent::__construct($scope);
		$contexts = array(
			ContextType::PLAY, 
			ContextType::DOWNLOAD, 
		);
		$partnerId = $accessControl ? $accessControl->getPartnerId() : kCurrentContext::$ks_partner_id;
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if($partner) {
			if($partner->getRestrictThumbnailByKs())
				$contexts[] = ContextType::THUMBNAIL;
			if($partner->getShouldApplyAccessControlOnEntryMetadata())
				$contexts[] = ContextType::METADATA;
		}
			
		$this->setContexts($contexts);
	}
	
	/**
	 * @param accessControl $accessControl
	 */
	public function setAccessControl(accessControl $accessControl)
	{
		$this->accessControl = $accessControl;
	}

	/* (non-PHPdoc)
	 * @see kRule::applyContext()
	 */
	public function applyContext(kContextDataResult $context)
	{
		$fulfilled = parent::applyContext($context);

		if($fulfilled)
			foreach($this->actions as $action)
				if($action instanceof kAccessControlPreviewAction)
					$context->setPreviewLength($action->getLimit());
			
		return $fulfilled;
	}
	
	public function __sleep()
	{
		$vars = get_class_vars('kAccessControlRestriction');
		unset($vars['accessControl']);
		return array_keys($vars);
	}
}

