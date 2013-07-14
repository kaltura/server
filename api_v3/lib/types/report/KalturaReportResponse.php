<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportResponse extends KalturaObject 
{
	/**
	 * @var string
	 */
	public $columns;
	
	/**
	 * @var KalturaStringArray
	 */
	public $results;
	
	public static function fromColumnsAndRows($columns, $rows)
	{
		$reportResponse = new KalturaReportResponse();
		$reportResponse->columns = implode(',', $columns);
		$reportResponse->results = new KalturaStringArray();
		foreach($rows as $row)
		{
			// we are using comma as a seperator, so don't allow it in results
			foreach($row as &$tempColumnData)
				$tempColumnData = str_replace(',', '', $tempColumnData);
				
			$string = new KalturaString();
			$string->value = implode(',', $row);
			$reportResponse->results[] = $string;
		}
		return $reportResponse;
	}
}