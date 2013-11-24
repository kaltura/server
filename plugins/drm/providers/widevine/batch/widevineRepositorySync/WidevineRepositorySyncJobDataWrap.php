<?php

class WidevineRepositorySyncJobDataWrap
{
	private $data;
	
	public function __construct(KalturaWidevineRepositorySyncJobData $data)
	{
		$this->data = $data;
	}
	
	public function getWidevineAssetIds()
	{
		return explode(',', $this->data->wvAssetIds);
	}
	
	public function getLicenseStartDate()
	{
		$modifiedAttributes = explode(',', $this->data->modifiedAttributes);
		foreach ($modifiedAttributes as $attribute) 
		{
			$attrPair = explode(':', $attribute);
			if($attrPair[0] == 'licenseStartDate')
			{
				return $attrPair[1];
			}
		}
		return null;
	}
	
	public function getLicenseEndDate()
	{
		$modifiedAttributes = explode(',', $this->data->modifiedAttributes);
		foreach ($modifiedAttributes as $attribute) 
		{
			$attrPair = explode(':', $attribute);
			if($attrPair[0] == 'licenseEndDate')
			{
				return $attrPair[1];
			}
		}
		return null;
	}
	
	public function hasAssetId($wvAssetId)
	{
		if(!$wvAssetId)
			return false;
			
		return strstr($this->data->wvAssetIds, strval($wvAssetId));
	}
}