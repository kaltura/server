<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportBaseTotalArray extends KalturaTypedArray
{
	public static function fromReportDataArray ( $arr )
	{
		$newArr = new KalturaReportBaseTotalArray();
		foreach ( $arr as $id => $data )
		{
			$nObj = new KalturaReportBaseTotal();
			$nObj->fromReportData ( $id, $data );
			$newArr[] = $nObj;
		}
			
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaReportBaseTotal" );
	}
}
?>