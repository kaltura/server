<?php
class Infra_FilterPaginatorWithPartnerLoader extends Infra_FilterPaginator
{
	public function __construct($impersonatedPartnerId/* $args*/)
	{
		$client = Infra_ClientHelper::getClient();
		parent::__construct($client->uiConf, "listAction", $impersonatedPartnerId);
		$this->args = array_slice(func_get_args(), 1);
	}
	
	/**
	 * 
	 * @param int $offset
	 * @param int $itemCountPerPage
	 */
	protected function callService($offset, $itemCountPerPage)
	{
		$objects = parent::callService($offset, $itemCountPerPage);
		$partners = array();
		foreach($objects as $object)
			$partners[$object->partnerId] = null;
		
		$filter = new Kaltura_Client_Type_PartnerFilter();
		$filter->idIn = implode(',', array_keys($partners));
		$client = Infra_ClientHelper::getClient();
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		$partnersResponse = $systemPartnerPlugin->systemPartner->listAction($filter);
		foreach($partnersResponse->objects as $partner)
			$partners[$partner->id] = $partner;
			
		foreach($objects as $object)
		{
			if (!is_null($partners[$object->partnerId]))
				$object->partner = $partners[$object->partnerId];
		}
		return $objects;
	}
}