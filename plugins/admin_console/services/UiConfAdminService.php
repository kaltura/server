<?php
/**
 * UiConf Admin service
 *
 * @service uiConfAdmin
 * @package plugins.adminConsole
 * @subpackage api.services
 */
class UiConfAdminService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if(!AdminConsolePlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	
	/**
	 * Adds new UIConf with no partner limitation
	 * 
	 * @action add
	 * @param KalturaUiConfAdmin $uiConf
	 * @return KalturaUiConfAdmin
	 */
	function addAction(KalturaUiConfAdmin $uiConf)
	{
		// if not specified set to true (default)
		if(is_null($uiConf->useCdn))
			$uiConf->useCdn = true;
		
		$dbUiConf = $uiConf->toObject(new uiConf());
		$dbUiConf->save();
		
		$uiConf = new KalturaUiConfAdmin();
		$uiConf->fromUiConf($dbUiConf);
		
		return $uiConf;
	}

	/**
	 * Update an existing UIConf with no partner limitation
	 * 
	 * @action update
	 * @param int $id 
	 * @param KalturaUiConfAdmin $uiConf
	 * @return KalturaUiConfAdmin
	 *
	 * @throws APIErrors::INVALID_UI_CONF_ID
	 */	
	function updateAction($id, KalturaUiConfAdmin $uiConf)
	{
		$dbUiConf = uiConfPeer::retrieveByPK( $id );
		
		if (!$dbUiConf)
			throw new KalturaAPIException ( APIErrors::INVALID_UI_CONF_ID , $id );
		
		$dbUiConf = $uiConf->toObject($dbUiConf);
		$dbUiConf->save();
		
		$uiConf = new KalturaUiConfAdmin();
		$uiConf->fromObject($dbUiConf);
		
		return $uiConf;
	}
	
	/**
	 * Retrieve a UIConf by id with no partner limitation
	 * 
	 * @action get
	 * @param int $id 
	 * @return KalturaUiConfAdmin
	 *
	 * @throws APIErrors::INVALID_UI_CONF_ID
	 */		
	function getAction($id)
	{
		$dbUiConf = uiConfPeer::retrieveByPK($id);
		
		if (!$dbUiConf)
			throw new KalturaAPIException(APIErrors::INVALID_UI_CONF_ID, $id);
			
		$uiConf = new KalturaUiConfAdmin();
		$uiConf->fromObject($dbUiConf);
		
		return $uiConf;
	}
	
	/**
	 * Delete an existing UIConf with no partner limitation
	 * 
	 * @action delete
	 * @param int $id
	 *
	 * @throws APIErrors::INVALID_UI_CONF_ID
	 */		
	function deleteAction($id)
	{
		$dbUiConf = uiConfPeer::retrieveByPK($id);
		
		if (!$dbUiConf)
			throw new KalturaAPIException(APIErrors::INVALID_UI_CONF_ID, $id);
		
		$dbUiConf->setStatus(uiConf::UI_CONF_STATUS_DELETED);
		$dbUiConf->save();
	}
	
	/**
	 * Retrieve a list of available UIConfs  with no partner limitation
	 * 
	 * @action list
	 * @param KalturaUiConfFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaUiConfAdminListResponse
	 */		
	function listAction( KalturaUiConfFilter $filter = null , KalturaFilterPager $pager = null)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		if (!$filter)
			$filter = new KalturaUiConfFilter();
			
		$uiConfFilter = new uiConfFilter();
		$filter->toObject($uiConfFilter);
		
		$c = new Criteria();
		$uiConfFilter->attachToCriteria($c);
		$count = uiConfPeer::doCount($c);
		if ($pager)
			$pager->attachToCriteria($c);
		$list = uiConfPeer::doSelect($c);
		
		$newList = KalturaUiConfAdminArray::fromUiConfAdminArray($list);
		
		$response = new KalturaUiConfAdminListResponse();
		$response->objects = $newList;
		$response->totalCount = $count;
		
		return $response;
	}
}
