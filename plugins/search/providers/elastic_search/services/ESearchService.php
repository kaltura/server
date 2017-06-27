<?php
/**
 * @service eSearch
 * @package plugins.elasticSearch
 * @subpackage api.services
 */
class ESearchService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		// init service and action name
		$this->serviceId = $serviceId;
		$this->serviceName = $serviceName;
		$this->actionName  = $actionName;

		// impersonated partner = partner parameter from the request
		$this->impersonatedPartnerId = kCurrentContext::$partner_id;

		$this->ks = kCurrentContext::$ks_object ? kCurrentContext::$ks_object : null;

		// operating partner = partner from the request or the ks partner
		$partnerId = kCurrentContext::getCurrentPartnerId();

		// if there is no session, assume it's partner 0 using actions that doesn't require ks
		if(is_null($partnerId))
			$partnerId = 0;

		$this->partnerId = $partnerId;

		// check if current aciton is allowed and if private partner data access is allowed
		$allowPrivatePartnerData = false;
		$actionPermitted = true;
		//$actionPermitted = $this->isPermitted($allowPrivatePartnerData);

		// action not permitted at all, not even kaltura network
		if (!$actionPermitted)
		{
			$e = new KalturaAPIException ( APIErrors::SERVICE_FORBIDDEN, $this->serviceId.'->'.$this->actionName); //TODO: should sometimes thorow MISSING_KS instead
			header("X-Kaltura:error-".$e->getCode());
			header("X-Kaltura-App: exiting on error ".$e->getCode()." - ".$e->getMessage());
			throw $e;
		}

		//$this->validateApiAccessControl();

		// init partner filter parameters
		$this->private_partner_data = $allowPrivatePartnerData;
		$this->partnerGroup = kPermissionManager::getPartnerGroup($this->serviceId, $this->actionName);

		//if ($this->globalPartnerAllowed($this->actionName)) {
		//	$this->partnerGroup = PartnerPeer::GLOBAL_PARTNER.','.trim($this->partnerGroup,',');
		//}

		$this->setPartnerFilters($partnerId);

		kCurrentContext::$HTMLPurifierBehaviour = $this->getPartner()->getHtmlPurifierBehaviour();
		kCurrentContext::$HTMLPurifierBaseListOnlyUsage = $this->getPartner()->getHtmlPurifierBaseListUsage();
	}

	/**
	 *
	 * @action search
	 * @param KalturaESearchOperator $searchOperator
	 * @param string $entryStatuses
	 * @return KalturaESearchResultArray
	 */
	function searchAction (KalturaESearchOperator $searchOperator, $entryStatuses = null)
	{
		if (!$searchOperator->operator)
			$searchOperator->operator = KalturaSearchOperatorType::SEARCH_AND;
		//TODO: should we allow doesnt contain without a specific contains
		$coreSearchOperator = $searchOperator->toObject();
		/**
		 * @var ESearchOperator $coreSearchOperator
		 */
		$entryStatusesArr = array();
		if (!empty($entryStatuses))
			$entryStatusesArr = explode(',', $entryStatuses);
		$entrySearch = new kEntrySearch();
		$elasticResults = $entrySearch->doSearch($coreSearchOperator, $entryStatusesArr);//TODO: handle error flow
		$coreResults = elasticSearchUtils::transformElasticToObject($elasticResults);

		return KalturaESearchResultArray::fromDbArray($coreResults);
	}


	/**
	 *
	 * @action getAllowedSearchTypes
	 * @param KalturaESearchItem $searchItem
	 * @param string $fieldName
	 * @return KalturaKeyValueArray
	 * @throws KalturaAPIException
	 */
	function getAllowedSearchTypesAction (KalturaESearchItem $searchItem)
	{
		$coreSearchItem = $searchItem->toObject();
		$coreSearchItemClass = get_class($coreSearchItem);
		$allowedSearchMap = $coreSearchItemClass::getAallowedSearchTypesForField();

		$result = new KalturaKeyValueArray();
		if (isset($searchItem->fieldName))
		{
			foreach ($allowedSearchMap[$coreSearchItem->getFieldName()] as $searchTypeName => $searchTypeVal)
			{
				$currVal = new KalturaKeyValue();
				$currVal->key = $searchTypeName;
				$currVal->value = $searchTypeVal;
				$result[] = $currVal;
			}
		}
		return $result;
	}


}


?>


