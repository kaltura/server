<?php

/**
 * @package Core
 * @subpackage config
 */ 
class PartnerPackages
{
	const PARTNER_PACKAGE_FREE = 1;
	const PARTNER_PACKAGE_PAID = 2;
	
	const CLASS_OF_SERVICE_SILVER = 1;
	const CLASS_OF_SERVICE_GOLD = 2;
	const CLASS_OF_SERVICE_PLATINUM = 3;	
	
	const PACKAGE_SUPPORT_TYPE_NONE = 0;
	const PACKAGE_SUPPORT_TYPE_COMMUNITY = 1;
	const PACKAGE_SUPPORT_TYPE_TICKETS = 2;
	const PACKAGE_SUPPORT_TYPE_EMAIL = 4;
	const PACKAGE_SUPPORT_TYPE_PHONE = 8;
	
	const PACKAGE_CYCLE_TYPE_FOREVER = 0;
	const PACKAGE_CYCLE_TYPE_MONTH = 1;
	
	const KALTURA_PACKAGE_UPGRADE = 80;
	const KALTURA_LOCKED_PACKAGE_UPGRADE = 86;
	
	private $packages = null;
	private $packagesClassOfService = null;
	private $packagesVertical = null;
	
	public function PartnerPackages ()
	{
		$package_config = simplexml_load_file(dirname(__FILE__).'/../../config/partnerPackages.xml');
		$package_nodes = $package_config->xpath('/packages/package');
		foreach ($package_nodes as $package)
		{
			$arrPackage = $this->flatXml2arr($package);
			$this->packages[$arrPackage['id']] = $arrPackage;
		}
		
		$package_class_of_service_config = simplexml_load_file(dirname(__FILE__).'/../../config/partnerPackagesClassOfService.xml');
		$package_nodes = $package_class_of_service_config->xpath('/packages/package');
		foreach ($package_nodes as $package)
		{
			$arrPackage = $this->flatXml2arr($package);
			$this->packagesClassOfService[$arrPackage['id']] = $arrPackage;
		}
		
		$package_vertical = simplexml_load_file(dirname(__FILE__).'/../../config/partnerVertical.xml');
		$package_nodes = $package_vertical->xpath('/packages/package');
		foreach ($package_nodes as $package)
		{
			$arrPackage = $this->flatXml2arr($package);
			$this->packagesVertical[$arrPackage['id']] = $arrPackage;
		}
		
		
	}
	
	private function flatXml2arr($flatXml) 
	{
		$arr = array();
		$children = $flatXml->children();
		foreach ($children as $child => $value) {
			if (!$value->children())
			{
				list($key,$val) = each ($value);
				$arr[$child] = $val;
			} 
			else
			{
				$arr[$child] = $this->flatXml2arr($value);
			}
		}
		return $arr;
	}
	public function getPackageDetails ( $packageId )
	{
		return $this->packages[$packageId];
	}
	
	public function getPackageClassOfServiceDetails ( $packageId )
	{
		return $this->packagesClassOfService[$packageId];
	}
	
	public function listPackages ()
	{
		return $this->packages;
	}
	
	public function listPackagesClassOfService()
	{
		return $this->packagesClassOfService;
	}
	
	public function listPackagesVertical()
	{
		return $this->packagesVertical;
	}
	
	
}
