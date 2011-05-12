<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportGraph extends KalturaObject 
{
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $data;
	
	
	public function fromReportData ( $id , array $dataArr )
	{
		$this->id = $id;
		$str = "";
		foreach ( $dataArr as $x => $y )
		{
			$str .= "$x,$y;";
		}
		
		$this->data = $str;
		
		return $this;
	}
}