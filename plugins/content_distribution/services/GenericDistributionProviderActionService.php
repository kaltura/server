<?php
/**
 * Generic Distribution Provider Actions service
 *
 * @service genericDistributionProviderAction
 */
class GenericDistributionProviderActionService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		myPartnerUtils::addPartnerToCriteria(new GenericDistributionProviderActionPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!ContentDistributionPlugin::isAllowedPartner(kCurrentContext::$master_partner_id))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN);
	}
	
	/**
	 * Add new Generic Distribution Provider Action
	 * 
	 * @action add
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction
	 * @return KalturaGenericDistributionProviderAction
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
	 */
	function addAction(KalturaGenericDistributionProviderAction $genericDistributionProviderAction)
	{
		$genericDistributionProviderAction->validatePropertyNotNull("genericDistributionProviderId");
		
		$dbGenericDistributionProvider = GenericDistributionProviderPeer::retrieveByPK($genericDistributionProviderAction->genericDistributionProviderId);
		if (!$dbGenericDistributionProvider)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $genericDistributionProviderAction->genericDistributionProviderId);
			
		$dbGenericDistributionProviderAction = new GenericDistributionProviderAction();
		$genericDistributionProviderAction->toInsertableObject($dbGenericDistributionProviderAction);
		$dbGenericDistributionProviderAction->setPartnerId($dbGenericDistributionProvider->getPartnerId());			
		$dbGenericDistributionProviderAction->setStatus(GenericDistributionProviderStatus::ACTIVE);
		$dbGenericDistributionProviderAction->save();
		
		$genericDistributionProviderAction = new KalturaGenericDistributionProviderAction();
		$genericDistributionProviderAction->fromObject($dbGenericDistributionProviderAction);
		return $genericDistributionProviderAction;
	}

	
	/**
	 * Add MRSS transform file to generic distribution provider action
	 * 
	 * @action addMrssTransform
	 * @param int $id the id of the generic distribution provider action
	 * @param string $xslData XSL MRSS transformation data
	 * @return KalturaGenericDistributionProviderAction
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 */
	function addMrssTransformAction($id, $xslData)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $id);
			
		$dbGenericDistributionProviderAction->incrementMrssTransformerVersion();
		$dbGenericDistributionProviderAction->save();
		
		$key = $dbGenericDistributionProviderAction->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_TRANSFORMER);
		kFileSyncUtils::file_put_contents($key, $xslData);
		
		$genericDistributionProviderAction = new KalturaGenericDistributionProviderAction();
		$genericDistributionProviderAction->fromObject($dbGenericDistributionProviderAction);
		return $genericDistributionProviderAction;
	}

	
	/**
	 * Add MRSS transform file to generic distribution provider action
	 * 
	 * @action addMrssTransformFromFile
	 * @param int $id the id of the generic distribution provider action
	 * @param file $xslFile XSL MRSS transformation file
	 * @return KalturaGenericDistributionProviderAction
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND
	 */
	function addMrssTransformFromFileAction($id, $xslFile)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $id);
			
		$filePath = $xslFile['tmp_name'];
		if(!file_exists($filePath))
			throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND, $xslFile['name']);
			
		$dbGenericDistributionProviderAction->incrementMrssTransformerVersion();
		$dbGenericDistributionProviderAction->save();
		
		$key = $dbGenericDistributionProviderAction->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_TRANSFORMER);
		kFileSyncUtils::moveFromFile($filePath, $key);
		
		$genericDistributionProviderAction = new KalturaGenericDistributionProviderAction();
		$genericDistributionProviderAction->fromObject($dbGenericDistributionProviderAction);
		return $genericDistributionProviderAction;
	}

	
	/**
	 * Add MRSS validate file to generic distribution provider action
	 * 
	 * @action addMrssValidate
	 * @param int $id the id of the generic distribution provider action
	 * @param string $xsdData XSD MRSS validatation data
	 * @return KalturaGenericDistributionProviderAction
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 */
	function addMrssValidateAction($id, $xsdData)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $id);
			
		$dbGenericDistributionProviderAction->incrementMrssValidatorVersion();
		$dbGenericDistributionProviderAction->save();
		
		$key = $dbGenericDistributionProviderAction->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_VALIDATOR);
		kFileSyncUtils::file_put_contents($key, $xsdData);
		
		$genericDistributionProviderAction = new KalturaGenericDistributionProviderAction();
		$genericDistributionProviderAction->fromObject($dbGenericDistributionProviderAction);
		return $genericDistributionProviderAction;
	}

	
	/**
	 * Add MRSS validate file to generic distribution provider action
	 * 
	 * @action addMrssValidateFromFile
	 * @param int $id the id of the generic distribution provider action
	 * @param file $xsdFile XSD MRSS validatation file
	 * @return KalturaGenericDistributionProviderAction
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND
	 */
	function addMrssValidateFromFileAction($id, $xsdFile)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $id);
			
		$filePath = $xsdFile['tmp_name'];
		if(!file_exists($filePath))
			throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND, $xsdFile['name']);
			
		$dbGenericDistributionProviderAction->incrementMrssValidatorVersion();
		$dbGenericDistributionProviderAction->save();
		
		$key = $dbGenericDistributionProviderAction->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_VALIDATOR);
		kFileSyncUtils::moveFromFile($filePath, $key);
		
		$genericDistributionProviderAction = new KalturaGenericDistributionProviderAction();
		$genericDistributionProviderAction->fromObject($dbGenericDistributionProviderAction);
		return $genericDistributionProviderAction;
	}

	
	/**
	 * Add results transform file to generic distribution provider action
	 * 
	 * @action addResultsTransform
	 * @param int $id the id of the generic distribution provider action
	 * @param string $transformData transformation data xsl, xPath or regex
	 * @return KalturaGenericDistributionProviderAction
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 */
	function addResultsTransformAction($id, $transformData)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $id);
			
		$dbGenericDistributionProviderAction->incrementResultsTransformerVersion();
		$dbGenericDistributionProviderAction->save();
		
		$key = $dbGenericDistributionProviderAction->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_RESULTS_TRANSFORMER);
		kFileSyncUtils::file_put_contents($key, $transformData);
		
		$genericDistributionProviderAction = new KalturaGenericDistributionProviderAction();
		$genericDistributionProviderAction->fromObject($dbGenericDistributionProviderAction);
		return $genericDistributionProviderAction;
	}

	
	/**
	 * Add MRSS transform file to generic distribution provider action
	 * 
	 * @action addResultsTransformFromFile
	 * @param int $id the id of the generic distribution provider action
	 * @param file $transformFile transformation file xsl, xPath or regex
	 * @return KalturaGenericDistributionProviderAction
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND
	 */
	function addResultsTransformFromFileAction($id, $transformFile)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $id);
			
		$filePath = $transformFile['tmp_name'];
		if(!file_exists($filePath))
			throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND, $transformFile['name']);
			
		$dbGenericDistributionProviderAction->incrementResultsTransformerVersion();
		$dbGenericDistributionProviderAction->save();
		
		$key = $dbGenericDistributionProviderAction->getSyncKey(GenericDistributionProviderAction::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_RESULTS_TRANSFORMER);
		kFileSyncUtils::moveFromFile($filePath, $key);
		
		$genericDistributionProviderAction = new KalturaGenericDistributionProviderAction();
		$genericDistributionProviderAction->fromObject($dbGenericDistributionProviderAction);
		return $genericDistributionProviderAction;
	}
	
	
	/**
	 * Get Generic Distribution Provider Action by id
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaGenericDistributionProviderAction
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $id);
			
		$genericDistributionProviderAction = new KalturaGenericDistributionProviderAction();
		$genericDistributionProviderAction->fromObject($dbGenericDistributionProviderAction);
		return $genericDistributionProviderAction;
	}
	
	
	/**
	 * Get Generic Distribution Provider Action by provider id
	 * 
	 * @action getByProviderId
	 * @param int $genericDistributionProviderId
	 * @param KalturaDistributionAction $actionType
	 * @return KalturaGenericDistributionProviderAction
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 */
	function getByProviderIdAction($genericDistributionProviderId, $actionType)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($genericDistributionProviderId, $actionType);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $genericDistributionProviderId);
	
		$genericDistributionProviderAction = new KalturaGenericDistributionProviderAction();
		$genericDistributionProviderAction->fromObject($dbGenericDistributionProviderAction);
		return $genericDistributionProviderAction;
	}
	
	/**
	 * Update Generic Distribution Provider Action by provider id
	 * 
	 * @action updateByProviderId
	 * @param int $genericDistributionProviderId
	 * @param KalturaDistributionAction $actionType
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction
	 * @return KalturaGenericDistributionProviderAction
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 */
	function updateByProviderIdAction($genericDistributionProviderId, $actionType, KalturaGenericDistributionProviderAction $genericDistributionProviderAction)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($genericDistributionProviderId, $actionType);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $genericDistributionProviderId);
	
		$genericDistributionProviderAction->toUpdatableObject($dbGenericDistributionProviderAction);
		$dbGenericDistributionProviderAction->save();
		
		$genericDistributionProviderAction = new KalturaGenericDistributionProviderAction();
		$genericDistributionProviderAction->fromObject($dbGenericDistributionProviderAction);
		return $genericDistributionProviderAction;
	}
	
	/**
	 * Update Generic Distribution Provider Action by id
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction
	 * @return KalturaGenericDistributionProviderAction
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 */
	function updateAction($id, KalturaGenericDistributionProviderAction $genericDistributionProviderAction)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $id);
		
		$genericDistributionProviderAction->toUpdatableObject($dbGenericDistributionProviderAction);
		$dbGenericDistributionProviderAction->save();
		
		$genericDistributionProviderAction = new KalturaGenericDistributionProviderAction();
		$genericDistributionProviderAction->fromObject($dbGenericDistributionProviderAction);
		return $genericDistributionProviderAction;
	}
	
	/**
	 * Delete Generic Distribution Provider Action by id
	 * 
	 * @action delete
	 * @param int $id
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 */
	function deleteAction($id)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByPK($id);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $id);

		$dbGenericDistributionProviderAction->setStatus(GenericDistributionProviderStatus::DELETED);
		$dbGenericDistributionProviderAction->save();
	}
	
	/**
	 * Delete Generic Distribution Provider Action by provider id
	 * 
	 * @action deleteByProviderId
	 * @param int $genericDistributionProviderId
	 * @param KalturaDistributionAction $actionType
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND
	 */
	function deleteByProviderIdAction($genericDistributionProviderId, $actionType)
	{
		$dbGenericDistributionProviderAction = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($genericDistributionProviderId, $actionType);
		if (!$dbGenericDistributionProviderAction)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND, $genericDistributionProviderId);

		$dbGenericDistributionProviderAction->setStatus(GenericDistributionProviderStatus::DELETED);
		$dbGenericDistributionProviderAction->save();
	}
	
	
	/**
	 * List all distribution providers
	 * 
	 * @action list
	 * @param KalturaGenericDistributionProviderActionFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaGenericDistributionProviderActionListResponse
	 */
	function listAction(KalturaGenericDistributionProviderActionFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaGenericDistributionProviderActionFilter();
			
		$c = new Criteria();
		$genericDistributionProviderActionFilter = new GenericDistributionProviderActionFilter();
		$filter->toObject($genericDistributionProviderActionFilter);
		
		$genericDistributionProviderActionFilter->attachToCriteria($c);
		$count = GenericDistributionProviderActionPeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = GenericDistributionProviderActionPeer::doSelect($c);
		
		$response = new KalturaGenericDistributionProviderActionListResponse();
		$response->objects = KalturaGenericDistributionProviderActionArray::fromDbArray($list);
		$response->totalCount = $count;
	
		return $response;
	}	
}
