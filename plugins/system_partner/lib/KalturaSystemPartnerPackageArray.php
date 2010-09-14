<?php
class KalturaSystemPartnerPackageArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaSystemPartnerPackage");
	}
	
	public function fromArray($arr)
	{
		foreach($arr as $item)
		{
			$obj = new KalturaSystemPartnerPackage();
			$obj->fromArray($item);
			$this[] = $obj;
		}
	}
}