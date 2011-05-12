<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportGraphArray extends KalturaTypedArray
{
	public static function fromReportDataArray ( $arr )
	{
		$newArr = new KalturaReportGraphArray();
		foreach ( $arr as $id => $data )
		{
			$nObj = new KalturaReportGraph();
			$nObj->fromReportData ( $id, $data );
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