<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportGraphArray extends KalturaTypedArray
{
	public static function fromReportDataArray ( $arr, $delimiter = ',' )
	{
		$newArr = new KalturaReportGraphArray();
		foreach ( $arr as $id => $data )
		{
			$nObj = new KalturaReportGraph();
			$nObj->fromReportData ( $id, $data, $delimiter );
			$newArr[] = $nObj;
		}
			
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaReportGraph" );
	}
}
?>