<?php
class Kaltura_FilterPaginatorWithPartnerLoader extends Kaltura_FilterPaginator
{
	public function __construct($impersonatedPartnerId/* $args*/)
	{
		parent::__construct("uiConf", "listAction", $impersonatedPartnerId);
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
		
		$filter = new KalturaPartnerFilter();
		$filter->idIn = implode(',', array_keys($partners));
		$client = Kaltura_ClientHelper::getClient();
		$partnersResponse = $client->systemPartner->listAction($filter);
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