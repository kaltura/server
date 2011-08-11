<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPartnerArray extends KalturaTypedArray
{
	public static function fromPartnerArray(array $arr)
	{
		$newArr = new KalturaPartnerArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaPartner();
			$nObj->fromPartner($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaPartner" );
	}
}
?>