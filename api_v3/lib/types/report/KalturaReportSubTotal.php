<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportSubTotal extends KalturaObject 
{
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $data;
	
	
	public function fromReportData ( $id , $data )
	{
		$this->id = $id;
		$this->data = $data;
		
		return $this;
	}
}