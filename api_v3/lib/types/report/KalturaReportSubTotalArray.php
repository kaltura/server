<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportSubTotalArray extends KalturaTypedArray
{
	public static function fromReportDataArray ( $arr )
	{
		$newArr = new KalturaReportSubTotalArray();
		foreach ( $arr as $id => $data )
		{
			$nObj = new KalturaReportSubTotal();
			$nObj->fromReportData ( $id, $data );
			$newArr[] = $nObj;
		}
			
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaReportSubTotal" );
	}
}
?>