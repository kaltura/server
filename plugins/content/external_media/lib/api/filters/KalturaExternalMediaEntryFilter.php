<?php
/**
 * @package plugins.externalMedia
 * @subpackage api.filters
 */
class KalturaExternalMediaEntryFilter extends KalturaExternalMediaEntryBaseFilter
{
	public function __construct()
	{
		$this->typeEqual = ExternalMediaPlugin::getEntryTypeCoreValue(ExternalMediaEntryType::EXTERNAL_MEDIA);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::toObject()
	 */
	public function toObject($coreFilter = null, $skip = array())
	{
		/* @var $coreFilter entryFilter */
		
		if($this->externalSourceTypeEqual)
		{
			$coreFilter->fields['_like_plugins_data'] = ExternalMediaPlugin::getExternalSourceSearchData($this->externalSourceTypeEqual);
			$this->externalSourceTypeEqual = null;
		}
	
		if($this->externalSourceTypeIn)
		{
			$coreExternalSourceTypes = array();
			$apiExternalSourceTypes = explode(',', $this->externalSourceTypeIn);
			foreach($apiExternalSourceTypes as $apiExternalSourceType)
			{
				$coreExternalSourceType = kPluginableEnumsManager::apiToCore('ExternalMediaSourceType', $apiExternalSourceType);
				$coreExternalSourceTypes[] = ExternalMediaPlugin::getExternalSourceSearchData($coreExternalSourceType);
			}
			$externalSourceTypeIn = implode(',', $coreExternalSourceTypes);
			
			$coreFilter->fields['_mlikeor_plugins_data'] = $externalSourceTypeIn;
			$this->externalSourceTypeIn = null;
		}
		
		return parent::toObject($coreFilter, $skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseEntryFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager);
		
	    $newList = KalturaExternalMediaEntryArray::fromDbArray($list, $responseProfile);
		$response = new KalturaExternalMediaEntryListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
}
