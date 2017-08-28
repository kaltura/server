<?php

/**
 * @package plugins.beacon
 * @subpackage api.filters
 */
class KalturaBeaconEnhanceFilter extends KalturaFilter
{
	/**
	 * @var string
	 */
	public $externalElasticQueryObject;
	
	public function getCoreFilter()
	{
		return null;
	}
	
	public function enhanceSearch()
	{
		$utf8Query = utf8_encode($this->externalElasticQueryObject);
		$extraElasticQuery = json_decode($utf8Query, true);
		$extraElasticQuery['index'] = kBeacon::ELASTIC_BEACONS_INDEX_NAME;
		
		$searchMgr = new kBeaconSearchManger();
		$responseArray = $searchMgr->search($extraElasticQuery);
		$responseArray = $searchMgr->getHitsFromElasticResponse($responseArray);
		
		$response = new KalturaBeaconListResponse();
		$response->objects = KalturaBeaconArray::fromDbArray($responseArray);
		return $response;
	}
}