<?php
class KalturaRestrictionFactory
{
	static function getInstanceByDbObject(baseRestriction $dbObject)
	{
		$objectClass = get_class($dbObject);
		switch($objectClass)
		{
			case "siteRestriction":
				$obj = new KalturaSiteRestriction();
				break;
			case "countryRestriction":
				$obj = new KalturaCountryRestriction();
				break;
			case "sessionRestriction":
				$obj = new KalturaSessionRestriction();
				break;
			case "previewRestriction":
				$obj = new KalturaPreviewRestriction();
				break;
			case "directoryRestriction":
				$obj = new KalturaDirectoryRestriction();
				break;
			case "ipAddressRestriction":
				$obj = new KalturaIpAddressRestriction();
				break;
			default:
				$obj = new KalturaBaseRestriction();
				break;
		}
		return $obj;
	}
	
	static function getDbInstanceApiObject(KalturaBaseRestriction $apiObject)
	{
		$obj = null;
		
		$restrictionClass = get_class($apiObject);
		switch($restrictionClass)
		{
			case "KalturaSiteRestriction":
				$obj = new siteRestriction();
				break;
			case "KalturaCountryRestriction":
				$obj = new countryRestriction();
				break;
			case "KalturaPreviewRestriction":
				$obj = new previewRestriction();
				break;
			case "KalturaSessionRestriction":
				$obj = new sessionRestriction();
				break;
			case "KalturaDirectoryRestriction":
				$obj = new directoryRestriction();
				break;
			case "KalturaIpAddressRestriction":
				$obj = new ipAddressRestriction();
				break;
		}
		return $obj;
	}
}
?>