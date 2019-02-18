<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportTable extends KalturaObject 
{
	/**
	 * @var string
	 * @readonly
	 */
	public $header;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $data;
	
	
	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
	
	public function fromReportTable (  $header ,  $data , $totalCount, $delimiter )
	{
		if ( ! $header ) return;
		$this->header = implode ( $delimiter , $header );
		
		$data_str = "";
		foreach ( $data as $row )
		{
			$row = str_replace ( $delimiter , " " , $row ); // TODO - escape the separatos
			$row = str_replace ( ";" , " " , $row ); // TODO - escape the separatos
			$data_str .= implode ( $delimiter , $row ) . ";";
		}
		
		$this->data = $data_str;
		
		$this->totalCount = $totalCount;
	}
	
	
}