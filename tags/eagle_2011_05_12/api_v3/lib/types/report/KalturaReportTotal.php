<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportTotal extends KalturaObject 
{
	/**
	 * @var string
	 */
	public $header;
	
	/**
	 * @var string
	 */
	public $data;
	
	
	public function fromReportTotal ( array $header , array $data )
	{
		$this->header = implode ( "," , $header );
		$this->data = implode ( "," , $data );
	}
	
}